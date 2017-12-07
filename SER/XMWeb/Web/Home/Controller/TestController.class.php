<?php
namespace Home\Controller;
use Think\Controller;

class TestController extends Controller {    

    /*
     * 主页
     * 
     * return #
    */
    public function index(){
		_sock("http://budian.richcomm.com.cn/index.php?s=/Home/Test/setTime&tid=111&uid=2&sessionid=33");
		echo 111;
    }
	//settion
	public function setTime(){
		ignore_user_abort(true);
		set_time_limit(0);
		sleep(5);
		$data['tid']= I('get.tid',0,'intval');
		$data['uid']= I('get.uid',0,'intval');		
		$data['session']= I('get.sessionid','','strip_tags');
		M('test')->add($data);
	}
	//循环
	public function ListTest(){
			
	}
}
function _sock($url) {
  $host = parse_url($url,PHP_URL_HOST);
  $port = parse_url($url,PHP_URL_PORT);
  $port = $port ? $port : 9001;
  $scheme = parse_url($url,PHP_URL_SCHEME);
  $path = parse_url($url,PHP_URL_PATH);
  $query = parse_url($url,PHP_URL_QUERY);
  if($query) $path .= '?'.$query;
  if($scheme == 'https') {
    $host = 'ssl://'.$host;
  }

  $fp = fsockopen($host,$port,$error_code,$error_msg,1);
  if(!$fp) {
    return array('error_code' => $error_code,'error_msg' => $error_msg);
  }
  else {
    stream_set_blocking($fp,true);//开启了手册上说的非阻塞模式
    stream_set_timeout($fp,1);//设置超时
    $header = "GET $path HTTP/1.1\r\n";
    $header.="Host: $host\r\n";
    $header.="Connection: close\r\n\r\n";//长连接关闭
    fwrite($fp, $header);
    usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
    fclose($fp);
    echo array('error_code' => 0);
  }
}
