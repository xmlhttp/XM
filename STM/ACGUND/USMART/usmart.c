#include "usmart.h"
#include "usart.h"
#include "lwip_comm.h" 
#include "sys.h"
#include "malloc.h"
#include "tcp_client_demo.h"
#include "usart2.h"	
#include "usart3.h"	
#include "beep.h"
#include "ADC.h"
#include "stmflash.h"

extern int TIM5NUM;
extern u8 islink;
extern float WDIS;
extern u8 Is485;
//flash����
extern struct InitData  my_data;

#if USMART_ENTIMX_SCAN==1
//��λruntime
//��Ҫ��������ֲ����MCU�Ķ�ʱ�����������޸�
void usmart_reset_runtime(void)
{
	TIM_ClearFlag(TIM4,TIM_FLAG_Update);//����жϱ�־λ 
	TIM_SetAutoreload(TIM4,0XFFFF);//����װ��ֵ���õ����
	TIM_SetCounter(TIM4,0);		//��ն�ʱ����CNT
	usmart_dev.runtime=0;	
}
//���runtimeʱ��
//����ֵ:ִ��ʱ��,��λ:0.1ms,�����ʱʱ��Ϊ��ʱ��CNTֵ��2��*0.1ms
//��Ҫ��������ֲ����MCU�Ķ�ʱ�����������޸�
u32 usmart_get_runtime(void)
{
	if(TIM_GetFlagStatus(TIM4,TIM_FLAG_Update)==SET)//�������ڼ�,�����˶�ʱ�����
	{
		usmart_dev.runtime+=0XFFFF;
	}
	usmart_dev.runtime+=TIM_GetCounter(TIM4);
	return usmart_dev.runtime;		//���ؼ���ֵ
}
//��������������,��USMART����,�ŵ�����,����������ֲ. 
//��ʱ��4�жϷ������	 
void TIM4_IRQHandler(void)
{ 		    		  			    
	if(TIM_GetITStatus(TIM4,TIM_IT_Update)==SET)//����ж�
	{
		usmart_dev.scan();	//ִ��usmartɨ��	
		TIM_SetCounter(TIM4,0);		//��ն�ʱ����CNT
		TIM_SetAutoreload(TIM4,100);//�ָ�ԭ��������		    				   				     	    	
	}				   
	TIM_ClearITPendingBit(TIM4,TIM_IT_Update);  //����жϱ�־λ    
}
//ʹ�ܶ�ʱ��4,ʹ���ж�.
void Timer4_Init(u16 arr,u16 psc)
{
    TIM_TimeBaseInitTypeDef  TIM_TimeBaseStructure;
	NVIC_InitTypeDef NVIC_InitStructure;

	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM4, ENABLE); //TIM4ʱ��ʹ�� 
 
	//TIM4��ʼ������
 	TIM_TimeBaseStructure.TIM_Period = arr; //��������һ�������¼�װ�����Զ���װ�ؼĴ������ڵ�ֵ	 ������5000Ϊ500ms
	TIM_TimeBaseStructure.TIM_Prescaler =psc; //����������ΪTIMxʱ��Ƶ�ʳ�����Ԥ��Ƶֵ  10Khz�ļ���Ƶ��  
	TIM_TimeBaseStructure.TIM_ClockDivision = 0; //����ʱ�ӷָ�:TDTS = Tck_tim
	TIM_TimeBaseStructure.TIM_CounterMode = TIM_CounterMode_Up;  //TIM���ϼ���ģʽ
	TIM_TimeBaseInit(TIM4, &TIM_TimeBaseStructure); //����TIM_TimeBaseInitStruct��ָ���Ĳ�����ʼ��TIMx��ʱ�������λ
 
	TIM_ITConfig( TIM4, TIM_IT_Update|TIM_IT_Trigger, ENABLE );//TIM4 ������£������ж�

	//TIM4�жϷ�������
	NVIC_InitStructure.NVIC_IRQChannel = TIM4_IRQn;  //TIM3�ж�
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 3;  //��ռ���ȼ�03��
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;  //�����ȼ�3��
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE; //IRQͨ����ʹ��
	NVIC_Init(&NVIC_InitStructure);  //����NVIC_InitStruct��ָ���Ĳ�����ʼ������NVIC�Ĵ���

	TIM_Cmd(TIM4, ENABLE);  //ʹ��TIM4							 
}
#endif
////////////////////////////////////////////////////////////////////////////////////////
//��ʼ�����ڿ�����
//sysclk:ϵͳʱ�ӣ�Mhz��
void usmart_init(u8 sysclk)
{
#if USMART_ENTIMX_SCAN==1
	Timer4_Init(1000,(u32)sysclk*100-1);//��Ƶ,ʱ��Ϊ10K ,100ms�ж�һ��,ע��,����Ƶ�ʱ���Ϊ10Khz,�Ժ�runtime��λ(0.1ms)ͬ��.
#endif
	usmart_dev.sptype=1;	//ʮ��������ʾ����
}		
//��str�л�ȡ������,id,��������Ϣ
//*str:�ַ���ָ��.
//����ֵ:0,ʶ��ɹ�;����,�������.
u8 usmart_cmd_rec(u8*str) 
{
	u8 sta,i,rval;//״̬	 
	u8 rpnum,spnum;
	u8 rfname[MAX_FNAME_LEN];//�ݴ�ռ�,���ڴ�Ž��յ��ĺ�����  
	u8 sfname[MAX_FNAME_LEN];//��ű��غ�����
	sta=usmart_get_fname(str,rfname,&rpnum,&rval);//�õ����յ������ݵĺ���������������	  
	if(sta)return sta;//����
	for(i=0;i<usmart_dev.fnum;i++)
	{
		sta=usmart_get_fname((u8*)usmart_dev.funs[i].name,sfname,&spnum,&rval);//�õ����غ���������������
		if(sta)return sta;//���ؽ�������	  
		if(usmart_strcmp(sfname,rfname)==0)//���
		{
			if(spnum>rpnum)return USMART_PARMERR;//��������(���������Դ����������)
			usmart_dev.id=i;//��¼����ID.
			break;//����.
		}	
	}
	if(i==usmart_dev.fnum)return USMART_NOFUNCFIND;	//δ�ҵ�ƥ��ĺ���
 	sta=usmart_get_fparam(str,&i);					//�õ�������������	
	if(sta)return sta;								//���ش���
	usmart_dev.pnum=i;								//����������¼
    return USMART_OK;
}
//usamrtִ�к���
//�ú�����������ִ�дӴ����յ�����Ч����.
//���֧��10�������ĺ���,����Ĳ���֧��Ҳ������ʵ��.�����õĺ���.һ��5�����ҵĲ����ĺ����Ѿ����ټ���.
//�ú������ڴ��ڴ�ӡִ�����.��:"������(����1������2...����N)=����ֵ".����ʽ��ӡ.
//����ִ�еĺ���û�з���ֵ��ʱ��,����ӡ�ķ���ֵ��һ�������������.
void usmart_exe(void)
{
	u8 id;
//	u32 res;		   
//	u32 temp[MAX_PARM];//����ת��,ʹ֧֮�����ַ��� 
	u8 sfname[MAX_FNAME_LEN];//��ű��غ�����
	u8 pnum,rval;
	id=usmart_dev.id;
	if(id>=usmart_dev.fnum)return;//��ִ��.
	usmart_get_fname((u8*)usmart_dev.funs[id].name,sfname,&pnum,&rval);//�õ����غ�����,���������� 
	printf("\r\n���У�%s",sfname);//�����Ҫִ�еĺ�����
	usmart_reset_runtime();	//��ʱ������,��ʼ��ʱ
	switch(usmart_dev.pnum)
	{
		case 0://�޲���(void����)											  
			(*(u32(*)())usmart_dev.funs[id].func)();
			break;
	}
	printf("*************************ִ�����**********************\r\n");
}
//usmartɨ�躯��
//ͨ�����øú���,ʵ��usmart�ĸ�������.�ú�����Ҫÿ��һ��ʱ�䱻����һ��
//�Լ�ʱִ�дӴ��ڷ������ĸ�������.
//�������������ж��������,�Ӷ�ʵ���Զ�����.

void usmart_scan(void)
{
	u8 sta,len;  
	if(USART_RX_STA&0x8000)//���ڽ�����ɣ�
	{					   
		len=USART_RX_STA&0x3fff;	//�õ��˴ν��յ������ݳ���
		USART_RX_BUF[len]='\0';	//��ĩβ���������. 
		sta=usmart_dev.cmd_rec(USART_RX_BUF);//�õ�����������Ϣ
		if(sta==0)usmart_dev.exe();	//ִ�к��� 
		else 
		{  
			len=0;//usmart_sys_cmd_exe(USART_RX_BUF);
			if(len!=USMART_FUNCERR)sta=len;
			if(sta)
			{
				switch(sta)
				{
					case USMART_FUNCERR:
					//	printf("��������!\r\n");   			
						break;	
					case USMART_PARMERR:
					//	printf("��������!\r\n");   			
						break;				
					case USMART_PARMOVER:
					//	printf("����̫��!\r\n");   			
						break;		
					case USMART_NOFUNCFIND:
					//	printf("δ�ҵ�ƥ��ĺ���!\r\n");   			
						break;		
				}
			}
		}
		USART_RX_STA=0;//״̬�Ĵ������	    
	}
}

#if USMART_USE_WRFUNS==1 	//���ʹ���˶�д����
//��ȡָ����ַ��ֵ		 
u32 read_addr(u32 addr)
{
	return *(u32*)addr;//	
}
//��ָ����ַд��ָ����ֵ		 
void write_addr(u32 addr,u32 val)
{
	*(u32*)addr=val; 	
}

//��ȡip
void getip(){
	printf("\r\n---------------------��ȡIP��ַ---------------------\r\n");
   	printf("MAC��ַ:......................%d.%d.%d.%d.%d.%d\r\n",lwipdev.mac[0],lwipdev.mac[1],lwipdev.mac[2],lwipdev.mac[3],lwipdev.mac[4],lwipdev.mac[5]);
	printf("IP��ַ........................%d.%d.%d.%d\r\n",lwipdev.ip[0],lwipdev.ip[1],lwipdev.ip[2],lwipdev.ip[3]);
	printf("��������......................%d.%d.%d.%d\r\n",lwipdev.netmask[0],lwipdev.netmask[1],lwipdev.netmask[2],lwipdev.netmask[3]);
	printf("����..........................%d.%d.%d.%d\r\n",lwipdev.gateway[0],lwipdev.gateway[1],lwipdev.gateway[2],lwipdev.gateway[3]);
	
}
//��ȡ������Ϣ
void getinfo(){
	printf("\r\n----------------------��Ȩ��Ϣ----------------------\r\n");
	printf("��ӭʹ�þ������罻����ǹ���ĳ��� ��Ȩ���� # 2017 #\r\n");
	printf("����֧�֣�http://www.vmuui.com \r\n");
	printf("��ϵQQ��568615539   �绰��13829719806\r\n");
	printf("�������ӣ�");
	if(islink){
		printf("����-1 ");	
	}else{
		printf("����-0 ");	
	}
	printf(" ���״̬��");
	if(Is485){
		printf("����-1 ");
	}else{
		printf("�쳣-0 ");
	}
	printf(" ADC�ɼ���%f V\r\n",getAD()*3.3/4096);
	printf("�忨�ڴ�ʹ���ʣ�%d%%  ���������%d��  ����:%.2f cm\r\n",my_mem_perused(SRAMIN),TIM5NUM,WDIS);
	printf("������:%.2fKW*H  ��ѹ��%.2fV  ������%.3fA\r\n",(float)my_data.Endp/10,(float)my_data.Cvol/100,(float)my_data.Cele/1000);	
}
//��ȡGPIO״̬
void getgpio(){
	printf("\r\n----------------------��ȡGPIO״̬----------------------\r\n");
	printf("���룺\r\nPC1�ĵ�ƽ�ǣ�%d ********���Ӵ����������͵�ƽ��Ч\r\nPC2�ĵ�ƽ�ǣ�%d ********CC�����ߣ��͵�ƽ��Ч\r\nPE4�ĵ�ƽ�ǣ�%d ********��ͣ���أ��͵�ƽ��Ч\r\nPE3�ĵ�ƽ�ǣ�%d ********����\r\n�����\r\nPC0�ĵ�ƽ�ǣ�%d ********���λ���ߵ�ƽΪ���\r\nPC6�ĵ�ƽ�ǣ�%d ********����ָʾ�ƣ��͵�ƽΪ����\r\nPC8�ĵ�ƽ�ǣ�%d ********����\r\nPC10�ĵ�ƽ�ǣ�%d *******����ָʾ�ƣ��͵�ƽ�й���\r\n",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2),GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4),GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_3),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_6),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_8),GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_10));
}


#endif













