#include "sys.h"
#include "delay.h"
#include "usart.h"
#include "stmflash.h"
#include "iap.h"
#include <string.h>
iapfun jump2app; 
u16 iapbuf[1025];   

//跳转到应用程序段
//appxaddr:用户代码起始地址.
void iap_load_app(u32 appxaddr)
{
	if(((*(vu32*)appxaddr)&0x2FFE0000)==0x20000000)	//检查栈顶地址是否合法.
	{ 
		jump2app=(iapfun)*(vu32*)(appxaddr+4);		//用户代码区第二个字为程序开始地址(复位地址)		
		MSR_MSP(*(vu32*)appxaddr);					//初始化APP堆栈指针(用户代码区的第一个字用于存放栈顶地址)
		jump2app();									//跳转到APP.
	}
}
		 
//appxaddr:应用程序FLASH的起始地址，目的地址
//startxaddr:被复制FLASH起始地址，源地址
//appsize:应用程序大小(字节).
void iap_copy_appbin(u32 appxaddr,u32 startxaddr,int appsize){
	int t;
	u16 i=0;
	u16 temp;
	u32 fwaddr=appxaddr;//当前写入的地址
	u8 *dfu=(u8 *)startxaddr;
	for(t=0;t<appsize;t+=2)
	{						    
		temp=(u16)dfu[1]<<8;
		temp+=(u16)dfu[0];	  
		dfu+=2;//偏移2个字节
		iapbuf[i++]=temp;	    
		if(i==1024)
		{
			i=0;
			STMFLASH_Write(fwaddr,iapbuf,1024);	
			fwaddr+=2048;//偏移2048  16=2*8.所以要乘以2.
			memset(iapbuf,'\0', sizeof(iapbuf));
		}
	}
	if(i)STMFLASH_Write(fwaddr,iapbuf,i);//将最后的一些内容字节写进去.  

}












