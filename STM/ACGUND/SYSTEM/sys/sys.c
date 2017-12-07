#include "sys.h"
//********************************************************************************  
//THUMB指令不支持汇编内联
//采用如下方法实现执行汇编指令WFI 
__asm void WFI_SET(void){
	WFI;         
}
//关闭所有中断
__asm void INTX_DISABLE(void){
    CPSID I;         
}
//开启所有中断
__asm void INTX_ENABLE(void){
    CPSIE I;         
}
//设置栈顶地址
//addr:栈顶地址
__asm void MSR_MSP(u32 addr){
    MSR MSP, r0             //set Main Stack value
    BX r14
}

//int转换char
void change(int num, char *str){
    int p = 0;
    int tmp = num;
    while(tmp)
    {
        p++;
        tmp /= 10;
    }
    if(num > 0)p--;
    tmp = num > 0 ? num : -num;
    while(tmp)
    {
        str[p] = (tmp%10) + '0';
        p--;
        tmp /= 10;
    }
	if(num==0)str[0]='0';
    if(num < 0)str[0] = '-';
}



