#ifndef __HTTP_H__
#define __HTTP_H__

#include "sys.h"
#include "includes.h"
#define US_NULL			(0)
typedef  void (*iapfun)(void);				//定义一个函数类型的参数. 
struct http_session
{
    char* user_agent;
	int   socket;
    unsigned long size;
    long  position;
};
struct shoutcast_session
{
	int   socket;
	char* station_name;
	int   bitrate;
	unsigned long metaint;
	unsigned long current_meta_chunk;
};

struct http_session* http_session_open(const char* url);


int http_parse_content_length(char *mime_buf);
int http_session_close(struct http_session* session);
void http_test(char* url);
void iap_load_app(u32 appxaddr);			//跳转到APP程序执行
#endif
