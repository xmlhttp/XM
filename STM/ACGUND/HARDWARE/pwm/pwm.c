#include "pwm.h"
void pwm_init(){

	TIM_TimeBaseInitTypeDef TIM_struct;
	GPIO_InitTypeDef GPIO_struct;
	TIM_OCInitTypeDef TIM_OCstruct;

	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM3,ENABLE);
	RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOC,ENABLE);
	RCC_APB2PeriphClockCmd(RCC_APB2Periph_AFIO,ENABLE);

	TIM_struct.TIM_Period=1000-1;
	TIM_struct.TIM_Prescaler=72-1;
	TIM_struct.TIM_ClockDivision=0;
	TIM_struct.TIM_CounterMode=TIM_CounterMode_Up;
	TIM_TimeBaseInit(TIM3,&TIM_struct);
	TIM_Cmd(TIM3,ENABLE);
	
	GPIO_struct.GPIO_Speed=GPIO_Speed_50MHz;
	GPIO_struct.GPIO_Pin=GPIO_Pin_7;
	GPIO_struct.GPIO_Mode=GPIO_Mode_AF_PP;
	GPIO_Init(GPIOC,&GPIO_struct);

	GPIO_PinRemapConfig(GPIO_FullRemap_TIM3,ENABLE);   //管脚全映射

	TIM_OCstruct.TIM_OCMode=TIM_OCMode_PWM1;
	TIM_OCstruct.TIM_OutputState=TIM_OutputState_Enable;//输出使能
	TIM_OCstruct.TIM_OCPolarity=TIM_OCPolarity_High;	   //输出极性
	TIM_OC2Init(TIM3,&TIM_OCstruct);

	TIM_OC2PreloadConfig(TIM3,TIM_OCPreload_Enable);
	TIM_SetCompare2(TIM3,1000);

}



