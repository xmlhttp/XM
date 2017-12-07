#ifndef __IAP_H__
#define __IAP_H__
#include "sys.h"      
typedef  void (*iapfun)(void);				//定义一个函数类型的参数.   
#define FLASH_APP1_ADDR		0x08007800  	//第一个应用程序起始地址(存放在FLASH) 0-14,15-134,135-254,255-255
#define FLASH_APP2_ADDR		0x08043800  	//第二个应用程序起始地址(存放在FLASH) 
											//保留0X08000000~0X08007800的空间为Bootloader使用	   
void iap_load_app(u32 appxaddr);			//跳转到APP程序执行
void iap_copy_appbin(u32 appxaddr,u32 startxaddr,int applen); //复制FLASH到另一块
#endif
