#ifndef _exti_H
#define	_exti_H
#include "stm32f10x.h"
#include "delay.h"
#include "sys.h"
 
#define FLASH_ADDR 0x0807F800
#define k_left GPIO_Pin_2

//��ʼ�����ݽṹ��
struct InitData{
	u8 Idc;			  		//״̬λAAΪ��ʼ����
	u8 Ip[4];		  		//IP��ַ
	u8 Mask[4];				//��������
	u8 Gway[4];				//����
	u8 Cloud[4];		   	//��ƽ̨��ַ
	u16 Port;				//��ƽ̨�˿ں�
	char Server[32];		//����������
	char Vname[16];			//�忨����
	u32 Sid;				//վ��ID
	char Spwd[12];			//վ������
	char Pwd[10];			//�û�����
	char Session[10];		//session
	u16 Volup;				//��ѹ����
	u16 Voldown;			//��ѹ����
	u32 Ele;				//������ֵ
	u8 Isdown;				//�Ƿ���Ҫ����
	u8 Isup;				//�Ƿ��и���
	u32 Vsize;				//�����ļ���С
	u16 Pwm;				//Pwmֵ
	u8 Ispower;				//�Ƿ���
	u16 Cvol;				//����ѹ
	u32 Cele;				//������
	u32 Starp;				//���ʼ����
	u32	Endp;				//���ǰ���
	u32 Cpower;				//������
	u32 Uint;				//���γ�絥��
	u32 Money;				//����ܼ�
	u32 Orderid;			//����ID
	u8 Isend;				//�Ƿ����

};
void exti_init(void);
#endif
