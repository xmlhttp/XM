#include "exti.h"
#include "beep.h"
#include "stmflash.h"

/*
按键中断，初始化板卡
*/

void exti_init(){
	GPIO_InitTypeDef GPIO_struct;
	EXTI_InitTypeDef EXTI_struct;
	NVIC_InitTypeDef NVIC_struct;

	RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOE|RCC_APB2Periph_AFIO|RCC_APB2Periph_GPIOC,ENABLE);
 
 	GPIO_struct.GPIO_Speed=GPIO_Speed_50MHz;
/*	GPIO_struct.GPIO_Pin=GPIO_Pin_0;
	GPIO_struct.GPIO_Mode=GPIO_Mode_Out_PP;
	GPIO_Init(GPIOC,&GPIO_struct);
	GPIO_SetBits(GPIOC,GPIO_Pin_0);

	GPIO_struct.GPIO_Pin=GPIO_Pin_2;
	GPIO_struct.GPIO_Mode=GPIO_Mode_IPU;
	//20170801改为浮空输入
	//GPIO_struct.GPIO_Mode=GPIO_Mode_IN_FLOATING;
	GPIO_Init(GPIOC,&GPIO_struct);
		*/
	GPIO_struct.GPIO_Pin=k_left;
	GPIO_struct.GPIO_Mode=GPIO_Mode_IPU;
	GPIO_Init(GPIOE,&GPIO_struct);

	GPIO_EXTILineConfig(GPIO_PortSourceGPIOE,GPIO_PinSource2);

	EXTI_struct.EXTI_Line=EXTI_Line2;
	EXTI_struct.EXTI_Mode=EXTI_Mode_Interrupt;
	EXTI_struct.EXTI_Trigger=EXTI_Trigger_Falling;
	EXTI_struct.EXTI_LineCmd=ENABLE;
	EXTI_Init(&EXTI_struct);
	
	NVIC_PriorityGroupConfig(NVIC_PriorityGroup_1);
	NVIC_struct.NVIC_IRQChannel=EXTI2_IRQn;
	NVIC_struct.NVIC_IRQChannelPreemptionPriority=0;
	NVIC_struct.NVIC_IRQChannelSubPriority=0;
	NVIC_struct.NVIC_IRQChannelCmd=ENABLE;
	NVIC_Init(&NVIC_struct);

}


void EXTI2_IRQHandler(void){ 
	u8 num=0;

	OSIntEnter(); 
	if(EXTI_GetITStatus(EXTI_Line2)==SET){
		EXTI_ClearITPendingBit(EXTI_Line2);
		delay_ms(10);
		while(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_2)==Bit_RESET&&num<=250){ //防止用户误操作 延时2.5秒执行
			num++;
			delay_ms(10);
		}
		if(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_2)==Bit_RESET&&num>=250){
			
			struct InitData  init_data={
				170,			  			//状态位AA为初始化过
				{192,168,1,88},		  		//IP地址
				{255,255,255,0},			//子网掩码
				{192,168,1,1},				//网关
				{192,168,1,77},			   	//云平台地址
				8282,						//云平台端口号
				"www.vmuui.com",			//服务器域名
				"VM001",					//板卡名称
				1,							//站点ID
				"123456",					//站点密码
				"admin",					//用户密码
				{'\0'},						//session
				26000,						//电压上限
				18000,						//电压下限
				1000,						//电流阀值
				1,							//是否需要下载 ，用于更新的应用程序无法安装，用原来的程序时版本过低但是本次不下载
				0,							//是否有更新
				0,							//更新文件大小
				266,						//Pwm值
				0,							//是否充电
				0,							//电表电压
				0,							//电表电流
				0,							//电表开始读数
				0,							//电表当前电度
				0,							//充电电量
				0,							//本次充电单价
				0,							//充电总价
				0,							//订单ID
				1							//是否结算
			};
			delay_ms(10);
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&init_data,sizeof(init_data));
			FLASH_Lock();
			BEEP=0;
			delay_ms(1000);
			BEEP=1;	
		}
		while(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_2)==0);
	}
OSIntExit();
}
