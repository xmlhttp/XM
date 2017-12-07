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
	delay_init();	    //延时函数初始化
	uart_init(115200);
	exti_init();
	BEEP_Init();
	STMFLASH_Read(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
	while(my_data.Idc!=170){
		printf("板卡未初始化，请按还原键3秒以上\r\n");
		delay_ms(1000);
		STMFLASH_Read(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
	}
	printf("初始化完成，检测应用程序中...Isup:%d,Isdown:%d\r\n",my_data.Isup,my_data.Isdown);
	if(my_data.Isup==0){ //无更新跳转到正常代码
		if(((*(vu32*)(FLASH_APP1_ADDR+4))&0xFF000000)==0x08000000){//判断是否为0X08XXXXXX.
			printf("应用程序检测成功，正在为您跳转...\r\n");
			delay_ms(10);
			iap_load_app(FLASH_APP1_ADDR);
		}else{
			while(1){
				printf("应用程序无法执行，需要重新安装#1!\r\n");
				delay_ms(1000);
			}
		}
	}else{	//有更新执行拷贝更新，修改标识位，重启 
		printf("应用程序有更新，正在检测更新文件...\r\n");
		if(((*(vu32*)(FLASH_APP2_ADDR+4))&0xFF000000)==0x08000000){//判断是否为0X08XXXXXX.
			int size = my_data.Vsize; 
			printf("更新文件大小：%d，执行更新操作中...\r\n",size);
			iap_copy_appbin(FLASH_APP1_ADDR,FLASH_APP2_ADDR,size); 
			
			//修改标识位，写入flash
			my_data.Isup=0;
			delay_ms(10);
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
			FLASH_Lock();

			printf("更新操作完成，正在重启...\r\n");
			delay_ms(100);	
			__disable_fault_irq();   
			NVIC_SystemReset();
		}else{ //更新程序有问题，直接跳过，并且本次不下载程序
			printf("更新文件存在问题，为您选择可用应用...\r\n");
		 
		  //修改标识位，写入flash
			my_data.Isup=0;
			my_data.Isdown=0;
			delay_ms(10);
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
			FLASH_Lock();

			if(((*(vu32*)(FLASH_APP1_ADDR+4))&0xFF000000)==0x08000000){//判断是否为0X08XXXXXX.
				printf("已选择上一次应用程序，正在为您跳转...\r\n");
				delay_ms(10);
				iap_load_app(FLASH_APP1_ADDR);
			}else{
				while(1){
					printf("应用程序无法执行，需要重新安装#2!\r\n");
					delay_ms(1000);
				}
			}

		}
	}	
}

