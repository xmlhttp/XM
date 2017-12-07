#ifndef __STMFLASH_H__
#define __STMFLASH_H__
#include "sys.h"  
//////////////////////////////////////////////////////////////////////////////////////////////////////
//�û������Լ�����Ҫ����
#define STM32_FLASH_SIZE 	512 	 		//��ѡSTM32��FLASH������С(��λΪK)
#define STM32_FLASH_WREN 	1              	//ʹ��FLASHд��(0��������;1��ʹ��)
//////////////////////////////////////////////////////////////////////////////////////////////////////

//FLASH��ʼ��ַ
#define STM32_FLASH_BASE 0x08000000 		//STM32 FLASH����ʼ��ַ
//�û����ݴ洢��ַ
#define FLASH_ADDR 0x0807F800
//FLASH������ֵ
#define FLASH_KEY1               0X45670123
#define FLASH_KEY2               0XCDEF89AB

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
	u8 Isend;				//�����Ƿ����	1����|0δ����
};

void STMFLASH_Unlock(void);					  //FLASH����
void STMFLASH_Lock(void);					  //FLASH����
u8 STMFLASH_GetStatus(void);				  //���״̬
u8 STMFLASH_WaitDone(u16 time);				  //�ȴ���������
u8 STMFLASH_ErasePage(u32 paddr);			  //����ҳ
u8 STMFLASH_WriteHalfWord(u32 faddr, u16 dat);//д�����
u16 STMFLASH_ReadHalfWord(u32 faddr);		  //��������  
void STMFLASH_WriteLenByte(u32 WriteAddr,u32 DataToWrite,u16 Len);	//ָ����ַ��ʼд��ָ�����ȵ�����
u32 STMFLASH_ReadLenByte(u32 ReadAddr,u16 Len);						//ָ����ַ��ʼ��ȡָ����������
void STMFLASH_Write(u32 WriteAddr,u16 *pBuffer,u16 NumToWrite);		//��ָ����ַ��ʼд��ָ�����ȵ�����
void STMFLASH_Read(u32 ReadAddr,u16 *pBuffer,u16 NumToRead);   		//��ָ����ַ��ʼ����ָ�����ȵ�����
void STMFLASH_WriteHalf(u32 WriteAddr,u8 *pBuffer,u32 NumToWrite);

//����д��
void Test_Write(u32 WriteAddr,u16 WriteData);								   
#endif

















