#include "gpios.h"
#include "delay.h"

/*��ʼ��GPIO������Ĭ��ֵ	    
 *���� ȫ��������
 *PC1:���Ӵ�������
 *PC2:CC�ߣ�ȷ������
 *PE4:��ͣ
 *PE3:����
 *��� ȫ������
 *PC0:���λ
 *PC6:��ǹָʾ��
 *PC8:����
 *PC10:����ָʾ��
 */
void GPIOS_Init(void){

	GPIO_InitTypeDef GPIO_struct;
	RCC_APB2PeriphClockCmd(RCC_APB2Periph_AFIO|RCC_APB2Periph_GPIOC|RCC_APB2Periph_GPIOE,ENABLE);
	//��ʼ�����
	GPIO_struct.GPIO_Speed=GPIO_Speed_50MHz;
	GPIO_struct.GPIO_Pin=GPIO_Pin_0|GPIO_Pin_8|GPIO_Pin_10|GPIO_Pin_6;
	GPIO_struct.GPIO_Mode=GPIO_Mode_Out_PP;
	GPIO_Init(GPIOC,&GPIO_struct);
	//��ʼ������
	//PC1���Ӵ���������������Ƿ�ɹ����͵�ƽ���ڳ��״̬ ����������
	GPIO_struct.GPIO_Pin=GPIO_Pin_1|GPIO_Pin_2;
	GPIO_struct.GPIO_Mode=GPIO_Mode_IPU;
	GPIO_Init(GPIOC,&GPIO_struct);
	//PE4��ͣ����⼱ͣ��ť�Ƿ��£��͵�ƽ���£��������� |PE3����
	GPIO_struct.GPIO_Pin=GPIO_Pin_4|GPIO_Pin_3;
	GPIO_struct.GPIO_Mode=GPIO_Mode_IPU;
	GPIO_Init(GPIOE,&GPIO_struct);
	//Ĭ���������
	GPIO_ResetBits(GPIOC,GPIO_Pin_0);
	GPIO_SetBits(GPIOC,GPIO_Pin_8|GPIO_Pin_10|GPIO_Pin_6);
}

