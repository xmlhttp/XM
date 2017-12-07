#ifndef _ADC_H
#define	_ADC_H
#include "stm32f10x.h"
void adc_init(void);
u32 getAD(void);
u32 getRand(void);
#endif
