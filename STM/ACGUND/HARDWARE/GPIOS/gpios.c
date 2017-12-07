#include "gpios.h"
#include "delay.h"

/*初始化GPIO并设置默认值	    
 *输入 全部是上拉
 *PC1:充电接触器反馈
 *PC2:CC线，确认连接
 *PE4:急停
 *PE3:备用
 *输出 全部推挽
 *PC0:充电位
 *PC6:插枪指示灯
 *PC8:备用
 *PC10:故障指示灯
 */
void GPIOS_Init(void){

	GPIO_InitTypeDef GPIO_struct;
	RCC_APB2PeriphClockCmd(RCC_APB2Periph_AFIO|RCC_APB2Periph_GPIOC|RCC_APB2Periph_GPIOE,ENABLE);
	//初始化输出
	GPIO_struct.GPIO_Speed=GPIO_Speed_50MHz;
	GPIO_struct.GPIO_Pin=GPIO_Pin_0|GPIO_Pin_8|GPIO_Pin_10|GPIO_Pin_6;
	GPIO_struct.GPIO_Mode=GPIO_Mode_Out_PP;
	GPIO_Init(GPIOC,&GPIO_struct);
	//初始化输入
	//PC1充电接触反馈器，检测充电是否成功，低电平处于充电状态 ，上拉输入
	GPIO_struct.GPIO_Pin=GPIO_Pin_1|GPIO_Pin_2;
	GPIO_struct.GPIO_Mode=GPIO_Mode_IPU;
	GPIO_Init(GPIOC,&GPIO_struct);
	//PE4急停，检测急停按钮是否按下，低电平按下，上拉输入 |PE3备用
	GPIO_struct.GPIO_Pin=GPIO_Pin_4|GPIO_Pin_3;
	GPIO_struct.GPIO_Mode=GPIO_Mode_IPU;
	GPIO_Init(GPIOE,&GPIO_struct);
	//默认输出设置
	GPIO_ResetBits(GPIOC,GPIO_Pin_0);
	GPIO_SetBits(GPIOC,GPIO_Pin_8|GPIO_Pin_10|GPIO_Pin_6);
}

