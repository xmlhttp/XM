#include "delay.h"
#include "tcp_client_demo.h"
#include "lwip/opt.h"
#include "lwip_comm.h"
#include "lwip/lwip_sys.h"
#include "lwip/api.h"
#include "includes.h"
#include "ADC.h"
#include "pwm.h"
#include "beep.h"
#include "cJSON.h"  
#include "http.h"
#include "stmflash.h"  
struct netconn *tcp_clientconn;					//TCP CLIENT网络连接结构体
u8 tcp_client_recvbuf[TCP_CLIENT_RX_BUFSIZE];	//TCP客户端接收数据缓冲区
char tcp_client_sendbuf[256];					//发送给服务端字符数组
u8 tcp_client_flag;								//TCP客户端数据发送标志位
//flash数据	stmflash.h
extern struct InitData  my_data;
//版本号stmflash.h
extern u8 Ver;
					
extern int DISTYPE;				  				//测距模块状态
extern u8 Is485;								//电表标识符
int TIM5NUM;									//TIM5计数器累计中断次数,和是否使用

u8 IsTran=0;									//是否为充停过渡期
//u8 istim5;										//TIM5是否处于定时状态,目前还不知道如何获取定时器使能状态
u8 islink=1;								   	//是否重连平台或者说是否已经连接平台
u8 IsSend;										//是否允许发送数据完成
u8 IsDown=0;									//是否允许下载，系统启动成功后会更改该字段

//TCP客户端任务
#define TCPCLIENT_PRIO		4
//任务堆栈大小
#define TCPCLIENT_STK_SIZE	300
//任务堆栈
OS_STK TCPCLIENT_TASK_STK[TCPCLIENT_STK_SIZE];

void any(u8*d);									//注册服务器下发数据处理函数
//tcp客户端任务函数
static void tcp_client_thread(void *arg)
{
	OS_CPU_SR cpu_sr;
	u32 data_len = 0;
	struct pbuf *q;
	err_t err,recv_err;
	static ip_addr_t server_ipaddr;
	static u16_t 		 server_port;
//	istim5=0;
	LWIP_UNUSED_ARG(arg);
	if((*(u8*)(FLASH_ADDR))==170){
		 server_port = my_data.Port;
	}else{
		 server_port = REMOTE_PORT;
	}
	

	IP4_ADDR(&server_ipaddr, lwipdev.remoteip[0],lwipdev.remoteip[1], lwipdev.remoteip[2],lwipdev.remoteip[3]);
	
	while (1) 
	{
		tcp_clientconn = netconn_new(NETCONN_TCP);  //创建一个TCP链接
		err = netconn_connect(tcp_clientconn,&server_ipaddr,server_port);//连接服务器
		//等待连接建立完成
		delay_ms(1000);
		if(err != ERR_OK){//返回值不等于ERR_OK,删除tcp_clientconn连接  
			netconn_delete(tcp_clientconn);
			delay_ms(1000);
		}else if (err == ERR_OK){    //处理新连接的数据
			struct netbuf *recvbuf;
			//不重置网卡、重置定时开启定时器计数
			islink=0;
			TIM5NUM=0;
		/*	if(istim5==0){
				istim5=1;
		//		printf("中断清零#1\r\n");
				TIM5NUM=0;
				
			} */
			tcp_clientconn->recv_timeout = 10;
			IsSend=0;
			memset(tcp_client_sendbuf,'\0',256);
		//	netconn_getaddr(tcp_clientconn,&loca_ipaddr,&loca_port,1); //获取本地IP主机IP地址和端口号
		//	printf("连接上服务器%d.%d.%d.%d,本机端口号为:%d\r\n",lwipdev.remoteip[0],lwipdev.remoteip[1], lwipdev.remoteip[2],lwipdev.remoteip[3],loca_port);
		  	/**
			登录，站点ID,站点密码，桩名，w v a 充电状态	t设备类型 c版本号
			my_data.Sid,my_data.Spwd,my_data.Vname,my_data.Endp,my_data.Cvol,my_data.Cele,my_data.Ispower,Ver
			*/

			sprintf(tcp_client_sendbuf,"{\"type\":\"login\",\"sid\":%d,\"pwd\":\"%s\",\"pname\":\"%s\",\"w\":%d,\"v\":%d,\"a\":%d,\"Ispower\":%d,\"Cpower\":%d,\"Orderid\":%d,\"Isend\":%d,\"t\":1,\"c\":%d}\r\n",my_data.Sid,my_data.Spwd,my_data.Vname,my_data.Endp,my_data.Cvol,my_data.Cele,my_data.Ispower,my_data.Cpower,my_data.Orderid,my_data.Isend,Ver);
			if(my_data.Isend==0&&my_data.Ispower==0){
				my_data.Isend=1;
				my_data.Orderid=0;
				my_data.Money=0;
				my_data.Uint=0;
				my_data.Cpower=0;
				//存储数据
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			delay_ms(10);
			
			tcp_client_flag |= LWIP_SEND_DATA;
			//连上后提交车位状态
			DISTYPE=3;
			while(1){
				if((tcp_client_flag & LWIP_SEND_DATA) == LWIP_SEND_DATA) //有数据要发送
				{
					err = netconn_write(tcp_clientconn ,tcp_client_sendbuf,strlen((char*)tcp_client_sendbuf),NETCONN_COPY); //发送tcp_server_sentbuf中的数据
					if(err != ERR_OK)
					{
						printf("发送失败：%s\r\n",tcp_client_sendbuf);
					}else{
					//	printf("中断清零#2\r\n"); 
						printf("发送数据：%s",tcp_client_sendbuf); 
						//由于超声波干扰，清零可能过快
						TIM5NUM=0;	
					}
					tcp_client_flag &= ~LWIP_SEND_DATA;
					
					IsSend=1;
				}

				if((recv_err = netconn_recv(tcp_clientconn,&recvbuf)) == ERR_OK)  //接收到数据
				{	
					OS_ENTER_CRITICAL(); //关中断
					memset(tcp_client_recvbuf,0,TCP_CLIENT_RX_BUFSIZE);  //数据接收缓冲区清零
					for(q=recvbuf->p;q!=NULL;q=q->next)  //遍历完整个pbuf链表
					{
						//判断要拷贝到TCP_CLIENT_RX_BUFSIZE中的数据是否大于TCP_CLIENT_RX_BUFSIZE的剩余空间，如果大于
						//的话就只拷贝TCP_CLIENT_RX_BUFSIZE中剩余长度的数据，否则的话就拷贝所有的数据
						if(q->len > (TCP_CLIENT_RX_BUFSIZE-data_len)) memcpy(tcp_client_recvbuf+data_len,q->payload,(TCP_CLIENT_RX_BUFSIZE-data_len));//拷贝数据
						else memcpy(tcp_client_recvbuf+data_len,q->payload,q->len);
						data_len += q->len;  	
						if(data_len > TCP_CLIENT_RX_BUFSIZE) break; //超出TCP客户端接收数组,跳出	
					}
					OS_EXIT_CRITICAL();  //开中断
					data_len=0;  //复制完成后data_len要清零。					
				//	printf("%s\r\n",tcp_client_recvbuf);
				//	printf("中断清零#3\r\n");
					TIM5NUM=0;
					any(tcp_client_recvbuf);
					netbuf_delete(recvbuf);
				}else if(recv_err == ERR_CLSD){  //关闭连接
					netconn_delete(tcp_clientconn);
					islink=1;
					printf("服务器%d.%d.%d.%d断开连接#111\r\n",lwipdev.remoteip[0],lwipdev.remoteip[1], lwipdev.remoteip[2],lwipdev.remoteip[3]);
					delay_ms(500);
					break;
				}
				//超时后重连
				if(islink==1){
					netconn_delete(tcp_clientconn);
					printf("服务器%d.%d.%d.%d断开连接#2\r\n",lwipdev.remoteip[0],lwipdev.remoteip[1], lwipdev.remoteip[2],lwipdev.remoteip[3]);
					delay_ms(500);
					break;	
				}
			}
		}
	}
}

//创建TCP客户端线程
//返回值:0 TCP客户端创建成功
//		其他 TCP客户端创建失败
INT8U tcp_client_init(void)
{
	INT8U res;
	OS_CPU_SR cpu_sr;
	
	OS_ENTER_CRITICAL();	//关中断
	res = OSTaskCreate(tcp_client_thread,(void*)0,(OS_STK*)&TCPCLIENT_TASK_STK[TCPCLIENT_STK_SIZE-1],TCPCLIENT_PRIO); //创建TCP客户端线程
	OS_EXIT_CRITICAL();		//开中断
	
	return res;
}
//开始充电
int StartChage(){
	//PC0位充电位，PC1充电检查位，PC2 CC连线位，
	//PC0未充电，PC2为低已插枪，CP信号检测	
/*	printf("当11前AD值：%u \r\n",adcnum);
	printf("PC0的值：%d",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_SET);
	printf("PC2的值：%d",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_RESET);
	printf("ADC的值：%d,%d",adcnum>2854&&adcnum<3104,adcnum>3351&&adcnum<3600);	 */
	

	u32 adcnum;																				//采集数据
	u16 t=0;																				//采集次数
	OSTaskSuspend(7);																	   	
	while(IsTran);																			//保证不在过渡期
	IsTran=1; 																				//标识位过渡状态			
	if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)!=Bit_RESET){									//CC线不为低表示未连接
		printf("CC线未连接，电平是：%d\r\n",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2));
		IsTran=0;
		OSTaskResume(7);
		return 20;
	}	 
	//低电平为充电 PC0不为高表示在充电，PC1为充电检测位，始终与PC0反向 暂时不处理PC1
	if(my_data.Ispower==1){																		//桩的标志位处于充电中
		printf("桩正在充电（标识位检测）#2\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 21;
	}
	if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_SET){									//桩的输出信号为高
		printf("桩正在充电（输出检测）#1\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 21;
	} 
	//if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_RESET){									//桩的输入信号为底 继电器反馈
	//	printf("桩正在充电（输入检测）#2\r\n");
	//	IsTran=0;
	//	OSTaskResume(7);
	//	return 22;
	//}
	if(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_RESET){									//急停开关被按下
		printf("急停被按下\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 23;
	}
	if(Is485==0){
		printf("电表掉线\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 28;
	}


	adcnum= getAD();
	
	if(!(adcnum>2483&&adcnum<3650)){ 														//AD采集非6-9V之间
		printf("第一次采集电压：%d V\r\n",adcnum);
		IsTran=0;
		OSTaskResume(7);
		return 24;
	}
	
	TIM_SetCompare2(TIM3,(*(u16*)(FLASH_ADDR+198))*0.999);									//发送PWM信号
	delay_ms(5);
	adcnum= getAD(); 
	while((!(adcnum>2483&&adcnum<3202))){													//发送PWM信号后要求10秒内降压到6V
		t++;
		
		if(t>500){
			break;
		}else{ 
			//printf("采集第：%d 次----",t);
			adcnum= getAD();
			//printf("采集第完成:%d\r\n",adcnum);
			delay_ms(20);		
		} 

	}

	if(t>500){
		printf("执行超时，采集电压：%d V\r\n",adcnum);																				//车系统不允许充电停发
		TIM_SetCompare2(TIM3,1000);	 
		IsTran=0;
		OSTaskResume(7);
		return 25;
	}
	adcnum= getAD();																		//再次采集后判断
	printf("最后采集电压：%d V\r\n",adcnum);
	//最后将所有条件都判断一次,PC0充电位为低，PC1充电反馈器为高，PC2 CC线为低，PE4急停为高，AD采集为6V区间
	//if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_RESET&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_SET&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_RESET&&GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_SET&&(adcnum>2483&&adcnum<3202)){
	if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_RESET&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_RESET&&GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_SET&&(adcnum>2483&&adcnum<3202)){

		GPIO_SetBits(GPIOC,GPIO_Pin_0); 													//充电
		t=0;
	//	while(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_SET){							//循环检测充电接触器反馈的电平
	//		t++;
	//		if(t>100){
	//			break;
	//		}else{
	//			delay_ms(10);	
	//		}
	//	}
		if(t>100){
			printf("充电接触器反馈超时-充电\r\n");												 //10秒还没有检测到低电平，开启充电超时
			GPIO_ResetBits(GPIOC,GPIO_Pin_0); 												 //还原充电位
		   	TIM_SetCompare2(TIM3,1000);	
			IsTran=0;
			OSTaskResume(7);
			return 26;
		}else{
			my_data.Ispower=1;
			IsTran=0;
			printf("充电成功\r\n");
			OSTaskResume(7); 
			printf("蜂鸣器值：%d\r\n",BEEP);
			if(BEEP==1){
				OSTaskResume(13);
			}
			return 0;
		}		
	}else{																					//又有条件通不过，直接停止
		printf("最后一次条件检测未通过\r\n");
		TIM_SetCompare2(TIM3,1000);	
		IsTran=0;
		OSTaskResume(7);
		return 27;
	}	
}

//停止充
u8 StopChage(){
	u8 i=0;
	//printf("执行停充方法#1\r\n");
	while(IsTran==1);
	IsTran=1;
	TIM_SetCompare2(TIM3,1000);																//停止pwm
	GPIO_ResetBits(GPIOC,GPIO_Pin_0);
	OSTaskSuspend(7);														//充电标志位置低
	//printf("执行停充方法\r\n");
//	while(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_RESET){								//循环检测充电接触器反馈的电平
//		i++;
//		if(i>100){
//			break;
//		}else{
//			delay_ms(10);	
//		}
//	}
	if(i>100){																			 	//停止失败
		printf("充电接触器反馈超时-停止\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 0;
	}else{
		printf("停止充电成功\r\n");
		my_data.Ispower=0;
		IsTran=0;
		OSTaskResume(7);
		printf("蜂鸣器值：%d\r\n",BEEP);
		if(BEEP==1){								 										//响蜂鸣器
			OSTaskResume(13); 
		}																   					//停止成功
		return 1;
	}

}

//停充后将代号发送平台
void SendStop(u8 c,u8 z){
	if(islink==0){
		while(IsSend==0){
			delay_ms(1);
		}
		IsSend=0;
		memset(tcp_client_sendbuf,'\0',256);
		//	my_data.Endp,my_data.Cvol,my_data.Cele,c,z
		sprintf(tcp_client_sendbuf,"{\"type\":\"stopdata\",\"w\":%d,\"v\":%d,\"a\":%d,\"c\":%d,\"z\":%d,\"Orderid\":%d,\"Cpower\":%d}\r\n",my_data.Endp,my_data.Cvol,my_data.Cele,c,z,my_data.Orderid,my_data.Cpower);
		tcp_client_flag |= LWIP_SEND_DATA;

		my_data.Cpower=0;
		my_data.Ispower=0;
		my_data.Uint=0;
		my_data.Money=0;
		my_data.Orderid=0;
		my_data.Isend=1;
		//存储数据
		FLASH_Unlock();
		FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
		FLASH_ErasePage(FLASH_ADDR);
		STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
		FLASH_Lock();

	}
}




//服务端数据解析
void any(u8*d){
	cJSON *type,*json=cJSON_Parse((char *)d);
	printf("接收数据：%s\r\n",d);
	if(!json) {
	//	printf("解析失败!\n");
		return ;
    }
	type = cJSON_GetObjectItem(json,"type");
	if(!type) {
	//	printf("没有该类型!\n");
		return ;
    }
	if(strcmp(type->valuestring,"StartChage")==0){ //充电
		int st= StartChage();
		cJSON *oid= cJSON_GetObjectItem(json,"Orderid");
		cJSON *uint= cJSON_GetObjectItem(json,"uint");
		cJSON *smoney= cJSON_GetObjectItem(json,"smoney");

		if(!oid||!uint||!smoney) {
			cJSON_Delete(oid);  	//释放内存
			cJSON_Delete(uint);  	//释放内存
			cJSON_Delete(smoney);	//释放内存
			cJSON_Delete(json);  	//释放内存   
			cJSON_Delete(type);  	//释放内存   
    		cJSON_free(d); 
			return;
		}

		if(st==0){
			printf("启动充电完成#1\r\n");
			if(islink==0){
				while(IsSend==0){
					delay_ms(1);
				}
				IsSend=0;
				memset(tcp_client_sendbuf,'\0',256);
				//电度发送服务端
				sprintf(tcp_client_sendbuf,"{\"type\":\"startdata\",\"w\":%d,\"v\":%d,\"a\":%d,\"Orderid\":%d}\r\n",my_data.Endp,my_data.Cvol,my_data.Cele,oid->valueint);
				tcp_client_flag |= LWIP_SEND_DATA;
				my_data.Ispower=1;
				my_data.Cpower=0;
				my_data.Uint=uint->valueint;
				my_data.Money=smoney->valueint;
				my_data.Orderid=oid->valueint;
				my_data.Isend=0;
				my_data.Starp=my_data.Endp;
				//存储数据
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
		}else{
			if(islink==0){
					
				while(IsSend==0){
					delay_ms(1);
				}
				IsSend=0;
		  		memset(tcp_client_sendbuf,'\0',256);
			   	sprintf(tcp_client_sendbuf,"{\"type\":\"starterr\",\"code\":%d,\"Orderid\":%d}\r\n",st,oid->valueint);
				tcp_client_flag |= LWIP_SEND_DATA;
			}
		}
		cJSON_Delete(oid);  //释放内存
		cJSON_Delete(uint);  //释放内存
		cJSON_Delete(smoney);  //释放内存
		cJSON_Delete(json);  //释放内存   
		cJSON_Delete(type);  //释放内存   
    	cJSON_free(d); 
		return;
	}
	if(strcmp(type->valuestring, "StopChage")==0){ 									//停止充电，分为网页停止和网络停止
	
		if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_RESET){						//判断充电反馈位是否为低
			u8 st=StopChage();														//停止操作
			cJSON *code= cJSON_GetObjectItem(json,"code");
			printf("停止充电#1\r\n");
			if(st){																	//停止成功
				if(code->valueint==0) {								//平台发起的充电
					printf("停止充电#2\r\n");
					SendStop(0,0);													//发送停充完成
				}else if(code->valueint==1) {						//网页发起的充电直接输出
					printf("停止充电#3\r\n");
					SendStop(1,0);
				}
			}else{																//停电失败，向平台发送停止失败要求用户急停或联系管理员
				if(code->valueint==0) {								//平台发起的充电
					printf("停止充电#2\r\n");
					SendStop(0,1);													//发送停充完成
				}else if(code->valueint==1) {						//网页发起的充电直接输出
					printf("停止充电#3\r\n");
					SendStop(1,1);
				}		
			}
			cJSON_Delete(code);
		}
		cJSON_Delete(json);  //释放内存   
		cJSON_Delete(type);  //释放内存   
   		cJSON_free(d); 
		return;
	}
	if(strcmp(type->valuestring, "UpdataVer")==0){ //更新版本
		printf("检测到系统版本有更新，核对是否需要下载...\r\n");
		if(my_data.Isdown==1){
			//char str[] = "http://192.168.1.66:215/index.php?s=/Home/Index/Down/id/1.html";
			char str[] = "http://139.199.221.53:9002/index.php?s=/Home/Index/NewBin";	
			printf("更新文件需要系统初始化完成后才下载，等待中...\r\n");
			OSTaskSuspend(7);
			while(!IsDown){	 //前期初始工作完成后下载
				delay_ms(500);
			}

			http_test((char *)str);
		}else{
			printf("启动程序检测上次下载文件存在问题，本次不下载.\r\n");
			my_data.Isdown=1;
			//存储数据
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
			FLASH_Lock();
		}
		cJSON_Delete(json);  //释放内存   
		cJSON_Delete(type);  //释放内存   
   		cJSON_free(d); 

		return;
	}

	if(strcmp(type->valuestring, "ping")==0){	 //心跳响应
		if(islink==0){
			while(IsSend==0){
				delay_ms(1);
			}
			IsSend=0;
			memset(tcp_client_sendbuf,'\0',256); 
			strcpy(tcp_client_sendbuf,"{\"type\":\"ping\"}\r\n");
			tcp_client_flag |= LWIP_SEND_DATA;
		}
		cJSON_Delete(json);  //释放内存   
		cJSON_Delete(type);  //释放内存   
   		cJSON_free(d);				
		return;
	}
	cJSON_Delete(json);  //释放内存   
	cJSON_Delete(type);  //释放内存   
   	cJSON_free(d);	
}


//tim5中断
void tim5_init(){
	TIM_TimeBaseInitTypeDef  TIM_TimeBaseStructure;
	NVIC_InitTypeDef NVIC_InitStructure;
	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM5, ENABLE); //时钟使能

	//定时器TIM5初始化
	TIM_TimeBaseStructure.TIM_Period = 2000; //设置在下一个更新事件装入活动的自动重装载寄存器周期的值	
	TIM_TimeBaseStructure.TIM_Prescaler =35999; //设置用来作为TIMx时钟频率除数的预分频值
	TIM_TimeBaseStructure.TIM_ClockDivision = TIM_CKD_DIV1; //设置时钟分割:TDTS = Tck_tim
	TIM_TimeBaseStructure.TIM_CounterMode = TIM_CounterMode_Up;  //TIM向上计数模式
	TIM_TimeBaseInit(TIM5, &TIM_TimeBaseStructure); //根据指定的参数初始化TIMx的时间基数单位
	TIM_ITConfig(TIM5,TIM_IT_Update,ENABLE ); //使能指定的TIM5中断,允许更新中断

	//中断优先级NVIC设置
	NVIC_InitStructure.NVIC_IRQChannel = TIM5_IRQn;  //TIM5中断
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority =3;  //先占优先级0级
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;  //从优先级3级
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE; //IRQ通道被使能
	NVIC_Init(&NVIC_InitStructure);  //初始化NVIC寄存器
	TIM_Cmd(TIM5, ENABLE);  //使能TIMx
	TIM_ClearITPendingBit(TIM5, TIM_IT_Update  );  //清除TIMx更新中断标志 
	//TIM_Cmd(TIM5, DISABLE);
//	TIM_Cmd(TIM5,ENABLE);	
}

//定时器5中断服务程序
void TIM5_IRQHandler(void){
	OSIntEnter();
	if (TIM_GetITStatus(TIM5, TIM_IT_Update) != RESET){		//检查TIM5更新中断发生与否
		TIM_ClearITPendingBit(TIM5, TIM_IT_Update  );		//清除TIMx更新中断标志 
		TIM5NUM++;											//定时次数
	   	if(TIM5NUM==35){									//35秒后判断掉线
			islink=1;										//更改状态位
		}
	}
	OSIntExit();
}
