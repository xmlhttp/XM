#include "delay.h"
#include "tcp_client_demo.h"
#include "lwip/opt.h"
#include "lwip_comm.h"
#include "lwip/lwip_sys.h"
#include "lwip/api.h"
#include "includes.h"
#include "ADC.h"
#include "pwm.h"
#include "beep.h"
#include "cJSON.h"  
#include "http.h"
#include "stmflash.h"  
struct netconn *tcp_clientconn;					//TCP CLIENT�������ӽṹ��
u8 tcp_client_recvbuf[TCP_CLIENT_RX_BUFSIZE];	//TCP�ͻ��˽������ݻ�����
char tcp_client_sendbuf[256];					//���͸�������ַ�����
u8 tcp_client_flag;								//TCP�ͻ������ݷ��ͱ�־λ
//flash����	stmflash.h
extern struct InitData  my_data;
//�汾��stmflash.h
extern u8 Ver;
					
extern int DISTYPE;				  				//���ģ��״̬
extern u8 Is485;								//����ʶ��
int TIM5NUM;									//TIM5�������ۼ��жϴ���,���Ƿ�ʹ��

u8 IsTran=0;									//�Ƿ�Ϊ��ͣ������
//u8 istim5;										//TIM5�Ƿ��ڶ�ʱ״̬,Ŀǰ����֪����λ�ȡ��ʱ��ʹ��״̬
u8 islink=1;								   	//�Ƿ�����ƽ̨����˵�Ƿ��Ѿ�����ƽ̨
u8 IsSend;										//�Ƿ��������������
u8 IsDown=0;									//�Ƿ��������أ�ϵͳ�����ɹ������ĸ��ֶ�

//TCP�ͻ�������
#define TCPCLIENT_PRIO		4
//�����ջ��С
#define TCPCLIENT_STK_SIZE	300
//�����ջ
OS_STK TCPCLIENT_TASK_STK[TCPCLIENT_STK_SIZE];

void any(u8*d);									//ע��������·����ݴ�����
//tcp�ͻ���������
static void tcp_client_thread(void *arg)
{
	OS_CPU_SR cpu_sr;
	u32 data_len = 0;
	struct pbuf *q;
	err_t err,recv_err;
	static ip_addr_t server_ipaddr;
	static u16_t 		 server_port;
//	istim5=0;
	LWIP_UNUSED_ARG(arg);
	if((*(u8*)(FLASH_ADDR))==170){
		 server_port = my_data.Port;
	}else{
		 server_port = REMOTE_PORT;
	}
	

	IP4_ADDR(&server_ipaddr, lwipdev.remoteip[0],lwipdev.remoteip[1], lwipdev.remoteip[2],lwipdev.remoteip[3]);
	
	while (1) 
	{
		tcp_clientconn = netconn_new(NETCONN_TCP);  //����һ��TCP����
		err = netconn_connect(tcp_clientconn,&server_ipaddr,server_port);//���ӷ�����
		//�ȴ����ӽ������
		delay_ms(1000);
		if(err != ERR_OK){//����ֵ������ERR_OK,ɾ��tcp_clientconn����  
			netconn_delete(tcp_clientconn);
			delay_ms(1000);
		}else if (err == ERR_OK){    //���������ӵ�����
			struct netbuf *recvbuf;
			//���������������ö�ʱ������ʱ������
			islink=0;
			TIM5NUM=0;
		/*	if(istim5==0){
				istim5=1;
		//		printf("�ж�����#1\r\n");
				TIM5NUM=0;
				
			} */
			tcp_clientconn->recv_timeout = 10;
			IsSend=0;
			memset(tcp_client_sendbuf,'\0',256);
		//	netconn_getaddr(tcp_clientconn,&loca_ipaddr,&loca_port,1); //��ȡ����IP����IP��ַ�Ͷ˿ں�
		//	printf("�����Ϸ�����%d.%d.%d.%d,�����˿ں�Ϊ:%d\r\n",lwipdev.remoteip[0],lwipdev.remoteip[1], lwipdev.remoteip[2],lwipdev.remoteip[3],loca_port);
		  	/**
			��¼��վ��ID,վ�����룬׮����w v a ���״̬	t�豸���� c�汾��
			my_data.Sid,my_data.Spwd,my_data.Vname,my_data.Endp,my_data.Cvol,my_data.Cele,my_data.Ispower,Ver
			*/

			sprintf(tcp_client_sendbuf,"{\"type\":\"login\",\"sid\":%d,\"pwd\":\"%s\",\"pname\":\"%s\",\"w\":%d,\"v\":%d,\"a\":%d,\"Ispower\":%d,\"Cpower\":%d,\"Orderid\":%d,\"Isend\":%d,\"t\":1,\"c\":%d}\r\n",my_data.Sid,my_data.Spwd,my_data.Vname,my_data.Endp,my_data.Cvol,my_data.Cele,my_data.Ispower,my_data.Cpower,my_data.Orderid,my_data.Isend,Ver);
			if(my_data.Isend==0&&my_data.Ispower==0){
				my_data.Isend=1;
				my_data.Orderid=0;
				my_data.Money=0;
				my_data.Uint=0;
				my_data.Cpower=0;
				//�洢����
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
			delay_ms(10);
			
			tcp_client_flag |= LWIP_SEND_DATA;
			//���Ϻ��ύ��λ״̬
			DISTYPE=3;
			while(1){
				if((tcp_client_flag & LWIP_SEND_DATA) == LWIP_SEND_DATA) //������Ҫ����
				{
					err = netconn_write(tcp_clientconn ,tcp_client_sendbuf,strlen((char*)tcp_client_sendbuf),NETCONN_COPY); //����tcp_server_sentbuf�е�����
					if(err != ERR_OK)
					{
						printf("����ʧ�ܣ�%s\r\n",tcp_client_sendbuf);
					}else{
					//	printf("�ж�����#2\r\n"); 
						printf("�������ݣ�%s",tcp_client_sendbuf); 
						//���ڳ��������ţ�������ܹ���
						TIM5NUM=0;	
					}
					tcp_client_flag &= ~LWIP_SEND_DATA;
					
					IsSend=1;
				}

				if((recv_err = netconn_recv(tcp_clientconn,&recvbuf)) == ERR_OK)  //���յ�����
				{	
					OS_ENTER_CRITICAL(); //���ж�
					memset(tcp_client_recvbuf,0,TCP_CLIENT_RX_BUFSIZE);  //���ݽ��ջ���������
					for(q=recvbuf->p;q!=NULL;q=q->next)  //����������pbuf����
					{
						//�ж�Ҫ������TCP_CLIENT_RX_BUFSIZE�е������Ƿ����TCP_CLIENT_RX_BUFSIZE��ʣ��ռ䣬�������
						//�Ļ���ֻ����TCP_CLIENT_RX_BUFSIZE��ʣ�೤�ȵ����ݣ�����Ļ��Ϳ������е�����
						if(q->len > (TCP_CLIENT_RX_BUFSIZE-data_len)) memcpy(tcp_client_recvbuf+data_len,q->payload,(TCP_CLIENT_RX_BUFSIZE-data_len));//��������
						else memcpy(tcp_client_recvbuf+data_len,q->payload,q->len);
						data_len += q->len;  	
						if(data_len > TCP_CLIENT_RX_BUFSIZE) break; //����TCP�ͻ��˽�������,����	
					}
					OS_EXIT_CRITICAL();  //���ж�
					data_len=0;  //������ɺ�data_lenҪ���㡣					
				//	printf("%s\r\n",tcp_client_recvbuf);
				//	printf("�ж�����#3\r\n");
					TIM5NUM=0;
					any(tcp_client_recvbuf);
					netbuf_delete(recvbuf);
				}else if(recv_err == ERR_CLSD){  //�ر�����
					netconn_delete(tcp_clientconn);
					islink=1;
					printf("������%d.%d.%d.%d�Ͽ�����#111\r\n",lwipdev.remoteip[0],lwipdev.remoteip[1], lwipdev.remoteip[2],lwipdev.remoteip[3]);
					delay_ms(500);
					break;
				}
				//��ʱ������
				if(islink==1){
					netconn_delete(tcp_clientconn);
					printf("������%d.%d.%d.%d�Ͽ�����#2\r\n",lwipdev.remoteip[0],lwipdev.remoteip[1], lwipdev.remoteip[2],lwipdev.remoteip[3]);
					delay_ms(500);
					break;	
				}
			}
		}
	}
}

//����TCP�ͻ����߳�
//����ֵ:0 TCP�ͻ��˴����ɹ�
//		���� TCP�ͻ��˴���ʧ��
INT8U tcp_client_init(void)
{
	INT8U res;
	OS_CPU_SR cpu_sr;
	
	OS_ENTER_CRITICAL();	//���ж�
	res = OSTaskCreate(tcp_client_thread,(void*)0,(OS_STK*)&TCPCLIENT_TASK_STK[TCPCLIENT_STK_SIZE-1],TCPCLIENT_PRIO); //����TCP�ͻ����߳�
	OS_EXIT_CRITICAL();		//���ж�
	
	return res;
}
//��ʼ���
int StartChage(){
	//PC0λ���λ��PC1�����λ��PC2 CC����λ��
	//PC0δ��磬PC2Ϊ���Ѳ�ǹ��CP�źż��	
/*	printf("��11ǰADֵ��%u \r\n",adcnum);
	printf("PC0��ֵ��%d",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_SET);
	printf("PC2��ֵ��%d",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_RESET);
	printf("ADC��ֵ��%d,%d",adcnum>2854&&adcnum<3104,adcnum>3351&&adcnum<3600);	 */
	

	u32 adcnum;																				//�ɼ�����
	u16 t=0;																				//�ɼ�����
	OSTaskSuspend(7);																	   	
	while(IsTran);																			//��֤���ڹ�����
	IsTran=1; 																				//��ʶλ����״̬			
	if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)!=Bit_RESET){									//CC�߲�Ϊ�ͱ�ʾδ����
		printf("CC��δ���ӣ���ƽ�ǣ�%d\r\n",GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2));
		IsTran=0;
		OSTaskResume(7);
		return 20;
	}	 
	//�͵�ƽΪ��� PC0��Ϊ�߱�ʾ�ڳ�磬PC1Ϊ�����λ��ʼ����PC0���� ��ʱ������PC1
	if(my_data.Ispower==1){																		//׮�ı�־λ���ڳ����
		printf("׮���ڳ�磨��ʶλ��⣩#2\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 21;
	}
	if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_SET){									//׮������ź�Ϊ��
		printf("׮���ڳ�磨�����⣩#1\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 21;
	} 
	//if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_RESET){									//׮�������ź�Ϊ�� �̵�������
	//	printf("׮���ڳ�磨�����⣩#2\r\n");
	//	IsTran=0;
	//	OSTaskResume(7);
	//	return 22;
	//}
	if(GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_RESET){									//��ͣ���ر�����
		printf("��ͣ������\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 23;
	}
	if(Is485==0){
		printf("������\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 28;
	}


	adcnum= getAD();
	
	if(!(adcnum>2483&&adcnum<3650)){ 														//AD�ɼ���6-9V֮��
		printf("��һ�βɼ���ѹ��%d V\r\n",adcnum);
		IsTran=0;
		OSTaskResume(7);
		return 24;
	}
	
	TIM_SetCompare2(TIM3,(*(u16*)(FLASH_ADDR+198))*0.999);									//����PWM�ź�
	delay_ms(5);
	adcnum= getAD(); 
	while((!(adcnum>2483&&adcnum<3202))){													//����PWM�źź�Ҫ��10���ڽ�ѹ��6V
		t++;
		
		if(t>500){
			break;
		}else{ 
			//printf("�ɼ��ڣ�%d ��----",t);
			adcnum= getAD();
			//printf("�ɼ������:%d\r\n",adcnum);
			delay_ms(20);		
		} 

	}

	if(t>500){
		printf("ִ�г�ʱ���ɼ���ѹ��%d V\r\n",adcnum);																				//��ϵͳ��������ͣ��
		TIM_SetCompare2(TIM3,1000);	 
		IsTran=0;
		OSTaskResume(7);
		return 25;
	}
	adcnum= getAD();																		//�ٴβɼ����ж�
	printf("���ɼ���ѹ��%d V\r\n",adcnum);
	//��������������ж�һ��,PC0���λΪ�ͣ�PC1��練����Ϊ�ߣ�PC2 CC��Ϊ�ͣ�PE4��ͣΪ�ߣ�AD�ɼ�Ϊ6V����
	//if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_RESET&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_SET&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_RESET&&GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_SET&&(adcnum>2483&&adcnum<3202)){
	if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_0)==Bit_RESET&&GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_2)==Bit_RESET&&GPIO_ReadInputDataBit(GPIOE,GPIO_Pin_4)==Bit_SET&&(adcnum>2483&&adcnum<3202)){

		GPIO_SetBits(GPIOC,GPIO_Pin_0); 													//���
		t=0;
	//	while(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_SET){							//ѭ�������Ӵ��������ĵ�ƽ
	//		t++;
	//		if(t>100){
	//			break;
	//		}else{
	//			delay_ms(10);	
	//		}
	//	}
		if(t>100){
			printf("���Ӵ���������ʱ-���\r\n");												 //10�뻹û�м�⵽�͵�ƽ��������糬ʱ
			GPIO_ResetBits(GPIOC,GPIO_Pin_0); 												 //��ԭ���λ
		   	TIM_SetCompare2(TIM3,1000);	
			IsTran=0;
			OSTaskResume(7);
			return 26;
		}else{
			my_data.Ispower=1;
			IsTran=0;
			printf("���ɹ�\r\n");
			OSTaskResume(7); 
			printf("������ֵ��%d\r\n",BEEP);
			if(BEEP==1){
				OSTaskResume(13);
			}
			return 0;
		}		
	}else{																					//��������ͨ������ֱ��ֹͣ
		printf("���һ���������δͨ��\r\n");
		TIM_SetCompare2(TIM3,1000);	
		IsTran=0;
		OSTaskResume(7);
		return 27;
	}	
}

//ֹͣ��
u8 StopChage(){
	u8 i=0;
	//printf("ִ��ͣ�䷽��#1\r\n");
	while(IsTran==1);
	IsTran=1;
	TIM_SetCompare2(TIM3,1000);																//ֹͣpwm
	GPIO_ResetBits(GPIOC,GPIO_Pin_0);
	OSTaskSuspend(7);														//����־λ�õ�
	//printf("ִ��ͣ�䷽��\r\n");
//	while(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_RESET){								//ѭ�������Ӵ��������ĵ�ƽ
//		i++;
//		if(i>100){
//			break;
//		}else{
//			delay_ms(10);	
//		}
//	}
	if(i>100){																			 	//ֹͣʧ��
		printf("���Ӵ���������ʱ-ֹͣ\r\n");
		IsTran=0;
		OSTaskResume(7);
		return 0;
	}else{
		printf("ֹͣ���ɹ�\r\n");
		my_data.Ispower=0;
		IsTran=0;
		OSTaskResume(7);
		printf("������ֵ��%d\r\n",BEEP);
		if(BEEP==1){								 										//�������
			OSTaskResume(13); 
		}																   					//ֹͣ�ɹ�
		return 1;
	}

}

//ͣ��󽫴��ŷ���ƽ̨
void SendStop(u8 c,u8 z){
	if(islink==0){
		while(IsSend==0){
			delay_ms(1);
		}
		IsSend=0;
		memset(tcp_client_sendbuf,'\0',256);
		//	my_data.Endp,my_data.Cvol,my_data.Cele,c,z
		sprintf(tcp_client_sendbuf,"{\"type\":\"stopdata\",\"w\":%d,\"v\":%d,\"a\":%d,\"c\":%d,\"z\":%d,\"Orderid\":%d,\"Cpower\":%d}\r\n",my_data.Endp,my_data.Cvol,my_data.Cele,c,z,my_data.Orderid,my_data.Cpower);
		tcp_client_flag |= LWIP_SEND_DATA;

		my_data.Cpower=0;
		my_data.Ispower=0;
		my_data.Uint=0;
		my_data.Money=0;
		my_data.Orderid=0;
		my_data.Isend=1;
		//�洢����
		FLASH_Unlock();
		FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
		FLASH_ErasePage(FLASH_ADDR);
		STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
		FLASH_Lock();

	}
}




//��������ݽ���
void any(u8*d){
	cJSON *type,*json=cJSON_Parse((char *)d);
	printf("�������ݣ�%s\r\n",d);
	if(!json) {
	//	printf("����ʧ��!\n");
		return ;
    }
	type = cJSON_GetObjectItem(json,"type");
	if(!type) {
	//	printf("û�и�����!\n");
		return ;
    }
	if(strcmp(type->valuestring,"StartChage")==0){ //���
		int st= StartChage();
		cJSON *oid= cJSON_GetObjectItem(json,"Orderid");
		cJSON *uint= cJSON_GetObjectItem(json,"uint");
		cJSON *smoney= cJSON_GetObjectItem(json,"smoney");

		if(!oid||!uint||!smoney) {
			cJSON_Delete(oid);  	//�ͷ��ڴ�
			cJSON_Delete(uint);  	//�ͷ��ڴ�
			cJSON_Delete(smoney);	//�ͷ��ڴ�
			cJSON_Delete(json);  	//�ͷ��ڴ�   
			cJSON_Delete(type);  	//�ͷ��ڴ�   
    		cJSON_free(d); 
			return;
		}

		if(st==0){
			printf("����������#1\r\n");
			if(islink==0){
				while(IsSend==0){
					delay_ms(1);
				}
				IsSend=0;
				memset(tcp_client_sendbuf,'\0',256);
				//��ȷ��ͷ����
				sprintf(tcp_client_sendbuf,"{\"type\":\"startdata\",\"w\":%d,\"v\":%d,\"a\":%d,\"Orderid\":%d}\r\n",my_data.Endp,my_data.Cvol,my_data.Cele,oid->valueint);
				tcp_client_flag |= LWIP_SEND_DATA;
				my_data.Ispower=1;
				my_data.Cpower=0;
				my_data.Uint=uint->valueint;
				my_data.Money=smoney->valueint;
				my_data.Orderid=oid->valueint;
				my_data.Isend=0;
				my_data.Starp=my_data.Endp;
				//�洢����
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			}
		}else{
			if(islink==0){
					
				while(IsSend==0){
					delay_ms(1);
				}
				IsSend=0;
		  		memset(tcp_client_sendbuf,'\0',256);
			   	sprintf(tcp_client_sendbuf,"{\"type\":\"starterr\",\"code\":%d,\"Orderid\":%d}\r\n",st,oid->valueint);
				tcp_client_flag |= LWIP_SEND_DATA;
			}
		}
		cJSON_Delete(oid);  //�ͷ��ڴ�
		cJSON_Delete(uint);  //�ͷ��ڴ�
		cJSON_Delete(smoney);  //�ͷ��ڴ�
		cJSON_Delete(json);  //�ͷ��ڴ�   
		cJSON_Delete(type);  //�ͷ��ڴ�   
    	cJSON_free(d); 
		return;
	}
	if(strcmp(type->valuestring, "StopChage")==0){ 									//ֹͣ��磬��Ϊ��ҳֹͣ������ֹͣ
	
		if(GPIO_ReadInputDataBit(GPIOC,GPIO_Pin_1)==Bit_RESET){						//�жϳ�練��λ�Ƿ�Ϊ��
			u8 st=StopChage();														//ֹͣ����
			cJSON *code= cJSON_GetObjectItem(json,"code");
			printf("ֹͣ���#1\r\n");
			if(st){																	//ֹͣ�ɹ�
				if(code->valueint==0) {								//ƽ̨����ĳ��
					printf("ֹͣ���#2\r\n");
					SendStop(0,0);													//����ͣ�����
				}else if(code->valueint==1) {						//��ҳ����ĳ��ֱ�����
					printf("ֹͣ���#3\r\n");
					SendStop(1,0);
				}
			}else{																//ͣ��ʧ�ܣ���ƽ̨����ֹͣʧ��Ҫ���û���ͣ����ϵ����Ա
				if(code->valueint==0) {								//ƽ̨����ĳ��
					printf("ֹͣ���#2\r\n");
					SendStop(0,1);													//����ͣ�����
				}else if(code->valueint==1) {						//��ҳ����ĳ��ֱ�����
					printf("ֹͣ���#3\r\n");
					SendStop(1,1);
				}		
			}
			cJSON_Delete(code);
		}
		cJSON_Delete(json);  //�ͷ��ڴ�   
		cJSON_Delete(type);  //�ͷ��ڴ�   
   		cJSON_free(d); 
		return;
	}
	if(strcmp(type->valuestring, "UpdataVer")==0){ //���°汾
		printf("��⵽ϵͳ�汾�и��£��˶��Ƿ���Ҫ����...\r\n");
		if(my_data.Isdown==1){
			//char str[] = "http://192.168.1.66:215/index.php?s=/Home/Index/Down/id/1.html";
			char str[] = "http://139.199.221.53:9002/index.php?s=/Home/Index/NewBin";	
			printf("�����ļ���Ҫϵͳ��ʼ����ɺ�����أ��ȴ���...\r\n");
			OSTaskSuspend(7);
			while(!IsDown){	 //ǰ�ڳ�ʼ������ɺ�����
				delay_ms(500);
			}

			http_test((char *)str);
		}else{
			printf("�����������ϴ������ļ��������⣬���β�����.\r\n");
			my_data.Isdown=1;
			//�洢����
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
			FLASH_Lock();
		}
		cJSON_Delete(json);  //�ͷ��ڴ�   
		cJSON_Delete(type);  //�ͷ��ڴ�   
   		cJSON_free(d); 

		return;
	}

	if(strcmp(type->valuestring, "ping")==0){	 //������Ӧ
		if(islink==0){
			while(IsSend==0){
				delay_ms(1);
			}
			IsSend=0;
			memset(tcp_client_sendbuf,'\0',256); 
			strcpy(tcp_client_sendbuf,"{\"type\":\"ping\"}\r\n");
			tcp_client_flag |= LWIP_SEND_DATA;
		}
		cJSON_Delete(json);  //�ͷ��ڴ�   
		cJSON_Delete(type);  //�ͷ��ڴ�   
   		cJSON_free(d);				
		return;
	}
	cJSON_Delete(json);  //�ͷ��ڴ�   
	cJSON_Delete(type);  //�ͷ��ڴ�   
   	cJSON_free(d);	
}


//tim5�ж�
void tim5_init(){
	TIM_TimeBaseInitTypeDef  TIM_TimeBaseStructure;
	NVIC_InitTypeDef NVIC_InitStructure;
	RCC_APB1PeriphClockCmd(RCC_APB1Periph_TIM5, ENABLE); //ʱ��ʹ��

	//��ʱ��TIM5��ʼ��
	TIM_TimeBaseStructure.TIM_Period = 2000; //��������һ�������¼�װ�����Զ���װ�ؼĴ������ڵ�ֵ	
	TIM_TimeBaseStructure.TIM_Prescaler =35999; //����������ΪTIMxʱ��Ƶ�ʳ�����Ԥ��Ƶֵ
	TIM_TimeBaseStructure.TIM_ClockDivision = TIM_CKD_DIV1; //����ʱ�ӷָ�:TDTS = Tck_tim
	TIM_TimeBaseStructure.TIM_CounterMode = TIM_CounterMode_Up;  //TIM���ϼ���ģʽ
	TIM_TimeBaseInit(TIM5, &TIM_TimeBaseStructure); //����ָ���Ĳ�����ʼ��TIMx��ʱ�������λ
	TIM_ITConfig(TIM5,TIM_IT_Update,ENABLE ); //ʹ��ָ����TIM5�ж�,��������ж�

	//�ж����ȼ�NVIC����
	NVIC_InitStructure.NVIC_IRQChannel = TIM5_IRQn;  //TIM5�ж�
	NVIC_InitStructure.NVIC_IRQChannelPreemptionPriority =3;  //��ռ���ȼ�0��
	NVIC_InitStructure.NVIC_IRQChannelSubPriority = 3;  //�����ȼ�3��
	NVIC_InitStructure.NVIC_IRQChannelCmd = ENABLE; //IRQͨ����ʹ��
	NVIC_Init(&NVIC_InitStructure);  //��ʼ��NVIC�Ĵ���
	TIM_Cmd(TIM5, ENABLE);  //ʹ��TIMx
	TIM_ClearITPendingBit(TIM5, TIM_IT_Update  );  //���TIMx�����жϱ�־ 
	//TIM_Cmd(TIM5, DISABLE);
//	TIM_Cmd(TIM5,ENABLE);	
}

//��ʱ��5�жϷ������
void TIM5_IRQHandler(void){
	OSIntEnter();
	if (TIM_GetITStatus(TIM5, TIM_IT_Update) != RESET){		//���TIM5�����жϷ������
		TIM_ClearITPendingBit(TIM5, TIM_IT_Update  );		//���TIMx�����жϱ�־ 
		TIM5NUM++;											//��ʱ����
	   	if(TIM5NUM==35){									//35����жϵ���
			islink=1;										//����״̬λ
		}
	}
	OSIntExit();
}
