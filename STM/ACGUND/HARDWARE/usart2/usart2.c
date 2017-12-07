#include "sys.h"
#include "delay.h"
#include "usart2.h"	 
#include "mod.h"
#include "tcp_client_demo.h"
#include "stmflash.h" 
	 
////////////////////////////////////////////////////////////////////////////////// 	 
//如果使用ucos,则包括下面的头文件即可.
#if SYSTEM_SUPPORT_OS
#include "includes.h"					//ucos 使用	  
#endif

int BUFLENGTH=0; //当前字符串长度
u8 read_buf[19] = {'\0'};

u8 Is485=1;//电表正常标准符，1正常|0异常
u8 coun485=0;//电表掉线次数统计
//flash数据	stmflash.h
extern struct InitData  my_data;
//网络发送字符串
extern char tcp_client_sendbuf[256];
//是否允许发送数据完成
extern u8 IsSend;
//是否连接
extern u8 islink;

//定时器7初始化，用于检测485是否正常
void tim7_init(){
	TIM_TimeBaseInitTypeDef  TIM_TimeBaseStructure;
	NVIC_InitTypeDef NVIC_InitStructure;
	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM7, ENABLE); //时钟使能

	//定时器TIM7初始化
	TIM_TimeBaseStructure.TIM_Period = 1000; //设置在下一个更新事件装入活动的自动重装载寄存器周期的值	
	TIM_TimeBaseStructure.TIM_Prescaler =35999; //设置用来作为TIMx时钟频率除数的预分频值
	TIM_TimeBaseStructure.TIM_ClockDivision = TIM_CKD_DIV1; //设置时钟分割:TDTS = Tck_tim
	TIM_TimeBaseStructure.TIM_CounterMode = TIM_CounterMode_Up;  //TIM向上计数模式
	TIM_TimeBaseInit(TIM7, &TIM_TimeBaseStructure); //根据指定的参数初始化TIMx的时间基数单位
	TIM_ClearFlag(TIM7, TIM_FLAG_Update);
	TIM_ITConfig(TIM7,TIM_IT_Update,ENABLE ); //使能指定的TIM7中断,允许更新中断

	//中断优先级NVIC设置
	NVIC_InitStructure.NVIC_IRQChannel = TIM7_IRQn;  //TIM5中断
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority = 3;  //先占优先级0级
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;  //从优先级3级
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE; //IRQ通道被使能
	NVIC_Init(&NVIC_InitStructure);  //初始化NVIC寄存器

	TIM_ClearITPendingBit(TIM7, TIM_IT_Update  );  //清除TIMx更新中断标志 
	TIM_Cmd(TIM7, DISABLE);	 
	
}

//初始化485
void uart2_init(u32 bound){
	//GPIO端口设置
	GPIO_InitTypeDef GPIO_InitStructure;
	USART_InitTypeDef USART_InitStructure;
	NVIC_InitTypeDef NVIC_InitStructure;
	 
	RCC_APB2PeriphClockCmd(RCC_APB2Periph_GPIOA|RCC_APB2Periph_GPIOC|RCC_APB2Periph_AFIO, ENABLE);	//使能USART2，GPIOA时钟
  	RCC_APB1PeriphClockCmd(RCC_APB1Periph_USART2,ENABLE);
	//USART2_TX   GPIOA.2
	GPIO_InitStructure.GPIO_Pin = GPIO_Pin_2; //PA.2
	GPIO_InitStructure.GPIO_Speed = GPIO_Speed_50MHz;
	GPIO_InitStructure.GPIO_Mode = GPIO_Mode_AF_PP;	//复用推挽输出
	GPIO_Init(GPIOA, &GPIO_InitStructure);//初始化GPIOA.2
   
	//USART2_RX	  GPIOA.3初始化
	GPIO_InitStructure.GPIO_Pin = GPIO_Pin_3;//PA3
	GPIO_InitStructure.GPIO_Mode = GPIO_Mode_IN_FLOATING;//浮空输入
	GPIO_Init(GPIOA, &GPIO_InitStructure);//初始化GPIOA.3  
	//初始化急停输入GPIOC.1
	/*GPIO_InitStructure.GPIO_Pin = GPIO_Pin_1;//PC1
	GPIO_Init(GPIOC, &GPIO_InitStructure);
	 */
	GPIO_InitStructure.GPIO_Pin = GPIO_Pin_5;//PC5
	GPIO_InitStructure.GPIO_Mode=GPIO_Mode_Out_PP;	  //半双工引脚
	GPIO_Init(GPIOC,&GPIO_InitStructure);
	//充电管脚
	/*GPIO_InitStructure.GPIO_Pin = GPIO_Pin_0;//PC0
	GPIO_Init(GPIOC, &GPIO_InitStructure);*/
	//Usart1 NVIC 配置							 
	NVIC_InitStructure.NVIC_IRQChannel = USART2_IRQn;
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority=3;//抢占优先级3
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;		//子优先级3
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE;			//IRQ通道使能
	NVIC_Init(&NVIC_InitStructure);	//根据指定的参数初始化VIC寄存器
  
   //USART 初始化设置

	USART_InitStructure.USART_BaudRate = bound;//串口波特率
	USART_InitStructure.USART_WordLength = USART_WordLength_8b;//字长为8位数据格式
	USART_InitStructure.USART_StopBits = USART_StopBits_1;//一个停止位
	USART_InitStructure.USART_Parity = USART_Parity_No;//无奇偶校验位
	USART_InitStructure.USART_HardwareFlowControl = USART_HardwareFlowControl_None;//无硬件数据流控制
	USART_InitStructure.USART_Mode = USART_Mode_Rx | USART_Mode_Tx;	//收发模式
	USART_Init(USART2, &USART_InitStructure); //初始化串口2
	USART_ITConfig(USART2, USART_IT_RXNE, ENABLE);//开启串口接受中断
	USART_Cmd(USART2, ENABLE);                    //使能串口2
	USART_ClearFlag(USART2,USART_FLAG_TC);
	GPIO_ResetBits(GPIOB,GPIO_Pin_5); 
	tim7_init();
}



//定时器7中断服务程序
void TIM7_IRQHandler(void){
	OSIntEnter();
	if (TIM_GetITStatus(TIM7, TIM_IT_Update) != RESET){ //检查TIM7更新中断发生与否
		TIM_ClearITPendingBit(TIM7, TIM_IT_Update  );  	//清除TIMx更新中断标志 
		TIM_Cmd(TIM7, DISABLE);							//停止使能
		TIM_SetCounter(TIM7, 0);					  	//定时器清零
		coun485++;										//掉线累加
		if(coun485>5){								   	//连续5次检查不到电表数据
			Is485=0;									//更改电表故障标识
			coun485=0;
		}
		BUFLENGTH=0;									//清除电表数据标识
		printf("电表读取超时，可能是线路没接好或者电表故障！\r\n");
	}
	OSIntExit();
}





//串口2中断
/*
判断接收数据的第一位是否为地址位0x01，第二位是否为功能码0x03,第三位为接收的数据长度0x06,最后两位CRC校验位，共计11位
*/
void USART2_IRQHandler(void){
	OSIntEnter();
	if(USART_GetFlagStatus(USART2, USART_FLAG_ORE) != Bit_RESET){
		USART_ClearFlag(USART2, USART_FLAG_ORE);
		if(BUFLENGTH==0){
			u8 temp=USART_ReceiveData(USART2);
			if(temp==57){
				read_buf[BUFLENGTH]=temp;
				BUFLENGTH++;	
			}
		}else if(BUFLENGTH==1){
			u8 temp=USART_ReceiveData(USART2);
			if(temp==3){
				read_buf[BUFLENGTH]=temp;
				BUFLENGTH++;	
			}
		}else{
			read_buf[BUFLENGTH]=USART_ReceiveData(USART2);
			BUFLENGTH++;
		}
	}
	if(USART_GetITStatus(USART2, USART_IT_RXNE) != Bit_RESET){
		USART_ClearITPendingBit(USART2, USART_IT_RXNE);
		if(BUFLENGTH==0){
			u8 temp=USART_ReceiveData(USART2);
			if(temp==57){
				read_buf[BUFLENGTH]=temp;
				BUFLENGTH++;	
			}
		}else if(BUFLENGTH==1){
			u8 temp=USART_ReceiveData(USART2);
			if(temp==3){
				read_buf[BUFLENGTH]=temp;
				BUFLENGTH++;	
			}
		}else{
			read_buf[BUFLENGTH]=USART_ReceiveData(USART2);
			BUFLENGTH++;
		}
  	}
	if(BUFLENGTH==19&&CHK_CRC16(read_buf,19)==1){
		
	//	printf("电能:%.2fKW*H，电压：%.2fV，电流：%.3fA\r\n",(float)((0xff & read_buf[6])<<24|(0xff & read_buf[5])<<16|(0xff & read_buf[3])<<8|(0xff & read_buf[4]))/10,(float)((0xff & read_buf[7])<<8|(0xff & read_buf[8]))/100,(float)((0xff & read_buf[11])<<8|(0xff & read_buf[12])|(0xff & read_buf[9])<<8|(0xff & read_buf[10]))/1000);
		u32 cpower;
		TIM_ClearITPendingBit(TIM7, TIM_IT_Update  );  //清除TIMx更新中断标志 
		TIM_Cmd(TIM7, DISABLE);
		TIM_SetCounter(TIM7, 0);
		Is485=1;
		coun485=0;
		cpower=(u32)((0xff & read_buf[6])<<24|(0xff & read_buf[5])<<16|(0xff & read_buf[3])<<8|(0xff & read_buf[4]));
		//充电有订单未结算 ,电表异常
		if(cpower<my_data.Endp&&my_data.Ispower==1&&my_data.Orderid!=0&&my_data.Isend==1){
			u8 t;
			my_data.Cpower=my_data.Cpower+1;
			my_data.Endp=cpower;
			my_data.Cvol=(u16)((0xff & read_buf[7])<<8|(0xff & read_buf[8]));
			my_data.Cele=(u32)((0xff & read_buf[11])<<8|(0xff & read_buf[12])|(0xff & read_buf[9])<<8|(0xff & read_buf[10]));
			t=StopChage();
			if(t==0){
				SendStop(23,0);
			}else{
				SendStop(23,13);
			}

		}else if(cpower>my_data.Endp){
			my_data.Endp=cpower;
			my_data.Cvol=(u16)((0xff & read_buf[7])<<8|(0xff & read_buf[8]));
			my_data.Cele=(u32)((0xff & read_buf[11])<<8|(0xff & read_buf[12])|(0xff & read_buf[9])<<8|(0xff & read_buf[10]));
			if(my_data.Ispower==1&&my_data.Orderid!=0&&my_data.Isend==1){
				my_data.Cpower=	my_data.Endp-my_data.Starp;
			}

			if(islink==0&&my_data.Money<=(float)(my_data.Cpower*my_data.Uint)/10&&my_data.Ispower==1&&my_data.Orderid!=0&&my_data.Isend==1){
				u8 t=StopChage();
				if(t==0){
					SendStop(24,0);
				}else{
					 SendStop(24,14);
				}
			}else if(islink==1&&my_data.Money<=(float)(my_data.Cpower*my_data.Uint)/10&&my_data.Ispower==1&&my_data.Orderid!=0&&my_data.Isend==1){
				my_data.Ispower=0;
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();

			}else{
				//存储
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
				//发送
				if(islink==0){
					while(IsSend==0){
				 		delay_ms(1);
					}
					IsSend=0;
					memset(tcp_client_sendbuf,'\0',256); 
					//发送普通提交请求
					sprintf(tcp_client_sendbuf,"{\"type\":\"postdata\",\"w\":%d,\"v\":%d,\"a\":%d,\"Ispower\":%d,\"Orderid\":%d,\"isend\":%d,\"Cpower\":%d}\r\n",my_data.Endp,my_data.Cvol,my_data.Cele,my_data.Ispower,my_data.Orderid,my_data.Isend,my_data.Cpower);
					tcp_client_flag |= LWIP_SEND_DATA;	
				}
			}

		} 
		BUFLENGTH=0;		
  	}
	OSIntExit(); 	   
}

