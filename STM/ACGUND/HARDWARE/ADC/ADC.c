#include "ADC.h"
#include "usart.h"
void adc_init(){
	GPIO_InitTypeDef GPIO_struct;
	ADC_InitTypeDef ADC_struct;
	RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOA|RCC_APB2Periph_AFIO|RCC_APB2Periph_ADC1,ENABLE);	
	RCC_ADCCLKConfig(RCC_PCLK2_Div6);
	//初始化GPIO
	GPIO_struct.GPIO_Speed=GPIO_Speed_50MHz;
	GPIO_struct.GPIO_Pin=GPIO_Pin_5;	   //发送
	GPIO_struct.GPIO_Mode=GPIO_Mode_AIN;
	GPIO_Init(GPIOA,&GPIO_struct);

	//ADC结构体定义
	ADC_struct.ADC_Mode=ADC_Mode_Independent;
	ADC_struct.ADC_ScanConvMode=DISABLE;
	ADC_struct.ADC_ContinuousConvMode=DISABLE;
	ADC_struct.ADC_ExternalTrigConv=ADC_ExternalTrigConv_None;
	ADC_struct.ADC_DataAlign=ADC_DataAlign_Right;
	ADC_struct.ADC_NbrOfChannel=1;
	ADC_Init(ADC1,&ADC_struct);

	ADC_RegularChannelConfig(ADC1,ADC_Channel_5,1,ADC_SampleTime_239Cycles5);
	ADC_Cmd(ADC1,ENABLE);
	
	ADC_ResetCalibration(ADC1);
	while(ADC_GetResetCalibrationStatus(ADC1));
	
	ADC_StartCalibration(ADC1);
	while(ADC_GetCalibrationStatus(ADC1));

	ADC_SoftwareStartConvCmd(ADC1,ENABLE);

}
u32 getAD(){
	u8 i=0;
	u16 j=0;
	u32 ad=0,temp;
	while(i<50){
		temp=0;
		ADC_SoftwareStartConvCmd(ADC1,ENABLE);
		while(!ADC_GetFlagStatus(ADC1,ADC_FLAG_EOC));
		temp=ADC_GetConversionValue(ADC1);
		if(temp>2048){	  //过滤干扰电平
		 	ad=ad+temp;
			i++;
		}
		if(j>1000){
			return 0;
		}
		j++;
	}
	ad=ad/i; 
	return ad;
}
//生成随机数
u32 getRand(){
	u8 i=0;
	u32 ad=0;
	while(i<50){
		ADC_SoftwareStartConvCmd(ADC1,ENABLE);
		while(!ADC_GetFlagStatus(ADC1,ADC_FLAG_EOC));
		ad+=ADC_GetConversionValue(ADC1);
		i++;
	}
	return ad;
}
