#include "delay.h"
#include "lwip/debug.h"
#include "httpd.h"
#include "lwip/tcp.h"
#include "fs.h"
#include "ADC.h"
#include "lwip_comm.h"
#include "tcp_client_demo.h"
#include "stmflash.h"
#include "beep.h"
#include <string.h>
#include <stdlib.h>

#define NUM_CONFIG_CGI_URIS	(sizeof(ppcURLs ) / sizeof(tCGI))  //CGI��URI����
#define NUM_CONFIG_SSI_TAGS	(sizeof(ppcTAGs) / sizeof (char *))  //SSI��TAG����

//����CGI handler
const char* LOGIN_CGI_Handler(int iIndex, int iNumParams, char *pcParam[], char *pcValue[]);
const char* INFO_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]);
const char* IP_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]);
const char* CLOUD_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]);
const char* THRES_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]);
const char* CPWD_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]);
const char* CPOWER_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]);
const char* REST_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]);

u8 boo=0;
char bnum[5]={'\0'};//���ظ�ҳ�������
//flash����	stmflash.h
extern struct InitData  my_data;
//�汾��stmflash.h
extern u8 Ver;

static const char *ppcTAGs[]=  //SSI��Tag
{
	"a", //JSֵ
	"b",
};

static const tCGI ppcURLs[]= //cgi����
{
	{"/login.cgi",LOGIN_CGI_Handler},
	{"/info.cgi",INFO_CGI_Handler},
	{"/ip.cgi",IP_CGI_Handler},
	{"/cloud.cgi",CLOUD_CGI_Handler},
	{"/cpwd.cgi",CPWD_CGI_Handler},
	{"/cpower.cgi",CPOWER_CGI_Handler},
	{"/rest.cgi",REST_CGI_Handler},
	{"/thres.cgi",THRES_CGI_Handler},
};

//�ַ����ָ�
void split(char dst[][5], char* str, const char* spl){
	int n=0;
    char *result = NULL;
    result = strtok(str, spl);
    while( result != NULL ){
        strcpy(dst[n++], result);
        result = strtok(NULL, spl);
    }
}
//��������ַ���
void get_rand_str(char s[],int num){
	char *str = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	int i,lstr;
	lstr = strlen(str);
	srand(getRand()); 
	for(i = 0; i < num; i++){
		s[i]=str[(rand()%lstr)];
	}
	s[i]=0;
}

//��web�ͻ��������������ʱ��,ʹ�ô˺�����CGI handler����
static int FindCGIParameter(const char *pcToFind,char *pcParam[],int iNumParams)
{
	int iLoop;
	for(iLoop = 0;iLoop < iNumParams;iLoop ++ )
	{
		if(strcmp(pcToFind,pcParam[iLoop]) == 0)
		{
			return (iLoop); //����iLOOP
		}
	}
	return (-1);
}


//��¼�󷵻صĺ���,��ʼ������
void JS_Handler(char *pcInsert){ 
//	char pcstr[256];
//	memset(pcstr,'\0',256);
	/**�忨��
	  *վ��id
	  *վ������
	  *IP��ַ
	  *��������
	  *����
	  *�ƶ˵�ַ
	  *��ƽ̨�˿�
	  *��ƽ̨����
	  *��ѹ����
	  *��ѹ����
	  *��������
	  *�汾��
	  *�Ƿ�����
	  *pwm����
	  *session
	**/
	sprintf(pcInsert,"\"%s\",%d,\"%s\",\"%d.%d.%d.%d\",\"%d.%d.%d.%d\",\"%d.%d.%d.%d\",\"%d.%d.%d.%d\",%d,\"%s\",%d,%d,%d,\"VM-AC.00%d\",%d,%d,\"%s\"",my_data.Vname,my_data.Sid,my_data.Spwd,my_data.Ip[0],my_data.Ip[1],my_data.Ip[2],my_data.Ip[3],my_data.Mask[0],my_data.Mask[1],my_data.Mask[2],my_data.Mask[3],my_data.Gway[0],my_data.Gway[1],my_data.Gway[2],my_data.Gway[3],my_data.Cloud[0],my_data.Cloud[1],my_data.Cloud[2],my_data.Cloud[3],my_data.Port,my_data.Server,my_data.Volup,my_data.Voldown,my_data.Ele,Ver,my_data.Ispower,my_data.Pwm,my_data.Session);
	

	/*my_data.Vname,my_data.Sid,my_data.Spwd,
	my_data.Ip[0],my_data.Ip[1],my_data.Ip[2],my_data.Ip[3],
	my_data.Mask[0],my_data.Mask[1],my_data.Mask[2],my_data.Mask[3],
	my_data.Gway[0],my_data.Gway[1],my_data.Gway[2],my_data.Gway[3],
	my_data.Cloud[0],my_data.Cloud[1],my_data.Cloud[2],my_data.Cloud[3],
	my_data.Port,my_data.Server,my_data.Volup,my_data.Voldown,my_data.Ele,Ver,my_data.Pwm,my_data.Session
	*/
}
//��Ϣ��ʾ
void ERR_Handler(char *pcInsert){ 
	 u8 i;
	 for(i=0;i<sizeof(bnum);i++){
	 	if(bnum[i]=='\0'){
			break;
		}else{
	 		*(pcInsert+i)=	bnum[i];
		}
	 }
}


//SSI��Handler���
static u16_t SSIHandler(int iIndex,char *pcInsert,int iInsertLen)
{
	switch(iIndex)
	{
		case 0: 
			JS_Handler(pcInsert);
			break;
		case 1:
			ERR_Handler(pcInsert);
			break;
	}
	return strlen(pcInsert);
}

//CGI LOGIN���ƾ��
const char* LOGIN_CGI_Handler(int iIndex, int iNumParams, char *pcParam[], char *pcValue[]){
	char *uname=pcValue[FindCGIParameter("uname",pcParam,iNumParams)];
	char *upwd=pcValue[FindCGIParameter("upwd",pcParam,iNumParams)];
	if(my_data.Idc==170){
	if(strcmp(uname,"admin")==0&&strcmp(my_data.Pwd,upwd)==0){
			//�������session
			char session[10];
			memset(session,'\0',10);
			get_rand_str(session,9);
			memset(my_data.Session,'\0',sizeof(my_data.Session));
			memcpy(my_data.Session,session,strlen(session));
			//�洢
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
			FLASH_Lock();

			return "/a.shtml";
		}else{
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"0\"",3);
			return "/b.shtml" ;
		}
	}else{	 //�忨δ��ʼ��
		memset(bnum,'\0',sizeof(bnum));
		memcpy(bnum,"\"0\"",3);
		return "/b.shtml" ;
	}				
}

//������Ϣ��CGI���ƾ��
const char* INFO_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]){
	char *uname=pcValue[FindCGIParameter("uname",pcParam,iNumParams)];
	char *session=pcValue[FindCGIParameter("session",pcParam,iNumParams)];
	if(my_data.Idc==170){
		if(strcmp(uname,"admin")==0&&strcmp(my_data.Session,session)==0){
			//�˺���֤ͨ��
			char *icname=pcValue[FindCGIParameter("icname",pcParam,iNumParams)];
			u32 siteid=	atoi(pcValue[FindCGIParameter("siteid",pcParam,iNumParams)]);
			char *sitepwd=pcValue[FindCGIParameter("sitepwd",pcParam,iNumParams)];
			u16 pt=	atoi(pcValue[FindCGIParameter("pt",pcParam,iNumParams)]);  //׮pwm����
			if(strlen(icname)>0&&strlen(icname)<=16&&strlen(sitepwd)>0&&strlen(sitepwd)<=12&&siteid>0&&siteid<65535){
			 	//��ֵ
				memset(my_data.Vname,'\0',sizeof(my_data.Vname));
				memcpy(my_data.Vname,icname,strlen(icname));
				memset(my_data.Spwd,'\0',sizeof(my_data.Spwd));
				memcpy(my_data.Spwd,sitepwd,strlen(sitepwd));
				my_data.Sid=siteid;
				my_data.Pwm=pt;
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			 	//����
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"4\"",3);
				return "/b.shtml";				
			}else{//��������ȷ
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"2\"",3);
				return "/b.shtml" ;	
			}
		//�˺���֤ͨ��
		}else{	//�������
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"0\"",3);
			return "/b.shtml" ;
		}
	}else{ //�忨δ��ʼ��
		memset(bnum,'\0',sizeof(bnum));
		memcpy(bnum,"\"1\"",3);
		return "/b.shtml" ;
	}		
				
}

//IP��ַ��CGI���ƾ��
const char* IP_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]){
	char *uname=pcValue[FindCGIParameter("uname",pcParam,iNumParams)];
	char *session=pcValue[FindCGIParameter("session",pcParam,iNumParams)];
	if(my_data.Idc==170){
		if(strcmp(uname,"admin")==0&&strcmp(my_data.Session,session)==0){
		//�˺���֤ͨ��
			char *ipadd=pcValue[FindCGIParameter("ipadd",pcParam,iNumParams)];
			char *mask=pcValue[FindCGIParameter("mask",pcParam,iNumParams)];
			char *gateway=pcValue[FindCGIParameter("gateway",pcParam,iNumParams)];
			char ipadds[4][5],masks[4][5],gateways[4][5];
			split(ipadds,ipadd,".");
			split(masks,mask,".");
			split(gateways,gateway,".");
			if(atoi(ipadds[0])>=0&&atoi(ipadds[0])<=255&&atoi(ipadds[1])>=0&&atoi(ipadds[1])<=255&&atoi(ipadds[2])>=0&&atoi(ipadds[2])<=255&&atoi(ipadds[3])>=0&&atoi(ipadds[3])<=255&&atoi(masks[0])>=0&&atoi(masks[0])<=255&&atoi(masks[1])>=0&&atoi(masks[1])<=255&&atoi(masks[2])>=0&&atoi(masks[2])<=255&&atoi(masks[3])>=0&&atoi(masks[3])<=255&&atoi(gateways[0])>=0&&atoi(gateways[0])<=255&&atoi(gateways[1])>=0&&atoi(gateways[1])<=255&&atoi(gateways[2])>=0&&atoi(gateways[2])<=255&&atoi(gateways[3])>=0&&atoi(gateways[3])<=255){
				u8 i;
				//IP��ַ
				for(i=0;i<4;i++){
					my_data.Ip[i]=atoi(ipadds[i]);
				}
				//��������
				for(i=0;i<4;i++){
					my_data.Mask[i]=atoi(masks[i]);	
				}
				//����
				for(i=0;i<4;i++){
					my_data.Gway[i]=atoi(gateways[i]);		
				}
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
			 	//����
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"4\"",3);
				return "/b.shtml" ;	
			}else{
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"2\"",3);
				return "/b.shtml" ;	
			}
		
		//�˺���֤ͨ��
		}else{	//�������
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"0\"",3);
			return "/b.shtml" ;
		}
	}else{ //�忨δ��ʼ��
		memset(bnum,'\0',sizeof(bnum));
		memcpy(bnum,"\"1\"",3);
		return "/b.shtml" ;
	}					
}

//�ƶ˵�ַ��CGI���ƾ��
const char* CLOUD_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]){
	char *uname=pcValue[FindCGIParameter("uname",pcParam,iNumParams)];
	char *session=pcValue[FindCGIParameter("session",pcParam,iNumParams)];
	if(my_data.Idc==170){
	if(strcmp(uname,"admin")==0&&strcmp(my_data.Session,session)==0){
		//�˺���֤ͨ��
		char *cadd=pcValue[FindCGIParameter("cadd",pcParam,iNumParams)];
		u16 port=atoi(pcValue[FindCGIParameter("cport",pcParam,iNumParams)]);
		char *cweb=pcValue[FindCGIParameter("cweb",pcParam,iNumParams)];
		char cadds[4][5];
		split(cadds,cadd,".");
		if(atoi(cadds[0])>=0&&atoi(cadds[0])<=255&&atoi(cadds[1])>=0&&atoi(cadds[1])<=255&&atoi(cadds[2])>=0&&atoi(cadds[2])<=255&&atoi(cadds[3])>=0&&atoi(cadds[3])<=255&&port>0&&port<65535&&strlen(cweb)>=3&&strlen(cweb)<=25){
			u8 i;
			//Զ��IP
			for(i=0;i<4;i++){
				my_data.Cloud[i]=atoi(cadds[i]);			
			}
			//�˿ں�
			my_data.Port=port;
			//��ַ
			memset(my_data.Server,'\0',sizeof(my_data.Server));
			memcpy(my_data.Server,cweb,strlen(cweb));
			//�洢
			FLASH_Unlock();
			FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
			FLASH_ErasePage(FLASH_ADDR);
			STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
			FLASH_Lock();
			//����
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"4\"",3);
			return "/b.shtml" ;
			//���ݸ�ʽ��֤�ɹ�
			}else{
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"2\"",3);
				return "/b.shtml";		
			}
		
		//�˺���֤ͨ��
		}else{	//�������
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"0\"",3);
			return "/b.shtml" ;
		}
	}else{ //�忨δ��ʼ��
		memset(bnum,'\0',sizeof(bnum));
		memcpy(bnum,"\"1\"",3);
		return "/b.shtml" ;
	}		
				
}
//��ֵ����
const char* THRES_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]){
	char *uname=pcValue[FindCGIParameter("uname",pcParam,iNumParams)];
	char *session=pcValue[FindCGIParameter("session",pcParam,iNumParams)];
	if(my_data.Idc==170){
		if(strcmp(uname,"admin")==0&&strcmp(my_data.Session,session)==0){
			//�˺���֤ͨ��
			char *vtop=pcValue[FindCGIParameter("vtop1",pcParam,iNumParams)];
			char *vbot=pcValue[FindCGIParameter("vbot1",pcParam,iNumParams)];
			char *atop=pcValue[FindCGIParameter("atop1",pcParam,iNumParams)]; 
			if(atoi(vtop)>0&&atoi(vtop)<50000&&atoi(vbot)>0&&atoi(vbot)<50000&&atoi(vtop)>atoi(vbot)&&atoi(atop)>0&&atoi(atop)<10000000){
				//��ֵ
				my_data.Volup = atoi(vtop);
				my_data.Voldown = atoi(vbot);
				my_data.Ele = atoi(atop);
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
				//����
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"3\"",3);
				return "/b.shtml" ;
				//���ݸ�ʽ��֤�ɹ�
			}else{
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"2\"",3);
				return "/b.shtml";	
			}
		
		//�˺���֤ͨ��
		}else{	//�������
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"0\"",3);
			return "/b.shtml" ;
		}
	}else{ //�忨δ��ʼ��
		memset(bnum,'\0',sizeof(bnum));
		memcpy(bnum,"\"1\"",3);
		return "/b.shtml" ;
	}		
	
}


//�޸������CGI���ƾ��
const char* CPWD_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]){

	char *uname=pcValue[FindCGIParameter("uname",pcParam,iNumParams)];
	char *session=pcValue[FindCGIParameter("session",pcParam,iNumParams)];
	if(my_data.Idc==170){
		if(strcmp(uname,"admin")==0&&strcmp(my_data.Session,session)==0){
			//�˺���֤ͨ��
			char *oldpwd=pcValue[FindCGIParameter("oldpwd",pcParam,iNumParams)];
			char *newpwd=pcValue[FindCGIParameter("newpwd",pcParam,iNumParams)];
			char *repwd=pcValue[FindCGIParameter("repwd",pcParam,iNumParams)]; 
			if(strcmp(my_data.Pwd,oldpwd)==0&&strcmp(newpwd,repwd)==0&&strlen(newpwd)>4&&strlen(newpwd)<=10){
				//�޸�����
				memset(my_data.Pwd,'\0',sizeof(my_data.Pwd));
				memcpy(my_data.Pwd,newpwd,strlen(newpwd));
				//�洢
				FLASH_Unlock();
				FLASH_ClearFlag(FLASH_FLAG_BSY|FLASH_FLAG_EOP|FLASH_FLAG_PGERR|FLASH_FLAG_WRPRTERR);
				FLASH_ErasePage(FLASH_ADDR);
				STMFLASH_Write(FLASH_ADDR,(u16 *)&my_data,sizeof(my_data));
				FLASH_Lock();
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"5\"",3);
				return "/b.shtml" ;
				//���ݸ�ʽ��֤�ɹ�
			}else{
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"2\"",3);
				return "/b.shtml";	
			}
		
		//�˺���֤ͨ��
		}else{	//�������
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"0\"",3);
			return "/b.shtml" ;
		}
	}else{ //�忨δ��ʼ��
		memset(bnum,'\0',sizeof(bnum));
		memcpy(bnum,"\"1\"",3);
		return "/b.shtml" ;
	}		
				
}

//���׮���Ƶ�CGI���ƾ��
const char* CPOWER_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]){
	char *uname=pcValue[FindCGIParameter("uname",pcParam,iNumParams)];
	char *session=pcValue[FindCGIParameter("session",pcParam,iNumParams)];
	if(my_data.Idc==170){
		if(strcmp(uname,"admin")==0&&strcmp(my_data.Session,session)==0){
			//�˺���֤ͨ��
			char *cval=pcValue[FindCGIParameter("cval",pcParam,iNumParams)];
			if(strcmp(cval,"0")==0){
				if(my_data.Ispower==1){	  	 //���ڳ����ֹͣ
					//OSTaskSuspend(9);  //ֹͣ���
					u8 st=StopChage();	   //����ֹͣ���� 
					printf("��ҳֹͣ��磡\r\n");
					OSTimeDlyHMSM(0,0,0,10);
					if(st){
						printf("��ҳֹͣ���ɹ���\r\n"); 
						SendStop(1,0);
						memset(bnum,'\0',sizeof(bnum));
						memcpy(bnum,"\"6\"",3);
						return "/b.shtml";
					}else{
						printf("��ҳֹͣ���ʧ�ܣ�\r\n");
						SendStop(1,2);
						memset(bnum,'\0',sizeof(bnum));
						memcpy(bnum,"\"8\"",3);
						return "/b.shtml";	
					}

				}else{
					printf("��׮û�г�磡\r\n");
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"8\"",3);
					return "/b.shtml";
				}
			}else if(strcmp(cval,"1")==0){
				int st=StartChage();
			   	if(st==0){
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"6\"",3);
					return "/b.shtml";
				}else if(st==20){
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"9\"",3);
					return "/b.shtml";	
				}else if(st==21||st==22){
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"7\"",3);
					return "/b.shtml";
				}else if(st==24){
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"0\"",3);
					return "/b.shtml";
				}else if(st==25){
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"11\"",4);
					return "/b.shtml";
				}else if(st==26){
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"12\"",4);
					return "/b.shtml";
				}else if(st==23){
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"13\"",4);
					return "/b.shtml";
				}else if(st==27){
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"14\"",4);
					return "/b.shtml";
				}else{
					memset(bnum,'\0',sizeof(bnum));
					memcpy(bnum,"\"15\"",4);
					return "/b.shtml";
				}
			}else{
				memset(bnum,'\0',sizeof(bnum));
				memcpy(bnum,"\"2\"",3);
				return "/b.shtml";	
			}
		//�˺���֤ͨ��
		}else{	//�������
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"0\"",3);
			return "/b.shtml";
		}
	}else{ //�忨δ��ʼ��
		memset(bnum,'\0',sizeof(bnum));
		memcpy(bnum,"\"1\"",3);
		return "/b.shtml" ;
	}					
}

//�������Ƶ�CGI���ƾ��
const char* REST_CGI_Handler(int iIndex,int iNumParams,char *pcParam[],char *pcValue[]){

	char *uname=pcValue[FindCGIParameter("uname",pcParam,iNumParams)];
	char *session=pcValue[FindCGIParameter("session",pcParam,iNumParams)];
	if(my_data.Idc==170){
		if(strcmp(uname,"admin")==0&&strcmp(my_data.Session,session)==0){
		//�˺���֤ͨ��
			__disable_fault_irq();   
			NVIC_SystemReset();
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"6\"",3);
			return "/b.shtml" ;
		//�˺���֤ͨ��
		}else{	//�������
			memset(bnum,'\0',sizeof(bnum));
			memcpy(bnum,"\"0\"",3);
			return "/b.shtml" ;
		}
	}else{ //�忨δ��ʼ��
		memset(bnum,'\0',sizeof(bnum));
		memcpy(bnum,"\"1\"",3);
		return "/b.shtml" ;
	}					
}

//SSI�����ʼ��
void httpd_ssi_init(void){  
	//����SSI���
	http_set_ssi_handler(SSIHandler,ppcTAGs,NUM_CONFIG_SSI_TAGS);
}

//CGI�����ʼ��
void httpd_cgi_init(void){ 
	//����CGI���
	http_set_cgi_handlers(ppcURLs, NUM_CONFIG_CGI_URIS);
}







