#include "exti.h"
#include "beep.h"
#include "stmflash.h"

/*
�����жϣ���ʼ���忨
*/

void exti_init(){
	GPIO_InitTypeDef GPIO_struct;
	EXTI_InitTypeDef EXTI_struct;
	NVIC_InitTypeDef NVIC_struct;

	RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOE|RCC_APB2Periph_AFIO|RCC_APB2Periph_GPIOC,ENABLE);
 
 	GPIO_struct.GPIO_Speed=GPIO_Speed_50MHz;
/*	GPIO_struct.GPIO_Pin=GPIO_Pin_0;
	GPIO_struct.GPIO_Mode=GPIO_Mode_Out_PP;
	GPIO_Init(GPIOC,&GPIO_struct);
	GPIO_SetBits(GPIOC,GPIO_Pin_0);

	GPIO_struct.GPIO_Pin=GPIO_Pin_2;
	GPIO_struct.GPIO_Mode=GPIO_Mode_IPU;
	//20170801��Ϊ��������
	//GPIO_struct.GPIO_Mode=GPIO_Mode_IN_FLOATING;
	GPIO_Init(GPIOC,&GPIO_struct);
		*/
	GPIO_struct.GPIO_Pin=k_left;
	GPIO_struct.GPIO_Mode=GPIO_Mode_IPU;
	GPIO_Init(GPIOE,&GPIO_struct);

	GPIO_EXTILineConfig(GPIO_PortSourceGPIOE,GPIO_PinSource2);

	EXTI_struct.EXTI_Line=EXTI_Line2;
	EXTI_struct.EXTI_Mode=EXTI_Mode_Interrupt;
	EXTI_struct.EXTI_Trigger=EXTI_Trigger_Falling;
	EXTI_struct.EXTI_LineCmd=ENABLE;
	EXTI_Init(&EXTI_struct);
	
	NVIC_PriorityGroupConfig(NVIC_PriorityGroup_1);
	NVIC_struct.NVIC_IRQChannel=EXTI2_IRQn;
	NVIC_struct.NVIC_IRQChannelPreemptionPriority=0;
	NVIC_struct.NVIC_IRQChannelSubPriority=0;
	NVIC_struct.NVIC_IRQChannelCmd=ENABLE;
	NVIC_Init(&NVIC_struct);

}


void EXTI2_IRQHandler(void){ 
	u8 num=0;

	OSIntEnter(); 
	if(EXTI_GetITStatus(EXTI_Line2)==SET){
		EXTI_ClearITPendingBit(EXTI_Line2);
		delay_ms(10);
		while(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_2)==Bit_RESET&&num<=250){ //��ֹ�û������ ��ʱ2.5��ִ��
			num++;
			delay_ms(10);
		}
		if(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_2)==Bit_RESET&&num>=250){
			
			struct InitData  init_data={
				170,			  			//״̬λAAΪ��ʼ����
				{192,168,1,88},		  		//IP��ַ
				{255,255,255,0},			//��������
				{192,168,1,1},				//����
				{192,168,1,77},			   	//��ƽ̨��ַ
				8282,						//��ƽ̨�˿ں�
				"www.vmuui.com",			//����������
				"VM001",					//�忨����
				1,							//վ��ID
				"123456",					//վ������
				"admin",					//�û�����
				{'\0'},						//session
				26000,						//��ѹ����
				18000,						//��ѹ����
				1000,						//������ֵ
				1,							//�Ƿ���Ҫ���� �����ڸ��µ�Ӧ�ó����޷���װ����ԭ���ĳ���ʱ�汾���͵��Ǳ��β�����
				0,							//�Ƿ��и���
				0,							//�����ļ���С
				266,						//Pwmֵ
				0,							//�Ƿ���
				0,							//����ѹ
				0,							//������
				0,							//���ʼ����
				0,							//���ǰ���
				0,							//������
				0,							//���γ�絥��
				0,							//����ܼ�
				0,							//����ID
				1							//�Ƿ����
			};
			delay_ms(10);
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&init_data,sizeof(init_data));
			FLASH_Lock();
			BEEP=0;
			delay_ms(1000);
			BEEP=1;	
		}
		while(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_2)==0);
	}
OSIntExit();
}
