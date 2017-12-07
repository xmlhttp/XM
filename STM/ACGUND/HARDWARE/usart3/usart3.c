#include "sys.h"
#include "delay.h"
#include "usart3.h"	 

//���ʹ��ucos,����������ͷ�ļ�����.
#if SYSTEM_SUPPORT_OS
#include "includes.h"					//ucos ʹ��	  
#endif
#include "tcp_client_demo.h"
int DISTYPE=3; //��λ״̬ 0�޳� 1�г� 2�赲 3δ��װ
u8 dis_buf[3]= {'\0'}; 
int DISLENGTH=0; //��ǰ�ַ�������
int DISNUM=0; //���մ���
float DISCOUNT=0; //�ܾ���
float WDIS=0;
extern char tcp_client_sendbuf[256];
//�Ƿ��������������
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
	USART_ITConfig(USART3, USART_IT_RXNE, ENABLE);  //ʹ�ܽ����ж�

	USART_Init(USART3, &USART_InitStructure);
	/* Enable USART3 */
	USART_Cmd(USART3, ENABLE);
	USART_ClearFlag(USART3, USART_FLAG_TC);
	tim6_init();
}

//��ʱ��6���ڿ������ν���ʱ����
void tim6_init(){
	TIM_TimeBaseInitTypeDef  TIM_TimeBaseStructure;
	NVIC_InitTypeDef NVIC_InitStructure;
	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM6, ENABLE); //ʱ��ʹ��

	//��ʱ��TIM5��ʼ��
	TIM_TimeBaseStructure.TIM_Period = 1000; //��������һ�������¼�װ�����Զ���װ�ؼĴ������ڵ�ֵ	
	TIM_TimeBaseStructure.TIM_Prescaler =3599; //����������ΪTIMxʱ��Ƶ�ʳ�����Ԥ��Ƶֵ
	TIM_TimeBaseStructure.TIM_ClockDivision = TIM_CKD_DIV1; //����ʱ�ӷָ�:TDTS = Tck_tim
	TIM_TimeBaseStructure.TIM_CounterMode = TIM_CounterMode_Up;  //TIM���ϼ���ģʽ
	TIM_TimeBaseInit(TIM6, &TIM_TimeBaseStructure); //����ָ���Ĳ�����ʼ��TIMx��ʱ�������λ
	TIM_ITConfig(TIM6,TIM_IT_Update,ENABLE ); //ʹ��ָ����TIM4�ж�,��������ж�

	//�ж����ȼ�NVIC����
	NVIC_InitStructure.NVIC_IRQChannel = TIM6_IRQn;  //TIM5�ж�
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 3;  //��ռ���ȼ�0��
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;  //�����ȼ�3��
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE; //IRQͨ����ʹ��
	NVIC_Init(&NVIC_InitStructure);  //��ʼ��NVIC�Ĵ���
	
}

//��ʱ��4�жϷ������
void TIM6_IRQHandler(void){   //TIM5�ж�
	OSIntEnter();
	if (TIM_GetITStatus(TIM6, TIM_IT_Update) != RESET){  //���TIM5�����жϷ������
		TIM_ClearITPendingBit(TIM6, TIM_IT_Update  );  //���TIMx�����жϱ�־ 
		DISLENGTH=0;
		TIM_Cmd(TIM6, DISABLE);
		TIM_SetCounter(TIM6, 0);
	}
	OSIntExit();
}


//����3�ж�
void USART3_IRQHandler(void){
	OSIntEnter();
	TIM_Cmd(TIM6, ENABLE);  //ʹ��TIMx
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
		//printf("����:%.2f cm,��һλ�ǣ�0x%02x,�ڶ�λ�ǣ�0x%02x\r\n",(float)((0xff & dis_buf[0])<<8|(0xff & dis_buf[1]))/10,dis_buf[0],dis_buf[1]);
		DISCOUNT=DISCOUNT+ (float)((0xff & dis_buf[0])<<8|(0xff & dis_buf[1]))/10;
		DISNUM++;
		if(DISNUM==5){
			DISCOUNT=DISCOUNT/5;
			WDIS= DISCOUNT;
			//printf("����:%.2f cm,DISTYPE��%d\r\n",DISCOUNT,DISTYPE);
		
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

