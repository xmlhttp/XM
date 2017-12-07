#include "delay.h"
#include "sys.h"
#include "usart.h"
#include "usart2.h"	
#include "usart3.h"	
#include "usmart.h"
#include "malloc.h"
#include "string.h"
#include "dm9000.h"
#include "pwm.h"
#include "ADC.h"
#include "beep.h"
#include "lwip/netif.h"
#include "lwip_comm.h"
#include "lwipopts.h"
#include "includes.h"
#include "httpd.h"
#include "exti.h"
#include "gpios.h"
#include "tcp_client_demo.h"
#include "stmflash.h"
/************************************************
 ����֧�֣�http://www.vmuui.com
 �����о��ۻ������Ƽ����޹�˾ 
 ���ߣ�QQ469100943
************************************************/

#define countof(a)   (sizeof(a) / sizeof(*(a)))
//���緢���ֶ�
extern char tcp_client_sendbuf[256];
//flash����
extern struct InitData  my_data;
//�Ƿ��������������
extern u8 IsSend;
//�Ƿ���������
extern u8 IsDown;
//���߱�ʶλ
extern u8 islink;
//����ʶλ
//extern u8 PowerStatu;
//����״̬��ʶλ
extern u8 IsTran;
//����ʶ��
extern u8 Is485;
//��ѹ����
u8 PowerSave=0;								
//�������*********************ʵʱ���忨��Ϣ����
//�������ȼ�
#define POW_TASK_PRIO		9
//�����ջ��С
#define POW_STK_SIZE		64
//�����ջ
OS_STK	POW_TASK_STK[POW_STK_SIZE];
//������
void POW_task(void *pdata); 


//���ݷ�������****************��ѯ���ͳ���������
//�������ȼ�
#define ReadDATA_TASK_PRIO		7
//�����ջ��С
#define ReadDATA_STK_SIZE		64
//�����ջ
OS_STK	ReadDATA_TASK_STK[ReadDATA_STK_SIZE];
//������
void ReadDATA_task(void *pdata); 

//START����*******************������
//�������ȼ�
#define START_TASK_PRIO		12
//�����ջ��С
#define START_STK_SIZE		128
//�����ջ
OS_STK START_TASK_STK[START_STK_SIZE];
//������
void start_task(void *pdata); 

//�������ȼ� ******************����������
#define BEEP_TASK_PRIO		13
//�����ջ��С
#define BEEP_STK_SIZE		64
//�����ջ
OS_STK BEEP_TASK_STK[BEEP_STK_SIZE];
//������
void beep_task(void *pdata); 

int main(void){
//	SCB->VTOR=FLASH_BASE|0x07800;						//��ַƫ��������ַǰ����IAP����
	delay_init();	    								//��ʱ������ʼ��
	NVIC_PriorityGroupConfig(NVIC_PriorityGroup_2);		//����NVIC�жϷ���2:2λ��ռ���ȼ���2λ��Ӧ���ȼ�	  
	uart_init(115200);	 								//����1��ʼ��Ϊ115200
	usmart_dev.init(72);							   	//���ڿ���̨
	BEEP_Init();										//��������ʼ��
	pwm_init();											//PWM��ʼ��
	adc_init();											//ADC��ʼ��
	exti_init();										//�����жϳ�ʼ��
	tim5_init();										//Time5�����������
	uart2_init(9600);									//����2��ʼ������ȡ���
	uart3_init(9600);								   	//����3��ʼ������ȡ������
	my_mem_init(SRAMIN);								//��ʼ���ڲ��ڴ��
//	my_mem_init(SRAMEX);								//��ʼ���ⲿ�ڴ��
	GPIOS_Init();										//��ʼ��GPIO
	IsDown=0;											//�Ƿ��ܹ����أ�����ϵͳ������ɺ��������

	STMFLASH_Read(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
	my_data.Ispower=0;
	OSInit();

	while(my_data.Idc!=170){					//�忨��ʼ����ʶ
		printf("�忨δ��ʼ�����밴��ԭ��3������\r\n");
		delay_ms(1000);
		STMFLASH_Read(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
	}
	printf("�忨��ʼ���ɹ�1111\r\n");
											//UCOS��ʼ��
	while(lwip_comm_init()) 							//lwip��ʼ��
	{
		printf("�����ʼ��ʧ��!\r\n"); 					//lwip��ʼ��ʧ��
		GPIO_ResetBits(GPIOC,GPIO_Pin_10);
		delay_ms(1000);
	}
	printf("HTTP��ʼ��\r\n");
	httpd_init(); 									   	//web�����ʼ��
	while(tcp_client_init()){ 							//��ʼ��tcp_client(����tcp_client�߳�)
		printf("�ͻ��˳�ʼ��ʧ��!\r\n"); 
		GPIO_ResetBits(GPIOC,GPIO_Pin_10);
		delay_ms(1000);
	}
	OSTaskCreate(start_task,(void*)0,(OS_STK*)&START_TASK_STK[START_STK_SIZE-1],START_TASK_PRIO);
	OSStart(); 											//����UCOS
	
}
 
//start����
void start_task(void *pdata)
{
	OS_CPU_SR cpu_sr;	
	OSStatInit();																				//��ʼ��ͳ������
	OS_ENTER_CRITICAL();																		//���ж�  
	OSTaskCreate(ReadDATA_task,(void*)0,(OS_STK*)&ReadDATA_TASK_STK[ReadDATA_STK_SIZE-1],ReadDATA_TASK_PRIO);
	OSTaskCreate(POW_task,(void*)0,(OS_STK*)&POW_TASK_STK[POW_STK_SIZE-1],POW_TASK_PRIO); 		//�����������
	OSTaskCreate(beep_task,(void*)0,(OS_STK*)&BEEP_TASK_STK[BEEP_STK_SIZE-1],BEEP_TASK_PRIO);	//��������������
	OSTaskSuspend(OS_PRIO_SELF); 																//����start_task����
	OS_EXIT_CRITICAL();  																		//���ж�
}

//�쳣�������
void POW_task(void *pdata){
//	u8 t=0;
	u32 adcnum;
	u8 st;	
	while(1){
	/*	t++;
		if(t==10){
			t=0;
			printf("�������ִ����\r\n");
		}
	*/
		if(!IsTran){
			adcnum= getAD();
		}
		if(my_data.Ispower&&!IsTran){
		//printf("ִ�г����\r\n");																		//���ڳ��
		 //printf("�����\r\n");
		 //�ж��Ƿ������ǹ�����|�޹�ѹ����|PC0��Ϊ�͵�ѹ
		 if(my_data.Ispower&&!IsTran&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_RESET&&PowerSave==0){
		 	printf("�����ǹ\r\n");
		 	st=StopChage();	   //����ֹͣ����
			if(islink==0){
				if(st){
					SendStop(9,0);
				}else{
					SendStop(9,3);	
				}
			}else{
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			
			}
			continue;
		 }
		 //������ǹ��⣬���|�޹�ѹ����|PC1��Ϊ�ߵ�ѹ
		 if(my_data.Ispower&&!IsTran&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_SET&&PowerSave==0){
		 	printf("������ǹ\r\n");
		 	st=StopChage();	   //����ֹͣ����
			if(islink==0){
				if(st){
					SendStop(4,0);
				}else{
					SendStop(4,4);	
				}
			}else{
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;
		 }
		  
		//CC�Ͽ���ÿ���жϺ��涼Ҫ�Ӹ����ڳ��
		if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)!=Bit_RESET&&my_data.Ispower&&!IsTran){	//CC�߲�Ϊ�ͱ�ʾδ����
			printf("CC�Ͽ���ǹ\r\n");
			st=StopChage();	   //����ֹͣ����
			if(islink==0){
				if(st){
					SendStop(2,0);
				}else{
					SendStop(2,5);	
				}
			}else{
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;
		}
		//��׮�Ͽ�
		
		if((!(adcnum>2483&&adcnum<3202))&&my_data.Ispower&&!IsTran){
			printf("ADC��ǹ\r\n");
			st=StopChage();	   //����ֹͣ����
			if(islink==0){
				if(st){
					SendStop(3,0);
				}else{
					SendStop(3,6);	
				}
			}else{
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;	
		}
		
		//������
		if(Is485==0&&my_data.Ispower&&!IsTran){
			printf("��������ǹ\r\n");
			st=StopChage();	   //����ֹͣ����
			if(islink==0){
				if(st){
					SendStop(8,0);
				}else{
					SendStop(8,7);	
				}
			}else{
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;	
		}

		//��ͣ����
		if(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_RESET&&my_data.Ispower&&!IsTran){
			printf("��ͣ��ť����\r\n");
			st=StopChage();	   //����ֹͣ����
			if(islink==0){
				if(st){
					SendStop(5,0);
				}else{
					SendStop(5,8);	
				}
			}else{
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;	
		}

		//�������
		if(my_data.Cele>my_data.Ele&&my_data.Ispower&&!IsTran){
			printf("������ǹ\r\n");
			st=StopChage();	   //����ֹͣ����
			if(islink==0){
				if(st){
					SendStop(6,0);
				}else{
					SendStop(6,9);	
				}
			}else{
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;
		}
		//��ѹ���
		if((my_data.Cvol>=my_data.Volup||my_data.Cvol<=my_data.Voldown)&&my_data.Ispower&&!IsTran){
			st=StopChage();	   //����ֹͣ����	
			printf("������ѹ����\r\n");			 											
			if(st){	
				PowerSave=1;
				if(islink==0){
					while(IsSend==0){
						delay_ms(1);
					}
					IsSend=0;
					memset(tcp_client_sendbuf,'\0',256);  
					//����б仯���ͷ����
					strcpy(tcp_client_sendbuf,"{\"type\":\"poststatus\",\"z\":1}\r\n");
					tcp_client_flag |= LWIP_SEND_DATA;
				}
			}else{
				SendStop(10,10);	
			}
			continue;	
		}
	

		}else if(!my_data.Ispower&&!IsTran){
		//	printf("ִ��δ���\r\n");	
			//ֹͣ���
			//printf("������\r\n");
			//ȡ����ѹ����
			if(my_data.Cvol<my_data.Volup&&my_data.Cvol>my_data.Voldown&&!my_data.Ispower&&!IsTran&&PowerSave){
				int st=StartChage(); 
				PowerSave=0;
			   	if(st==0){
					if(islink==0){
						while(IsSend==0){
							delay_ms(1);
						}
					IsSend=0;
					memset(tcp_client_sendbuf,'\0',100);  
					//����б仯���ͷ����
					strcpy(tcp_client_sendbuf,"{\"type\":\"poststatus\",\"z\":0}\r\n");
					tcp_client_flag |= LWIP_SEND_DATA;
				}else{
					u8 st=StopChage();	   //����ֹͣ����
					if(st){
						SendStop(7,0);
					}else{
						SendStop(7,11);	
					}
				}
			   	continue;
			}
		}
		}
		//��ѹ����״̬��ǹ
		if(PowerSave&&(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_SET||adcnum>3800)){
			PowerSave=0;
			SendStop(11,0);
		}

		//��϶�ִ��
		//�ж��Ƿ��ǹ
		if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_RESET&&adcnum<3800){
			delay_ms(1);
			GPIO_ResetBits(GPIOC,GPIO_Pin_6);
			delay_ms(2);
		}else{
			delay_ms(1);
			GPIO_SetBits(GPIOC,GPIO_Pin_6);
			delay_ms(2); 
		}
		
		//�����ж� ��ͣ|����쳣|�����쳣
			
		if(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_RESET||islink||Is485==0){
			delay_ms(1);
			GPIO_ResetBits(GPIOC,GPIO_Pin_10);
			delay_ms(2); 
		}else{
			delay_ms(1);
		 	GPIO_SetBits(GPIOC,GPIO_Pin_10);
		 	delay_ms(2); 
		}

		OSTimeDlyHMSM(0,0,0,100);  //��ʱ100ms	
	}
}

//���ݷ�������
void ReadDATA_task(void *pdata){
	char send_buf[]={0x39,0x03,0x00,0x00,0x00,0x07,0x00,0xB0};							//���͵������
	u8 ch=0x55;																		   	//���ͳ���������
	u8 i,j=0;																		   	//���Ʋ���
	while(1){		
		
		if(j==0){																		//��ȡ�������
		//	printf("��ȡ������ݣ�\r\n");
		//	printf("***************************************************\r\n");	
		//	printf("PC2��ֵ��%d���жϴ�����%d���ڴ�ʹ����Ϊ:%d%%,���ʹ���ʣ�%d%%\r\n",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2),TIM5NUM,my_mem_perused(SRAMIN),my_mem_perused(SRAMIN));
			GPIO_SetBits(GPIOC,GPIO_Pin_5);	  											//PC5��ʶΪ����������
			delay_ms(1);
			for(i=0;i<countof(send_buf);i++){					 						//���η�������
				USART_SendData(USART2,send_buf[i]);
				while(USART_GetFlagStatus(USART2,USART_FLAG_TXE)==Bit_RESET);
			}
			delay_ms(2);
			GPIO_ResetBits(GPIOC,GPIO_Pin_5); 										   	//PC5��ԭ
			TIM_Cmd(TIM7, ENABLE); 														//������ʱ�����жϵ���Ƿ����
			delay_ms(1000);			
		}else{ 																			//��ȡ�������
		   	USART_SendData(USART3,ch);													//����232���ݻ�ȡ����
			while (USART_GetFlagStatus(USART3, USART_FLAG_TXE) == RESET);				//һֱ���232�����Ƿ������
			delay_ms(200);
		}
		j++;
		j=j%6;
	//	OSTaskSuspend(OS_PRIO_SELF);
	}
}

void beep_task(void *pdata){															//����������
	while(1){
		printf("��������\r\n");
		BEEP=0;	  																		//��������
		OSTimeDlyHMSM(0,0,1,0);															//��ʱ1s�����Ʒ�������1s
		BEEP=1;	
		printf("�����������\r\n");																		//�ط�����
		if(IsDown==0){																	//���ϵͳ�����ɹ�������Ϊ�ɸ���ϵͳ
			IsDown=1;																	//�޸����ر�ʶ��
			printf("ϵͳ�����ɹ���\r\n********************��ӭʹ�þ������罻����ǹ���ĳ���********************\r\n");	
		}
		OSTaskSuspend(OS_PRIO_SELF);													 //���߸�����
	}
}
