#include "sys.h"
//********************************************************************************  
//THUMBָ�֧�ֻ������
//�������·���ʵ��ִ�л��ָ��WFI 
__asm void WFI_SET(void){
	WFI;         
}
//�ر������ж�
__asm void INTX_DISABLE(void){
    CPSID I;         
}
//���������ж�
__asm void INTX_ENABLE(void){
    CPSIE I;         
}
//����ջ����ַ
//addr:ջ����ַ
__asm void MSR_MSP(u32 addr){
    MSR MSP, r0             //set Main Stack value
    BX r14
}

//intת��char
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



