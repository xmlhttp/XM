#include "sys.h"
#include "delay.h"
#include "usart2.h"	 
#include "mod.h"
#include "tcp_client_demo.h"
#include "stmflash.h" 
	 
////////////////////////////////////////////////////////////////////////////////// 	 
//���ʹ��ucos,����������ͷ�ļ�����.
#if SYSTEM_SUPPORT_OS
#include "includes.h"					//ucos ʹ��	  
#endif

int BUFLENGTH=0; //��ǰ�ַ�������
u8 read_buf[19] = {'\0'};

u8 Is485=1;//���������׼����1����|0�쳣
u8 coun485=0;//�����ߴ���ͳ��
//flash����	stmflash.h
extern struct InitData  my_data;
//���緢���ַ���
extern char tcp_client_sendbuf[256];
//�Ƿ��������������
extern u8 IsSend;
//�Ƿ�����
extern u8 islink;

//��ʱ��7��ʼ�������ڼ��485�Ƿ�����
void tim7_init(){
	TIM_TimeBaseInitTypeDef  TIM_TimeBaseStructure;
	NVIC_InitTypeDef NVIC_InitStructure;
	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM7, ENABLE); //ʱ��ʹ��

	//��ʱ��TIM7��ʼ��
	TIM_TimeBaseStructure.TIM_Period = 1000; //��������һ�������¼�װ�����Զ���װ�ؼĴ������ڵ�ֵ	
	TIM_TimeBaseStructure.TIM_Prescaler =35999; //����������ΪTIMxʱ��Ƶ�ʳ�����Ԥ��Ƶֵ
	TIM_TimeBaseStructure.TIM_ClockDivision = TIM_CKD_DIV1; //����ʱ�ӷָ�:TDTS = Tck_tim
	TIM_TimeBaseStructure.TIM_CounterMode = TIM_CounterMode_Up;  //TIM���ϼ���ģʽ
	TIM_TimeBaseInit(TIM7, &TIM_TimeBaseStructure); //����ָ���Ĳ�����ʼ��TIMx��ʱ�������λ
	TIM_ClearFlag(TIM7, TIM_FLAG_Update);
	TIM_ITConfig(TIM7,TIM_IT_Update,ENABLE ); //ʹ��ָ����TIM7�ж�,��������ж�

	//�ж����ȼ�NVIC����
	NVIC_InitStructure.NVIC_IRQChannel = TIM7_IRQn;  //TIM5�ж�
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 3;  //��ռ���ȼ�0��
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;  //�����ȼ�3��
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE; //IRQͨ����ʹ��
	NVIC_Init(&NVIC_InitStructure);  //��ʼ��NVIC�Ĵ���

	TIM_ClearITPendingBit(TIM7, TIM_IT_Update  );  //���TIMx�����жϱ�־ 
	TIM_Cmd(TIM7, DISABLE);	 
	
}

//��ʼ��485
void uart2_init(u32 bound){
	//GPIO�˿�����
	GPIO_InitTypeDef GPIO_InitStructure;
	USART_InitTypeDef USART_InitStructure;
	NVIC_InitTypeDef NVIC_InitStructure;
	 
	RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOA|RCC_APB2Periph_GPIOC|RCC_APB2Periph_AFIO, ENABLE);	//ʹ��USART2��GPIOAʱ��
  	RCC_APB1PeriphClockCmd(RCC_APB1Periph_USART2,ENABLE);
	//USART2_TX   GPIOA.2
	GPIO_InitStructure.GPIO_Pin = GPIO_Pin_2; //PA.2
	GPIO_InitStructure.GPIO_Speed = GPIO_Speed_50MHz;
	GPIO_InitStructure.GPIO_Mode = GPIO_Mode_AF_PP;	//�����������
	GPIO_Init(GPIOA, &GPIO_InitStructure);//��ʼ��GPIOA.2
   
	//USART2_RX	  GPIOA.3��ʼ��
	GPIO_InitStructure.GPIO_Pin = GPIO_Pin_3;//PA3
	GPIO_InitStructure.GPIO_Mode = GPIO_Mode_IN_FLOATING;//��������
	GPIO_Init(GPIOA, &GPIO_InitStructure);//��ʼ��GPIOA.3  
	//��ʼ����ͣ����GPIOC.1
	/*GPIO_InitStructure.GPIO_Pin = GPIO_Pin_1;//PC1
	GPIO_Init(GPIOC, &GPIO_InitStructure);
	 */
	GPIO_InitStructure.GPIO_Pin = GPIO_Pin_5;//PC5
	GPIO_InitStructure.GPIO_Mode=GPIO_Mode_Out_PP;	  //��˫������
	GPIO_Init(GPIOC,&GPIO_InitStructure);
	//���ܽ�
	/*GPIO_InitStructure.GPIO_Pin = GPIO_Pin_0;//PC0
	GPIO_Init(GPIOC, &GPIO_InitStructure);*/
	//Usart1 NVIC ����							 
	NVIC_InitStructure.NVIC_IRQChannel = USART2_IRQn;
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority=3;//��ռ���ȼ�3
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;		//�����ȼ�3
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE;			//IRQͨ��ʹ��
	NVIC_Init(&NVIC_InitStructure);	//����ָ���Ĳ�����ʼ��VIC�Ĵ���
  
   //USART ��ʼ������

	USART_InitStructure.USART_BaudRate = bound;//���ڲ�����
	USART_InitStructure.USART_WordLength = USART_WordLength_8b;//�ֳ�Ϊ8λ���ݸ�ʽ
	USART_InitStructure.USART_StopBits = USART_StopBits_1;//һ��ֹͣλ
	USART_InitStructure.USART_Parity = USART_Parity_No;//����żУ��λ
	USART_InitStructure.USART_HardwareFlowControl = USART_HardwareFlowControl_None;//��Ӳ������������
	USART_InitStructure.USART_Mode = USART_Mode_Rx | USART_Mode_Tx;	//�շ�ģʽ
	USART_Init(USART2, &USART_InitStructure); //��ʼ������2
	USART_ITConfig(USART2, USART_IT_RXNE, ENABLE);//�������ڽ����ж�
	USART_Cmd(USART2, ENABLE);                    //ʹ�ܴ���2
	USART_ClearFlag(USART2,USART_FLAG_TC);
	GPIO_ResetBits(GPIOB,GPIO_Pin_5); 
	tim7_init();
}



//��ʱ��7�жϷ������
void TIM7_IRQHandler(void){
	OSIntEnter();
	if (TIM_GetITStatus(TIM7, TIM_IT_Update) != RESET){ //���TIM7�����жϷ������
		TIM_ClearITPendingBit(TIM7, TIM_IT_Update  );  	//���TIMx�����жϱ�־ 
		TIM_Cmd(TIM7, DISABLE);							//ֹͣʹ��
		TIM_SetCounter(TIM7, 0);					  	//��ʱ������
		coun485++;										//�����ۼ�
		if(coun485>5){								   	//����5�μ�鲻���������
			Is485=0;									//���ĵ����ϱ�ʶ
			coun485=0;
		}
		BUFLENGTH=0;									//���������ݱ�ʶ
		printf("����ȡ��ʱ����������·û�Ӻû��ߵ����ϣ�\r\n");
	}
	OSIntExit();
}





//����2�ж�
/*
�жϽ������ݵĵ�һλ�Ƿ�Ϊ��ַλ0x01���ڶ�λ�Ƿ�Ϊ������0x03,����λΪ���յ����ݳ���0x06,�����λCRCУ��λ������11λ
*/
void USART2_IRQHandler(void){
	OSIntEnter();
	if(USART_GetFlagStatus(USART2, USART_FLAG_ORE) != Bit_RESET){
		USART_ClearFlag(USART2, USART_FLAG_ORE);
		if(BUFLENGTH==0){
			u8 temp=USART_ReceiveData(USART2);
			if(temp==57){
				read_buf[BUFLENGTH]=temp;
				BUFLENGTH++;	
			}
		}else if(BUFLENGTH==1){
			u8 temp=USART_ReceiveData(USART2);
			if(temp==3){
				read_buf[BUFLENGTH]=temp;
				BUFLENGTH++;	
			}
		}else{
			read_buf[BUFLENGTH]=USART_ReceiveData(USART2);
			BUFLENGTH++;
		}
	}
	if(USART_GetITStatus(USART2, USART_IT_RXNE) != Bit_RESET){
		USART_ClearITPendingBit(USART2, USART_IT_RXNE);
		if(BUFLENGTH==0){
			u8 temp=USART_ReceiveData(USART2);
			if(temp==57){
				read_buf[BUFLENGTH]=temp;
				BUFLENGTH++;	
			}
		}else if(BUFLENGTH==1){
			u8 temp=USART_ReceiveData(USART2);
			if(temp==3){
				read_buf[BUFLENGTH]=temp;
				BUFLENGTH++;	
			}
		}else{
			read_buf[BUFLENGTH]=USART_ReceiveData(USART2);
			BUFLENGTH++;
		}
  	}
	if(BUFLENGTH==19&&CHK_CRC16(read_buf,19)==1){
		
	//	printf("����:%.2fKW*H����ѹ��%.2fV��������%.3fA\r\n",(float)((0xff & read_buf[6])<<24|(0xff & read_buf[5])<<16|(0xff & read_buf[3])<<8|(0xff & read_buf[4]))/10,(float)((0xff & read_buf[7])<<8|(0xff & read_buf[8]))/100,(float)((0xff & read_buf[11])<<8|(0xff & read_buf[12])|(0xff & read_buf[9])<<8|(0xff & read_buf[10]))/1000);
		u32 cpower;
		TIM_ClearITPendingBit(TIM7, TIM_IT_Update  );  //���TIMx�����жϱ�־ 
		TIM_Cmd(TIM7, DISABLE);
		TIM_SetCounter(TIM7, 0);
		Is485=1;
		coun485=0;
		cpower=(u32)((0xff & read_buf[6])<<24|(0xff & read_buf[5])<<16|(0xff & read_buf[3])<<8|(0xff & read_buf[4]));
		//����ж���δ���� ,����쳣
		if(cpower<my_data.Endp&&my_data.Ispower==1&&my_data.Orderid!=0&&my_data.Isend==1){
			u8 t;
			my_data.Cpower=my_data.Cpower+1;
			my_data.Endp=cpower;
			my_data.Cvol=(u16)((0xff & read_buf[7])<<8|(0xff & read_buf[8]));
			my_data.Cele=(u32)((0xff & read_buf[11])<<8|(0xff & read_buf[12])|(0xff & read_buf[9])<<8|(0xff & read_buf[10]));
			t=StopChage();
			if(t==0){
				SendStop(23,0);
			}else{
				SendStop(23,13);
			}

		}else if(cpower>my_data.Endp){
			my_data.Endp=cpower;
			my_data.Cvol=(u16)((0xff & read_buf[7])<<8|(0xff & read_buf[8]));
			my_data.Cele=(u32)((0xff & read_buf[11])<<8|(0xff & read_buf[12])|(0xff & read_buf[9])<<8|(0xff & read_buf[10]));
			if(my_data.Ispower==1&&my_data.Orderid!=0&&my_data.Isend==1){
				my_data.Cpower=	my_data.Endp-my_data.Starp;
			}

			if(islink==0&&my_data.Money<=(float)(my_data.Cpower*my_data.Uint)/10&&my_data.Ispower==1&&my_data.Orderid!=0&&my_data.Isend==1){
				u8 t=StopChage();
				if(t==0){
					SendStop(24,0);
				}else{
					 SendStop(24,14);
				}
			}else if(islink==1&&my_data.Money<=(float)(my_data.Cpower*my_data.Uint)/10&&my_data.Ispower==1&&my_data.Orderid!=0&&my_data.Isend==1){
				my_data.Ispower=0;
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();

			}else{
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
				//����
				if(islink==0){
					while(IsSend==0){
				 		delay_ms(1);
					}
					IsSend=0;
					memset(tcp_client_sendbuf,'\0',256); 
					//������ͨ�ύ����
					sprintf(tcp_client_sendbuf,"{\"type\":\"postdata\",\"w\":%d,\"v\":%d,\"a\":%d,\"Ispower\":%d,\"Orderid\":%d,\"isend\":%d,\"Cpower\":%d}\r\n",my_data.Endp,my_data.Cvol,my_data.Cele,my_data.Ispower,my_data.Orderid,my_data.Isend,my_data.Cpower);
					tcp_client_flag |= LWIP_SEND_DATA;	
				}
			}

		} 
		BUFLENGTH=0;		
  	}
	OSIntExit(); 	   
}

