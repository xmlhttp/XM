<?php
//模拟setTimeout
function sock_post($url, $query){
	$host = parse_url($url,PHP_URL_HOST);
	$port = parse_url($url,PHP_URL_PORT);
	$port = $port ? $port : 222;
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
	}else {
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