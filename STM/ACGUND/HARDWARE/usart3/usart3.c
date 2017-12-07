#include "sys.h"
#include "delay.h"
#include "usart3.h"	 

//如果使用ucos,则包括下面的头文件即可.
#if SYSTEM_SUPPORT_OS
#include "includes.h"					//ucos 使用	  
#endif
#include "tcp_client_demo.h"
int DISTYPE=3; //车位状态 0无车 1有车 2阻挡 3未安装
u8 dis_buf[3]= {'\0'}; 
int DISLENGTH=0; //当前字符串长度
int DISNUM=0; //接收次数
float DISCOUNT=0; //总距离
float WDIS=0;
extern char tcp_client_sendbuf[256];
//是否允许发送数据完成
extern u8 IsSend;
extern u8 islink;
void tim6_init(void);
void uart3_init(u32 bound){	   

	USART_InitTypeDef USART_InitStructure;
	GPIO_InitTypeDef GPIO_InitStructure;
	NVIC_InitTypeDef NVIC_InitStructure;

	RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOB, ENABLE);
	RCC_APB1PeriphClockCmd(RCC_APB1Periph_USART3, ENABLE);

	/* Configure USART3 Tx (PB10) as alternate function push-pull */
	GPIO_InitStructure.GPIO_Pin = GPIO_Pin_10;
	GPIO_InitStructure.GPIO_Speed = GPIO_Speed_50MHz;
	GPIO_InitStructure.GPIO_Mode = GPIO_Mode_AF_PP;
	GPIO_Init(GPIOB, &GPIO_InitStructure);
  
	/* Configure USART3 Rx (PB11) as input floating */
	GPIO_InitStructure.GPIO_Pin = GPIO_Pin_11;
	GPIO_InitStructure.GPIO_Mode = GPIO_Mode_IN_FLOATING;
	GPIO_Init(GPIOB, &GPIO_InitStructure);
  
  
	USART_InitStructure.USART_BaudRate = bound;
	USART_InitStructure.USART_WordLength = USART_WordLength_8b;
	USART_InitStructure.USART_StopBits = USART_StopBits_1;
	USART_InitStructure.USART_Parity = USART_Parity_No;
	USART_InitStructure.USART_HardwareFlowControl = USART_HardwareFlowControl_None;
	USART_InitStructure.USART_Mode = USART_Mode_Rx | USART_Mode_Tx;

	
      /* Enable the USART3 Interrupt */
	NVIC_InitStructure.NVIC_IRQChannel = USART3_IRQn;
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority=3;
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE;
	NVIC_Init(&NVIC_InitStructure);
	USART_ITConfig(USART3, USART_IT_RXNE, ENABLE);  //使能接收中断

	USART_Init(USART3, &USART_InitStructure);
	/* Enable USART3 */
	USART_Cmd(USART3, ENABLE);
	USART_ClearFlag(USART3, USART_FLAG_TC);
	tim6_init();
}

//定时器6用于控制两次接收时间间隔
void tim6_init(){
	TIM_TimeBaseInitTypeDef  TIM_TimeBaseStructure;
	NVIC_InitTypeDef NVIC_InitStructure;
	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM6, ENABLE); //时钟使能

	//定时器TIM5初始化
	TIM_TimeBaseStructure.TIM_Period = 1000; //设置在下一个更新事件装入活动的自动重装载寄存器周期的值	
	TIM_TimeBaseStructure.TIM_Prescaler =3599; //设置用来作为TIMx时钟频率除数的预分频值
	TIM_TimeBaseStructure.TIM_ClockDivision = TIM_CKD_DIV1; //设置时钟分割:TDTS = Tck_tim
	TIM_TimeBaseStructure.TIM_CounterMode = TIM_CounterMode_Up;  //TIM向上计数模式
	TIM_TimeBaseInit(TIM6, &TIM_TimeBaseStructure); //根据指定的参数初始化TIMx的时间基数单位
	TIM_ITConfig(TIM6,TIM_IT_Update,ENABLE ); //使能指定的TIM4中断,允许更新中断

	//中断优先级NVIC设置
	NVIC_InitStructure.NVIC_IRQChannel = TIM6_IRQn;  //TIM5中断
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 3;  //先占优先级0级
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;  //从优先级3级
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE; //IRQ通道被使能
	NVIC_Init(&NVIC_InitStructure);  //初始化NVIC寄存器
	
}

//定时器4中断服务程序
void TIM6_IRQHandler(void){   //TIM5中断
	OSIntEnter();
	if (TIM_GetITStatus(TIM6, TIM_IT_Update) != RESET){  //检查TIM5更新中断发生与否
		TIM_ClearITPendingBit(TIM6, TIM_IT_Update  );  //清除TIMx更新中断标志 
		DISLENGTH=0;
		TIM_Cmd(TIM6, DISABLE);
		TIM_SetCounter(TIM6, 0);
	}
	OSIntExit();
}


//串口3中断
void USART3_IRQHandler(void){
	OSIntEnter();
	TIM_Cmd(TIM6, ENABLE);  //使能TIMx
	if(USART_GetFlagStatus(USART3, USART_FLAG_ORE) != Bit_RESET){
		USART_ClearFlag(USART3, USART_FLAG_ORE);
		dis_buf[DISLENGTH]=USART_ReceiveData(USART3);
		DISLENGTH++;
	}
	if(USART_GetITStatus(USART3, USART_IT_RXNE) != Bit_RESET){
		USART_ClearITPendingBit(USART3, USART_IT_RXNE);
		dis_buf[DISLENGTH]=USART_ReceiveData(USART3);
		DISLENGTH++;
  	}

	if(DISLENGTH==2){
		//printf("距离:%.2f cm,第一位是：0x%02x,第二位是：0x%02x\r\n",(float)((0xff & dis_buf[0])<<8|(0xff & dis_buf[1]))/10,dis_buf[0],dis_buf[1]);
		DISCOUNT=DISCOUNT+ (float)((0xff & dis_buf[0])<<8|(0xff & dis_buf[1]))/10;
		DISNUM++;
		if(DISNUM==5){
			DISCOUNT=DISCOUNT/5;
			WDIS= DISCOUNT;
			//printf("距离:%.2f cm,DISTYPE：%d\r\n",DISCOUNT,DISTYPE);
		
			if(DISCOUNT<=5&&DISTYPE!=2){
				DISTYPE=2;
				if(islink==0){
				while(IsSend==0){
					delay_ms(1);
				}
				IsSend=0;
				memset(tcp_client_sendbuf,'\0',256);
				strcpy(tcp_client_sendbuf,"{\"type\":\"disdata\",\"s\":2}\r\n");
				tcp_client_flag |= LWIP_SEND_DATA;
				}
			}else if(DISCOUNT>5&&DISCOUNT<=300&&DISTYPE!=1){
				DISTYPE=1;
				if(islink==0){
				while(IsSend==0){
					delay_ms(1);
				}
				IsSend=0;
				memset(tcp_client_sendbuf,'\0',256);
				strcpy(tcp_client_sendbuf,"{\"type\":\"disdata\",\"s\":1}\r\n");
				tcp_client_flag |= LWIP_SEND_DATA;
				}
			}else if(DISCOUNT>300&&DISTYPE!=0){
				DISTYPE=0;
				if(islink==0){
				while(IsSend==0){
					delay_ms(1);
				}
				IsSend=0;
				memset(tcp_client_sendbuf,'\0',256);
				strcpy(tcp_client_sendbuf,"{\"type\":\"disdata\",\"s\":0}\r\n");
				tcp_client_flag |= LWIP_SEND_DATA;
				}
			}
			DISNUM=0;
			DISCOUNT=0;	
		}

		DISLENGTH=0;		
  	}
	OSIntExit();
}

