#include "sys.h"
#include "delay.h"
#include "usart.h"
#include "exti.h"
#include "iap.h"
#include "beep.h"
#include "stmflash.h"
#include <string.h>
int main(void){	
	struct InitData  my_data;
	delay_init();	    //��ʱ������ʼ��
	uart_init(115200);
	exti_init();
	BEEP_Init();
	STMFLASH_Read(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
	while(my_data.Idc!=170){
		printf("�忨δ��ʼ�����밴��ԭ��3������\r\n");
		delay_ms(1000);
		STMFLASH_Read(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
	}
	printf("��ʼ����ɣ����Ӧ�ó�����...Isup:%d,Isdown:%d\r\n",my_data.Isup,my_data.Isdown);
	if(my_data.Isup==0){ //�޸�����ת����������
		if(((*(vu32*)(FLASH_APP1_ADDR+4))&0xFF000000)==0x08000000){//�ж��Ƿ�Ϊ0X08XXXXXX.
			printf("Ӧ�ó�����ɹ�������Ϊ����ת...\r\n");
			delay_ms(10);
			iap_load_app(FLASH_APP1_ADDR);
		}else{
			while(1){
				printf("Ӧ�ó����޷�ִ�У���Ҫ���°�װ#1!\r\n");
				delay_ms(1000);
			}
		}
	}else{	//�и���ִ�п������£��޸ı�ʶλ������ 
		printf("Ӧ�ó����и��£����ڼ������ļ�...\r\n");
		if(((*(vu32*)(FLASH_APP2_ADDR+4))&0xFF000000)==0x08000000){//�ж��Ƿ�Ϊ0X08XXXXXX.
			int size = my_data.Vsize; 
			printf("�����ļ���С��%d��ִ�и��²�����...\r\n",size);
			iap_copy_appbin(FLASH_APP1_ADDR,FLASH_APP2_ADDR,size); 
			
			//�޸ı�ʶλ��д��flash
			my_data.Isup=0;
			delay_ms(10);
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
			FLASH_Lock();

			printf("���²�����ɣ���������...\r\n");
			delay_ms(100);	
			__disable_fault_irq();   
			NVIC_SystemReset();
		}else{ //���³��������⣬ֱ�����������ұ��β����س���
			printf("�����ļ��������⣬Ϊ��ѡ�����Ӧ��...\r\n");
		 
		  //�޸ı�ʶλ��д��flash
			my_data.Isup=0;
			my_data.Isdown=0;
			delay_ms(10);
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
			FLASH_Lock();

			if(((*(vu32*)(FLASH_APP1_ADDR+4))&0xFF000000)==0x08000000){//�ж��Ƿ�Ϊ0X08XXXXXX.
				printf("��ѡ����һ��Ӧ�ó�������Ϊ����ת...\r\n");
				delay_ms(10);
				iap_load_app(FLASH_APP1_ADDR);
			}else{
				while(1){
					printf("Ӧ�ó����޷�ִ�У���Ҫ���°�װ#2!\r\n");
					delay_ms(1000);
				}
			}

		}
	}	
}

