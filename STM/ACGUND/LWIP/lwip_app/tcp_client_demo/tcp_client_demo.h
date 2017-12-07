#ifndef __TCP_CLIENT_DEMO_H
#define __TCP_CLIENT_DEMO_H
#include "sys.h"
#include "includes.h"

#define TCP_CLIENT_RX_BUFSIZE	2000	//接收缓冲区长度
#define REMOTE_PORT				8282	//定义远端主机的IP地址
#define LWIP_SEND_DATA			0X80    //定义有数据发送

extern u8 tcp_client_recvbuf[TCP_CLIENT_RX_BUFSIZE];	//TCP客户端接收数据缓冲区
extern u8 tcp_client_flag;		//TCP客户端数据发送标志位

INT8U tcp_client_init(void);  //tcp客户端初始化(创建tcp客户端线程)  
void time3_init(void);
int StartChage(void);
u8 StopChage(void);
void tim5_init(void);
void SendStop(u8 c,u8 z);
#endif

