#ifndef _exti_H
#define	_exti_H
#include "stm32f10x.h"
#include "delay.h"
#include "sys.h"
 
#define FLASH_ADDR 0x0807F800
#define k_left GPIO_Pin_2

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
	u8 Isend;				//是否结算

};
void exti_init(void);
#endif
