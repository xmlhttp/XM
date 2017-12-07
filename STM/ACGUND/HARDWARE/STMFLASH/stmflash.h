#ifndef __STMFLASH_H__
#define __STMFLASH_H__
#include "sys.h"  
//////////////////////////////////////////////////////////////////////////////////////////////////////
//用户根据自己的需要设置
#define STM32_FLASH_SIZE 	512 	 		//所选STM32的FLASH容量大小(单位为K)
#define STM32_FLASH_WREN 	1              	//使能FLASH写入(0，不是能;1，使能)
//////////////////////////////////////////////////////////////////////////////////////////////////////

//FLASH起始地址
#define STM32_FLASH_BASE 0x08000000 		//STM32 FLASH的起始地址
//用户数据存储地址
#define FLASH_ADDR 0x0807F800
//FLASH解锁键值
#define FLASH_KEY1               0X45670123
#define FLASH_KEY2               0XCDEF89AB

//初始化数据结构体
struct InitData{
	u8 Idc;			  		//状态位AA为初始化过
	u8 Ip[4];		  		//IP地址
	u8 Mask[4];				//子网掩码
	u8 Gway[4];				//网关
	u8 Cloud[4];		   	//云平台地址
	u16 Port;				//云平台端口号
	char Server[32];		//服务器域名
	char Vname[16];			//板卡名称
	u32 Sid;				//站点ID
	char Spwd[12];			//站点密码
	char Pwd[10];			//用户密码
	char Session[10];		//session
	u16 Volup;				//电压上限
	u16 Voldown;			//电压下限
	u32 Ele;				//电流阀值
	u8 Isdown;				//是否需要下载
	u8 Isup;				//是否有更新
	u32 Vsize;				//更新文件大小
	u16 Pwm;				//Pwm值
	u8 Ispower;				//是否充电
	u16 Cvol;				//电表电压
	u32 Cele;				//电表电流
	u32 Starp;				//电表开始读数
	u32	Endp;				//电表当前电度
	u32 Cpower;				//充电电量
	u32 Uint;				//本次充电单价
	u32 Money;				//充电总价
	u32 Orderid;			//订单ID
	u8 Isend;				//订单是否结算	1结算|0未结算
};

void STMFLASH_Unlock(void);					  //FLASH解锁
void STMFLASH_Lock(void);					  //FLASH上锁
u8 STMFLASH_GetStatus(void);				  //获得状态
u8 STMFLASH_WaitDone(u16 time);				  //等待操作结束
u8 STMFLASH_ErasePage(u32 paddr);			  //擦除页
u8 STMFLASH_WriteHalfWord(u32 faddr, u16 dat);//写入半字
u16 STMFLASH_ReadHalfWord(u32 faddr);		  //读出半字  
void STMFLASH_WriteLenByte(u32 WriteAddr,u32 DataToWrite,u16 Len);	//指定地址开始写入指定长度的数据
u32 STMFLASH_ReadLenByte(u32 ReadAddr,u16 Len);						//指定地址开始读取指定长度数据
void STMFLASH_Write(u32 WriteAddr,u16 *pBuffer,u16 NumToWrite);		//从指定地址开始写入指定长度的数据
void STMFLASH_Read(u32 ReadAddr,u16 *pBuffer,u16 NumToRead);   		//从指定地址开始读出指定长度的数据
void STMFLASH_WriteHalf(u32 WriteAddr,u8 *pBuffer,u32 NumToWrite);

//测试写入
void Test_Write(u32 WriteAddr,u16 WriteData);								   
#endif

















