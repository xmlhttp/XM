#include "usmart.h"
#include "usart.h"
#include "lwip_comm.h" 
#include "sys.h"
#include "malloc.h"
#include "tcp_client_demo.h"
#include "usart2.h"	
#include "usart3.h"	
#include "beep.h"
#include "ADC.h"
#include "stmflash.h"

extern int TIM5NUM;
extern u8 islink;
extern float WDIS;
extern u8 Is485;
//flash数据
extern struct InitData  my_data;

#if USMART_ENTIMX_SCAN==1
//复位runtime
//需要根据所移植到的MCU的定时器参数进行修改
void usmart_reset_runtime(void)
{
	TIM_ClearFlag(TIM4,TIM_FLAG_Update);//清除中断标志位 
	TIM_SetAutoreload(TIM4,0XFFFF);//将重装载值设置到最大
	TIM_SetCounter(TIM4,0);		//清空定时器的CNT
	usmart_dev.runtime=0;	
}
//获得runtime时间
//返回值:执行时间,单位:0.1ms,最大延时时间为定时器CNT值的2倍*0.1ms
//需要根据所移植到的MCU的定时器参数进行修改
u32 usmart_get_runtime(void)
{
	if(TIM_GetFlagStatus(TIM4,TIM_FLAG_Update)==SET)//在运行期间,产生了定时器溢出
	{
		usmart_dev.runtime+=0XFFFF;
	}
	usmart_dev.runtime+=TIM_GetCounter(TIM4);
	return usmart_dev.runtime;		//返回计数值
}
//下面这两个函数,非USMART函数,放到这里,仅仅方便移植. 
//定时器4中断服务程序	 
void TIM4_IRQHandler(void)
{ 		    		  			    
	if(TIM_GetITStatus(TIM4,TIM_IT_Update)==SET)//溢出中断
	{
		usmart_dev.scan();	//执行usmart扫描	
		TIM_SetCounter(TIM4,0);		//清空定时器的CNT
		TIM_SetAutoreload(TIM4,100);//恢复原来的设置		    				   				     	    	
	}				   
	TIM_ClearITPendingBit(TIM4,TIM_IT_Update);  //清除中断标志位    
}
//使能定时器4,使能中断.
void Timer4_Init(u16 arr,u16 psc)
{
    TIM_TimeBaseInitTypeDef  TIM_TimeBaseStructure;
	NVIC_InitTypeDef NVIC_InitStructure;

	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM4, ENABLE); //TIM4时钟使能 
 
	//TIM4初始化设置
 	TIM_TimeBaseStructure.TIM_Period = arr; //设置在下一个更新事件装入活动的自动重装载寄存器周期的值	 计数到5000为500ms
	TIM_TimeBaseStructure.TIM_Prescaler =psc; //设置用来作为TIMx时钟频率除数的预分频值  10Khz的计数频率  
	TIM_TimeBaseStructure.TIM_ClockDivision = 0; //设置时钟分割:TDTS = Tck_tim
	TIM_TimeBaseStructure.TIM_CounterMode = TIM_CounterMode_Up;  //TIM向上计数模式
	TIM_TimeBaseInit(TIM4, &TIM_TimeBaseStructure); //根据TIM_TimeBaseInitStruct中指定的参数初始化TIMx的时间基数单位
 
	TIM_ITConfig( TIM4, TIM_IT_Update|TIM_IT_Trigger, ENABLE );//TIM4 允许更新，触发中断

	//TIM4中断分组配置
	NVIC_InitStructure.NVIC_IRQChannel = TIM4_IRQn;  //TIM3中断
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 3;  //先占优先级03级
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;  //从优先级3级
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE; //IRQ通道被使能
	NVIC_Init(&NVIC_InitStructure);  //根据NVIC_InitStruct中指定的参数初始化外设NVIC寄存器

	TIM_Cmd(TIM4, ENABLE);  //使能TIM4							 
}
#endif
////////////////////////////////////////////////////////////////////////////////////////
//初始化串口控制器
//sysclk:系统时钟（Mhz）
void usmart_init(u8 sysclk)
{
#if USMART_ENTIMX_SCAN==1
	Timer4_Init(1000,(u32)sysclk*100-1);//分频,时钟为10K ,100ms中断一次,注意,计数频率必须为10Khz,以和runtime单位(0.1ms)同步.
#endif
	usmart_dev.sptype=1;	//十六进制显示参数
}		
//从str中获取函数名,id,及参数信息
//*str:字符串指针.
//返回值:0,识别成功;其他,错误代码.
u8 usmart_cmd_rec(u8*str) 
{
	u8 sta,i,rval;//状态	 
	u8 rpnum,spnum;
	u8 rfname[MAX_FNAME_LEN];//暂存空间,用于存放接收到的函数名  
	u8 sfname[MAX_FNAME_LEN];//存放本地函数名
	sta=usmart_get_fname(str,rfname,&rpnum,&rval);//得到接收到的数据的函数名及参数个数	  
	if(sta)return sta;//错误
	for(i=0;i<usmart_dev.fnum;i++)
	{
		sta=usmart_get_fname((u8*)usmart_dev.funs[i].name,sfname,&spnum,&rval);//得到本地函数名及参数个数
		if(sta)return sta;//本地解析有误	  
		if(usmart_strcmp(sfname,rfname)==0)//相等
		{
			if(spnum>rpnum)return USMART_PARMERR;//参数错误(输入参数比源函数参数少)
			usmart_dev.id=i;//记录函数ID.
			break;//跳出.
		}	
	}
	if(i==usmart_dev.fnum)return USMART_NOFUNCFIND;	//未找到匹配的函数
 	sta=usmart_get_fparam(str,&i);					//得到函数参数个数	
	if(sta)return sta;								//返回错误
	usmart_dev.pnum=i;								//参数个数记录
    return USMART_OK;
}
//usamrt执行函数
//该函数用于最终执行从串口收到的有效函数.
//最多支持10个参数的函数,更多的参数支持也很容易实现.不过用的很少.一般5个左右的参数的函数已经很少见了.
//该函数会在串口打印执行情况.以:"函数名(参数1，参数2...参数N)=返回值".的形式打印.
//当所执行的函数没有返回值的时候,所打印的返回值是一个无意义的数据.
void usmart_exe(void)
{
	u8 id;
//	u32 res;		   
//	u32 temp[MAX_PARM];//参数转换,使之支持了字符串 
	u8 sfname[MAX_FNAME_LEN];//存放本地函数名
	u8 pnum,rval;
	id=usmart_dev.id;
	if(id>=usmart_dev.fnum)return;//不执行.
	usmart_get_fname((u8*)usmart_dev.funs[id].name,sfname,&pnum,&rval);//得到本地函数名,及参数个数 
	printf("\r\n运行：%s",sfname);//输出正要执行的函数名
	usmart_reset_runtime();	//计时器清零,开始计时
	switch(usmart_dev.pnum)
	{
		case 0://无参数(void类型)											  
			(*(u32(*)())usmart_dev.funs[id].func)();
			break;
	}
	printf("*************************执行完成**********************\r\n");
}
//usmart扫描函数
//通过调用该函数,实现usmart的各个控制.该函数需要每隔一定时间被调用一次
//以及时执行从串口发过来的各个函数.
//本函数可以在中断里面调用,从而实现自动管理.

void usmart_scan(void)
{
	u8 sta,len;  
	if(USART_RX_STA&0x8000)//串口接收完成？
	{					   
		len=USART_RX_STA&0x3fff;	//得到此次接收到的数据长度
		USART_RX_BUF[len]='\0';	//在末尾加入结束符. 
		sta=usmart_dev.cmd_rec(USART_RX_BUF);//得到函数各个信息
		if(sta==0)usmart_dev.exe();	//执行函数 
		else 
		{  
			len=0;//usmart_sys_cmd_exe(USART_RX_BUF);
			if(len!=USMART_FUNCERR)sta=len;
			if(sta)
			{
				switch(sta)
				{
					case USMART_FUNCERR:
					//	printf("函数错误!\r\n");   			
						break;	
					case USMART_PARMERR:
					//	printf("参数错误!\r\n");   			
						break;				
					case USMART_PARMOVER:
					//	printf("参数太多!\r\n");   			
						break;		
					case USMART_NOFUNCFIND:
					//	printf("未找到匹配的函数!\r\n");   			
						break;		
				}
			}
		}
		USART_RX_STA=0;//状态寄存器清空	    
	}
}

#if USMART_USE_WRFUNS==1 	//如果使能了读写操作
//读取指定地址的值		 
u32 read_addr(u32 addr)
{
	return *(u32*)addr;//	
}
//在指定地址写入指定的值		 
void write_addr(u32 addr,u32 val)
{
	*(u32*)addr=val; 	
}

//获取ip
void getip(){
	printf("\r\n---------------------获取IP地址---------------------\r\n");
   	printf("MAC地址:......................%d.%d.%d.%d.%d.%d\r\n",lwipdev.mac[0],lwipdev.mac[1],lwipdev.mac[2],lwipdev.mac[3],lwipdev.mac[4],lwipdev.mac[5]);
	printf("IP地址........................%d.%d.%d.%d\r\n",lwipdev.ip[0],lwipdev.ip[1],lwipdev.ip[2],lwipdev.ip[3]);
	printf("子网掩码......................%d.%d.%d.%d\r\n",lwipdev.netmask[0],lwipdev.netmask[1],lwipdev.netmask[2],lwipdev.netmask[3]);
	printf("网关..........................%d.%d.%d.%d\r\n",lwipdev.gateway[0],lwipdev.gateway[1],lwipdev.gateway[2],lwipdev.gateway[3]);
	
}
//获取基本信息
void getinfo(){
	printf("\r\n----------------------版权信息----------------------\r\n");
	printf("欢迎使用劲驰网络交流单枪核心充电板 版权所有 # 2017 #\r\n");
	printf("技术支持：http://www.vmuui.com \r\n");
	printf("联系QQ：568615539   电话：13829719806\r\n");
	printf("网络连接：");
	if(islink){
		printf("断线-1 ");	
	}else{
		printf("连接-0 ");	
	}
	printf(" 电表状态：");
	if(Is485){
		printf("正常-1 ");
	}else{
		printf("异常-0 ");
	}
	printf(" ADC采集：%f V\r\n",getAD()*3.3/4096);
	printf("板卡内存使用率：%d%%  心跳间隔：%d秒  距离:%.2f cm\r\n",my_mem_perused(SRAMIN),TIM5NUM,WDIS);
	printf("电表读数:%.2fKW*H  电压：%.2fV  电流：%.3fA\r\n",(float)my_data.Endp/10,(float)my_data.Cvol/100,(float)my_data.Cele/1000);	
}
//获取GPIO状态
void getgpio(){
	printf("\r\n----------------------获取GPIO状态----------------------\r\n");
	printf("输入：\r\nPC1的电平是：%d ********充电接触器反馈，低电平有效\r\nPC2的电平是：%d ********CC连接线，低电平有效\r\nPE4的电平是：%d ********急停开关，低电平有效\r\nPE3的电平是：%d ********备用\r\n输出：\r\nPC0的电平是：%d ********充电位，高电平为充电\r\nPC6的电平是：%d ********连接指示灯，低电平为连接\r\nPC8的电平是：%d ********备用\r\nPC10的电平是：%d *******故障指示灯，低电平有故障\r\n",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2),GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4),GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_3),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_6),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_8),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_10));
}


#endif













