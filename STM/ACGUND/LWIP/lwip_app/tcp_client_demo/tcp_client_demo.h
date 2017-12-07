#ifndef __TCP_CLIENT_DEMO_H
#define __TCP_CLIENT_DEMO_H
#include "sys.h"
#include "includes.h"

#define TCP_CLIENT_RX_BUFSIZE	2000	//���ջ���������
#define REMOTE_PORT				8282	//����Զ��������IP��ַ
#define LWIP_SEND_DATA			0X80    //���������ݷ���

extern u8 tcp_client_recvbuf[TCP_CLIENT_RX_BUFSIZE];	//TCP�ͻ��˽������ݻ�����
extern u8 tcp_client_flag;		//TCP�ͻ������ݷ��ͱ�־λ

INT8U tcp_client_init(void);  //tcp�ͻ��˳�ʼ��(����tcp�ͻ����߳�)  
void time3_init(void);
int StartChage(void);
u8 StopChage(void);
void tim5_init(void);
void SendStop(u8 c,u8 z);
#endif

