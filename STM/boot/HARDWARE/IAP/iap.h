#ifndef __IAP_H__
#define __IAP_H__
#include "sys.h"      
typedef  void (*iapfun)(void);				//����һ���������͵Ĳ���.   
#define FLASH_APP1_ADDR		0x08007800  	//��һ��Ӧ�ó�����ʼ��ַ(�����FLASH) 0-14,15-134,135-254,255-255
#define FLASH_APP2_ADDR		0x08043800  	//�ڶ���Ӧ�ó�����ʼ��ַ(�����FLASH) 
											//����0X08000000~0X08007800�Ŀռ�ΪBootloaderʹ��	   
void iap_load_app(u32 appxaddr);			//��ת��APP����ִ��
void iap_copy_appbin(u32 appxaddr,u32 startxaddr,int applen); //����FLASH����һ��
#endif
