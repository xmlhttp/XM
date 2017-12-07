/*
 * http client for RT-Thread
 */
#include "http.h"
#include "delay.h"
#include "lwip/sockets.h"
#include "lwip/netdb.h"
#include "malloc.h"
#include "stmflash.h"
#define HTTP_RCV_TIMEO	6000 /* 6 second */
#define bak_add 0x08043800 //备份地址从第135块开始 0-14,15-134,135-254,255-255
#define FLASH_ADDR 0x0807F800
u8 iapbuf[2049];
char sizechar[10];
void inttohex(int aa); 
const char _http_get[] = "GET %s HTTP/1.1\r\nHost: %s\r\nUser-Agent: VMUUI HTTP Agent\r\nConnection: Keep-Alive\r\nCookie: name=\"VMUUI\"; QQ=\"568615539\"\r\n\r\n";
void pu8(u32 appxaddr,u32 appsize);
/*
*@function 解析文件大小字符串
*@param mime_buf 获取头文件中字符串
*/
int http_parse_content_length(char *mime_buf){
	char *line;
	line = strstr(mime_buf, "ACCEPT-LENGTH:");
	line += strlen("ACCEPT-LENGTH:");
	while((*line == ' ') || (*line == '\t')) line++;
	return (int)strtol(line, US_NULL, 10);
}

/*
*@function 初始响应返回“200 OK”或其他状态
*@param mime_buf 获取头文件中文件字符串
*/
int http_is_error_header(char *mime_buf){
	char *line;
	int i;
	int code;
	line = strstr(mime_buf, "HTTP/1.");
	line += strlen("HTTP/1.");
	line++;
	while((*line == ' ') || (*line == '\t')) line++;
	for(i = 0; ((line[i] != ' ') && (line[i] != '\t')); i++);
	line[i] = '\0';
	code = (int)strtol(line, US_NULL, 10);
	if( code == 200 )
		return 0;
	else
		return code;
}

/*
*@function 流文件的初始响应返回“200 OK”或其他状态
*@param mime_buf 获取头文件中文件字符串

int shoutcast_is_error_header(char *mime_buf)
{
	char *line;
	int i;
	int code;

	line = strstr(mime_buf, "ICY");
	line += strlen("ICY");

	// Advance past minor protocol version number
	line++;

	// Advance past any whitespace characters
	while((*line == ' ') || (*line == '\t')) line++;

	// Terminate string after status code
	for(i = 0; ((line[i] != ' ') && (line[i] != '\t')); i++);
	line[i] = '\0';

	code = (int)strtol(line, US_NULL, 10);
	if( code == 200 )
		return 0;
	else
		return code;
}
 */

int http_read_line( int socket, char * buffer, int size )
{
	char * ptr = buffer;
	int count = 0;
	int rc;

	// Keep reading until we fill the buffer.
	while ( count < size )
	{
		rc = recv( socket, ptr, 1, 0 );
		if ( rc <= 0 ) return rc;

		if ((*ptr == '\n'))
		{
			ptr ++;
			count++;
			break;
		}

		// increment after check for cr.  Don't want to count the cr.
		count++;
		ptr++;
	}

	// Terminate string
	*ptr = '\0';

	// return how many bytes read.
	return count;
}



int http_resolve_address(struct sockaddr_in *server, const char * url, char *host_addr, char** request)
{
	char *ptr;
	char port[6] = "80"; 
	int i = 0, is_domain;
	struct hostent *hptr;


	ptr = strchr(url, ':');
	if (ptr != NULL)
	{
		url = ptr + 1;
	}


	if((url[0] != '/') || (url[1] != '/' )) return -1;

	url += 2; is_domain = 0;
	i = 0;

	while (*url)
	{
		if (*url == '/') break;
		if (*url == ':')
		{
			unsigned char w;
			for (w = 0; w < 5 && url[w + 1] != '/' && url[w + 1] != '\0'; w ++)
				port[w] = url[w + 1];
			

			port[w] = '\0';
			url += w + 1;
			break;
		}

		if ((*url < '0' || *url > '9') && *url != '.')
			is_domain = 1;
		host_addr[i++] = *url;
		url ++;
	}
	*request = (char*)url;


	host_addr[i] = '\0';

	if (is_domain)
	{

		hptr = gethostbyname(host_addr);
		if(hptr == 0)
		{
			printf("HTTP: failed to resolve domain '%s'\r\n", host_addr);
			return -1;
		}
		memcpy(&server->sin_addr, *hptr->h_addr_list, sizeof(server->sin_addr));
	}
	else
	{
		inet_aton(host_addr, (struct in_addr*)&(server->sin_addr));
	}

	server->sin_port = htons((int) strtol(port, NULL, 10));
	server->sin_family = AF_INET;

	return 0;
}

signed long us_snprintf(char *buf, unsigned long size, const char *fmt, ...)
{
    signed   long n;
    va_list args;

    va_start(args, fmt);
    n = vsnprintf(buf, size, fmt, args);
    va_end(args);

    return n;
}


static int http_connect(struct http_session* session,
    struct sockaddr_in * server, char *host_addr, const char *url)
{
	int socket_handle;
	int peer_handle;
	int rc;
	char mimeBuffer[100];
	int timeout = HTTP_RCV_TIMEO;
	socket_handle = socket( PF_INET, SOCK_STREAM, IPPROTO_TCP );
	if(socket_handle < 0)
	{
		//printf( "HTTP: SOCKET FAILED111\r\n" );
		return -1;
	}

	/* set recv timeout option */
	setsockopt(socket_handle, SOL_SOCKET, SO_RCVTIMEO, (void*)&timeout, sizeof(timeout));

	peer_handle = connect( socket_handle, (struct sockaddr *) server, sizeof(*server));
	if ( peer_handle < 0 )
	{
	//	printf( "HTTP: CONNECT FAILED %i\r\n", peer_handle );
		return -1;
	}

	{
		char *buf;
		unsigned long length;

		buf = mymalloc(SRAMIN,512);
		if (*url){
			length = us_snprintf(buf, 512, _http_get, url, host_addr);
		}else{
			length = us_snprintf(buf, 512, _http_get, "/", host_addr);
		}
		rc = send(peer_handle, buf, length, 0);

		myfree(SRAMIN,buf);
	}

	// We now need to read the header information
	while ( 1 )
	{
		int i;

		// read a line from the header information.
		rc = http_read_line( peer_handle, mimeBuffer, 100 );
		// rt_kprintf(">> %s\n", mimeBuffer);

		if ( rc < 0 ) return rc;

		// End of headers is a blank line.  exit.
		if (rc == 0) break;
		if ((rc == 2) && (mimeBuffer[0] == '\r')) break;

		// Convert mimeBuffer to upper case, so we can do string comps
		for(i = 0; i < strlen(mimeBuffer); i++)
			mimeBuffer[i] = toupper(mimeBuffer[i]);

		if(strstr(mimeBuffer, "HTTP/1.")) // First line of header, contains status code. Check for an error code
		{
			rc = http_is_error_header(mimeBuffer);
			if(rc)
			{
			//	printf("HTTP: status code = %d!\r\n", rc);
				return -rc;
			}
		}
		 
		if(strstr(mimeBuffer, "ACCEPT-LENGTH:"))
		{  
			session->size = http_parse_content_length(mimeBuffer);
		//	printf("size = %d\r\n", session->size);
		}
	}

	// We've sent the request, and read the headers.  SockHandle is
	// now at the start of the main data read for a file io read.
	return peer_handle;
}

struct http_session* http_session_open(const char* url)
{
	int peer_handle = 0;
	struct sockaddr_in server;
	char *request, host_addr[32];
	struct http_session* session;
    session = (struct http_session*) mymalloc(SRAMIN,sizeof(struct http_session));
	if(session == US_NULL) return US_NULL;

	session->size = 0;
	session->position = 0;
	/* Check valid IP address and URL */
	if(http_resolve_address(&server, url, &host_addr[0], &request) != 0)
	{
		myfree(SRAMIN,session);
		return US_NULL;
	}
	// Now we connect and initiate the transfer by sending a
	// request header to the server, and receiving the response header
	if((peer_handle = http_connect(session, &server, host_addr, request)) < 0)
	{
        printf("HTTP: failed to connect to '%s'!\r\n", host_addr);
		http_session_close(session);
		//rt_free(session);
		return US_NULL;
	}

	// http connect returns valid socket.  Save in handle list.
	session->socket = peer_handle;

	/* open successfully */
	return session;
}

unsigned long http_session_read(struct http_session* session, unsigned char *buffer, unsigned long length)
{
	int bytesRead = 0;
	int totalRead = 0;
	int left = length;

	// Read until: there is an error, we've read "size" bytes or the remote
	//             side has closed the connection.
	do
	{
		bytesRead = recv(session->socket, buffer + totalRead, left, 0);
		if(bytesRead <= 0) break;

		left -= bytesRead;
		totalRead += bytesRead;
	} while(left);

	return totalRead;
}

long http_session_seek(struct http_session* session, long offset, int mode)
{
	switch(mode)
	{
	case SEEK_SET:
		session->position = offset;
		break;

	case SEEK_CUR:
		session->position += offset;
		break;

	case SEEK_END:
		session->position = session->size + offset;
		break;
	}

	return session->position;
}

int http_session_close(struct http_session* session)
{
   	lwip_close(session->socket);
	myfree(SRAMIN,session);

	return 0;
}



void http_test(char* url)
{
	struct http_session* session;
	char buffer[80];
	unsigned long length;
	int j=0,status=1,sizenum;
	u16 t,i=0,m=0;
	u32 fwaddr=bak_add;
	u8 *dfu;
	u8 k=0,k1=0;
	u16 FLASH_DATA[99];//共98位，站点id和端口号占两位
	session = http_session_open(url);
	if (session == US_NULL)
	{
	//	printf("open http session failed\r\n");
		return;
	}
	memset(iapbuf,'\0', sizeof(iapbuf));
	sizenum=session->size;
	memset(sizechar,'\0', sizeof(sizechar));
	inttohex(sizenum);
	for(k1=0;k1<sizeof(sizechar);k1++){
		if(sizechar[k1]=='\0'){
			break;
		}
	}
	printf("开始下载文件,文件大小：%d\r\n",session->size);
	do
	{
		memset(buffer,'\0', sizeof(buffer));
		if((m+1)*sizeof(buffer)>=sizenum+k){
			length = http_session_read(session, (unsigned char *)buffer, sizenum-sizeof(buffer)*m+k);
			status=0;
		}else{
			length = http_session_read(session, (unsigned char *)buffer, sizeof(buffer));
		}
		if(m==0){ //第一次请求判断头部是否带有文件大小
			for(k=0;k<sizeof(sizechar);k++){
				if(sizechar[k]=='\0'||sizechar[k]!=buffer[k]){
				 	break;
				}
			}	
			if(k!=0&&k!=k1){
				k=0;
			}else if(k!=0&&k==k1){
				if(buffer[k]==0x0D&&buffer[k+1]==0x0A){
			   		k=k+2;
				}else{
					k=0;	
				}
			} 
		//	printf("头文件长度为：%d\r\n",k);
		}

		dfu=(u8 *)buffer;
		if(m==0&&k!=0){
			for(t=k;t<sizeof(buffer);t++){
				iapbuf[i++]=dfu[t];
			}	
		}else{
			for(t=0;t<sizeof(buffer);t++){
				iapbuf[i++]=dfu[t];
				if((j*2048+i)>session->size){
					break;
				}	    
				if(i==2048){
					i=0;
					j++;
					STMFLASH_WriteHalf(fwaddr,iapbuf,2048);	
					fwaddr+=2048;//偏移2048  16=2*8.所以要乘以2.
			 		memset(iapbuf,'\0', sizeof(iapbuf));
				}
			}
		}
		m++;
	} while (length > 0&&status==1); 

	if(i){ 
		STMFLASH_WriteHalf(fwaddr,iapbuf,(i-1));
	}
	http_session_close(session);
	printf("文件接收完成.\r\n");
	memset(FLASH_DATA,'\0',sizeof(FLASH_DATA));
	for(i=0;i<sizeof(FLASH_DATA);i++){
		FLASH_DATA[i]=(*(u16*)(FLASH_ADDR+2*i));
	}
	FLASH_DATA[96]=sizenum/65536;
	FLASH_DATA[97]=sizenum%65536;
	FLASH_DATA[98]=1;
	STMFLASH_Write(FLASH_ADDR,FLASH_DATA,i);
	printf("等待重启...\r\n");
	delay_ms(100);	
	__disable_fault_irq();   
	NVIC_SystemReset();
}

//十进制转16进制字符串
void inttohex(int aa)
{
    static int i = 0;
    if (aa < 16)            //递归结束条件 
    {
        if (aa < 10)        //当前数转换成字符放入字符串 
            sizechar[i] = aa + '0';
        else
            sizechar[i] = aa - 10 + 'a';
        sizechar[i+1] = '\0'; //字符串结束标志 
    }
    else
    {
        inttohex(aa / 16);  //递归调用 
        i++;                //字符串索引+1 
        aa %= 16;           //计算当前值
        if (aa < 10)        //当前数转换成字符放入字符串 
            sizechar[i] = aa + '0';
        else
            sizechar[i] = aa - 10 + 'a';
    }
}
