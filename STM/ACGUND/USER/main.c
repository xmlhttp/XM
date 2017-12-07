#include "delay.h"
#include "sys.h"
#include "usart.h"
#include "usart2.h"	
#include "usart3.h"	
#include "usmart.h"
#include "malloc.h"
#include "string.h"
#include "dm9000.h"
#include "pwm.h"
#include "ADC.h"
#include "beep.h"
#include "lwip/netif.h"
#include "lwip_comm.h"
#include "lwipopts.h"
#include "includes.h"
#include "httpd.h"
#include "exti.h"
#include "gpios.h"
#include "tcp_client_demo.h"
#include "stmflash.h"
/************************************************
 技术支持：http://www.vmuui.com
 广州市劲驰互联网科技有限公司 
 作者：QQ469100943
************************************************/

#define countof(a)   (sizeof(a) / sizeof(*(a)))
//网络发送字段
extern char tcp_client_sendbuf[256];
//flash数据
extern struct InitData  my_data;
//是否允许发送数据完成
extern u8 IsSend;
//是否允许下载
extern u8 IsDown;
//连线标识位
extern u8 islink;
//充电标识位
//extern u8 PowerStatu;
//过渡状态标识位
extern u8 IsTran;
//电表标识符
extern u8 Is485;
//过压保护
u8 PowerSave=0;								
//检测任务*********************实时检测板卡信息任务
//任务优先级
#define POW_TASK_PRIO		9
//任务堆栈大小
#define POW_STK_SIZE		64
//任务堆栈
OS_STK	POW_TASK_STK[POW_STK_SIZE];
//任务函数
void POW_task(void *pdata); 


//数据发送任务****************轮询电表和超声波任务
//任务优先级
#define ReadDATA_TASK_PRIO		7
//任务堆栈大小
#define ReadDATA_STK_SIZE		64
//任务堆栈
OS_STK	ReadDATA_TASK_STK[ReadDATA_STK_SIZE];
//任务函数
void ReadDATA_task(void *pdata); 

//START任务*******************主任务
//任务优先级
#define START_TASK_PRIO		12
//任务堆栈大小
#define START_STK_SIZE		128
//任务堆栈
OS_STK START_TASK_STK[START_STK_SIZE];
//任务函数
void start_task(void *pdata); 

//任务优先级 ******************蜂鸣器任务
#define BEEP_TASK_PRIO		13
//任务堆栈大小
#define BEEP_STK_SIZE		64
//任务堆栈
OS_STK BEEP_TASK_STK[BEEP_STK_SIZE];
//任务函数
void beep_task(void *pdata); 

int main(void){
//	SCB->VTOR=FLASH_BASE|0x07800;						//地址偏移量，地址前面是IAP程序
	delay_init();	    								//延时函数初始化
	NVIC_PriorityGroupConfig(NVIC_PriorityGroup_2);		//设置NVIC中断分组2:2位抢占优先级，2位响应优先级	  
	uart_init(115200);	 								//串口1初始化为115200
	usmart_dev.init(72);							   	//串口控制台
	BEEP_Init();										//蜂鸣器初始化
	pwm_init();											//PWM初始化
	adc_init();											//ADC初始化
	exti_init();										//按键中断初始化
	tim5_init();										//Time5检测心跳计数
	uart2_init(9600);									//串口2初始化，读取电表
	uart3_init(9600);								   	//串口3初始化，读取超声波
	my_mem_init(SRAMIN);								//初始化内部内存池
//	my_mem_init(SRAMEX);								//初始化外部内存池
	GPIOS_Init();										//初始化GPIO
	IsDown=0;											//是否能够下载，控制系统启动完成后才能下载

	STMFLASH_Read(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
	my_data.Ispower=0;
	OSInit();

	while(my_data.Idc!=170){					//板卡初始化标识
		printf("板卡未初始化，请按还原键3秒以上\r\n");
		delay_ms(1000);
		STMFLASH_Read(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
	}
	printf("板卡初始化成功1111\r\n");
											//UCOS初始化
	while(lwip_comm_init()) 							//lwip初始化
	{
		printf("网络初始化失败!\r\n"); 					//lwip初始化失败
		GPIO_ResetBits(GPIOC,GPIO_Pin_10);
		delay_ms(1000);
	}
	printf("HTTP初始化\r\n");
	httpd_init(); 									   	//web服务初始化
	while(tcp_client_init()){ 							//初始化tcp_client(创建tcp_client线程)
		printf("客户端初始化失败!\r\n"); 
		GPIO_ResetBits(GPIOC,GPIO_Pin_10);
		delay_ms(1000);
	}
	OSTaskCreate(start_task,(void*)0,(OS_STK*)&START_TASK_STK[START_STK_SIZE-1],START_TASK_PRIO);
	OSStart(); 											//开启UCOS
	
}
 
//start任务
void start_task(void *pdata)
{
	OS_CPU_SR cpu_sr;	
	OSStatInit();																				//初始化统计任务
	OS_ENTER_CRITICAL();																		//关中断  
	OSTaskCreate(ReadDATA_task,(void*)0,(OS_STK*)&ReadDATA_TASK_STK[ReadDATA_STK_SIZE-1],ReadDATA_TASK_PRIO);
	OSTaskCreate(POW_task,(void*)0,(OS_STK*)&POW_TASK_STK[POW_STK_SIZE-1],POW_TASK_PRIO); 		//创建检测任务
	OSTaskCreate(beep_task,(void*)0,(OS_STK*)&BEEP_TASK_STK[BEEP_STK_SIZE-1],BEEP_TASK_PRIO);	//创建蜂鸣器任务
	OSTaskSuspend(OS_PRIO_SELF); 																//挂起start_task任务
	OS_EXIT_CRITICAL();  																		//开中断
}

//异常检查任务
void POW_task(void *pdata){
//	u8 t=0;
	u32 adcnum;
	u8 st;	
	while(1){
	/*	t++;
		if(t==10){
			t=0;
			printf("检测任务执行中\r\n");
		}
	*/
		if(!IsTran){
			adcnum= getAD();
		}
		if(my_data.Ispower&&!IsTran){
		//printf("执行充电中\r\n");																		//正在充电
		 //printf("充电中\r\n");
		 //判断是否输出调枪，充电|无过压保护|PC0降为低电压
		 if(my_data.Ispower&&!IsTran&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_RESET&&PowerSave==0){
		 	printf("输出跳枪\r\n");
		 	st=StopChage();	   //运行停止方法
			if(islink==0){
				if(st){
					SendStop(9,0);
				}else{
					SendStop(9,3);	
				}
			}else{
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			
			}
			continue;
		 }
		 //输入跳枪检测，充电|无过压保护|PC1变为高电压
		 if(my_data.Ispower&&!IsTran&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_SET&&PowerSave==0){
		 	printf("输入跳枪\r\n");
		 	st=StopChage();	   //运行停止方法
			if(islink==0){
				if(st){
					SendStop(4,0);
				}else{
					SendStop(4,4);	
				}
			}else{
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;
		 }
		  
		//CC断开，每个判断后面都要加个正在充电
		if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)!=Bit_RESET&&my_data.Ispower&&!IsTran){	//CC线不为低表示未连接
			printf("CC断开跳枪\r\n");
			st=StopChage();	   //运行停止方法
			if(islink==0){
				if(st){
					SendStop(2,0);
				}else{
					SendStop(2,5);	
				}
			}else{
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;
		}
		//车桩断开
		
		if((!(adcnum>2483&&adcnum<3202))&&my_data.Ispower&&!IsTran){
			printf("ADC跳枪\r\n");
			st=StopChage();	   //运行停止方法
			if(islink==0){
				if(st){
					SendStop(3,0);
				}else{
					SendStop(3,6);	
				}
			}else{
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;	
		}
		
		//电表掉线
		if(Is485==0&&my_data.Ispower&&!IsTran){
			printf("电表断线跳枪\r\n");
			st=StopChage();	   //运行停止方法
			if(islink==0){
				if(st){
					SendStop(8,0);
				}else{
					SendStop(8,7);	
				}
			}else{
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;	
		}

		//急停触发
		if(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_RESET&&my_data.Ispower&&!IsTran){
			printf("急停按钮按下\r\n");
			st=StopChage();	   //运行停止方法
			if(islink==0){
				if(st){
					SendStop(5,0);
				}else{
					SendStop(5,8);	
				}
			}else{
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;	
		}

		//过流检测
		if(my_data.Cele>my_data.Ele&&my_data.Ispower&&!IsTran){
			printf("过流跳枪\r\n");
			st=StopChage();	   //运行停止方法
			if(islink==0){
				if(st){
					SendStop(6,0);
				}else{
					SendStop(6,9);	
				}
			}else{
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			continue;
		}
		//过压检测
		if((my_data.Cvol>=my_data.Volup||my_data.Cvol<=my_data.Voldown)&&my_data.Ispower&&!IsTran){
			st=StopChage();	   //运行停止方法	
			printf("开启过压保护\r\n");			 											
			if(st){	
				PowerSave=1;
				if(islink==0){
					while(IsSend==0){
						delay_ms(1);
					}
					IsSend=0;
					memset(tcp_client_sendbuf,'\0',256);  
					//电度有变化发送服务端
					strcpy(tcp_client_sendbuf,"{\"type\":\"poststatus\",\"z\":1}\r\n");
					tcp_client_flag |= LWIP_SEND_DATA;
				}
			}else{
				SendStop(10,10);	
			}
			continue;	
		}
	

		}else if(!my_data.Ispower&&!IsTran){
		//	printf("执行未充电\r\n");	
			//停止充电
			//printf("空闲中\r\n");
			//取消过压保护
			if(my_data.Cvol<my_data.Volup&&my_data.Cvol>my_data.Voldown&&!my_data.Ispower&&!IsTran&&PowerSave){
				int st=StartChage(); 
				PowerSave=0;
			   	if(st==0){
					if(islink==0){
						while(IsSend==0){
							delay_ms(1);
						}
					IsSend=0;
					memset(tcp_client_sendbuf,'\0',100);  
					//电度有变化发送服务端
					strcpy(tcp_client_sendbuf,"{\"type\":\"poststatus\",\"z\":0}\r\n");
					tcp_client_flag |= LWIP_SEND_DATA;
				}else{
					u8 st=StopChage();	   //运行停止方法
					if(st){
						SendStop(7,0);
					}else{
						SendStop(7,11);	
					}
				}
			   	continue;
			}
		}
		}
		//过压保护状态拔枪
		if(PowerSave&&(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_SET||adcnum>3800)){
			PowerSave=0;
			SendStop(11,0);
		}

		//充断都执行
		//判断是否插枪
		if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_RESET&&adcnum<3800){
			delay_ms(1);
			GPIO_ResetBits(GPIOC,GPIO_Pin_6);
			delay_ms(2);
		}else{
			delay_ms(1);
			GPIO_SetBits(GPIOC,GPIO_Pin_6);
			delay_ms(2); 
		}
		
		//错误判断 急停|电表异常|网络异常
			
		if(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_RESET||islink||Is485==0){
			delay_ms(1);
			GPIO_ResetBits(GPIOC,GPIO_Pin_10);
			delay_ms(2); 
		}else{
			delay_ms(1);
		 	GPIO_SetBits(GPIOC,GPIO_Pin_10);
		 	delay_ms(2); 
		}

		OSTimeDlyHMSM(0,0,0,100);  //延时100ms	
	}
}

//数据发送任务
void ReadDATA_task(void *pdata){
	char send_buf[]={0x39,0x03,0x00,0x00,0x00,0x07,0x00,0xB0};							//发送电表数据
	u8 ch=0x55;																		   	//发送超声波数据
	u8 i,j=0;																		   	//控制参数
	while(1){		
		
		if(j==0){																		//获取电表数据
		//	printf("读取电表数据！\r\n");
		//	printf("***************************************************\r\n");	
		//	printf("PC2的值：%d，中断次数：%d，内存使用率为:%d%%,外存使用率：%d%%\r\n",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2),TIM5NUM,my_mem_perused(SRAMIN),my_mem_perused(SRAMIN));
			GPIO_SetBits(GPIOC,GPIO_Pin_5);	  											//PC5标识为允许发送数据
			delay_ms(1);
			for(i=0;i<countof(send_buf);i++){					 						//依次发送数据
				USART_SendData(USART2,send_buf[i]);
				while(USART_GetFlagStatus(USART2,USART_FLAG_TXE)==Bit_RESET);
			}
			delay_ms(2);
			GPIO_ResetBits(GPIOC,GPIO_Pin_5); 										   	//PC5还原
			TIM_Cmd(TIM7, ENABLE); 														//开启定时器，判断电表是否可用
			delay_ms(1000);			
		}else{ 																			//获取测距数据
		   	USART_SendData(USART3,ch);													//发送232数据获取距离
			while (USART_GetFlagStatus(USART3, USART_FLAG_TXE) == RESET);				//一直检测232数据是否发送完成
			delay_ms(200);
		}
		j++;
		j=j%6;
	//	OSTaskSuspend(OS_PRIO_SELF);
	}
}

void beep_task(void *pdata){															//蜂鸣器任务
	while(1){
		printf("播放声音\r\n");
		BEEP=0;	  																		//开蜂鸣器
		OSTimeDlyHMSM(0,0,1,0);															//延时1s，控制蜂鸣器响1s
		BEEP=1;	
		printf("播放声音完成\r\n");																		//关蜂鸣器
		if(IsDown==0){																	//如果系统启动成功，设置为可更新系统
			IsDown=1;																	//修改下载标识符
			printf("系统启动成功！\r\n********************欢迎使用劲驰网络交流单枪核心充电板********************\r\n");	
		}
		OSTaskSuspend(OS_PRIO_SELF);													 //休眠该任务
	}
}
