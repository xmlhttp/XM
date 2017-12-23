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
		$res = file_get_contents("https://login.weixin.qq.com/jslogin?appid=wx782c26e4c19acffb&fun=new&lang=zh_CN&_=".time());
		$code = preg_replace('/.*"(.*)".*/', '$1', $res);
		$this->assign('code',$code);
		ob_clean();
    	$this->display('Index:wxtest');
    }
	//验证登录
	public function chk_login(){

		$uuid=I("post.uuid",'','strip_tags');
		$url="https://login.weixin.qq.com/cgi-bin/mmwebwx-bin/login?uuid=".$uuid."&tip=0&loginicon=true&_=".time();
		$res = file_get_contents($url);
		$json['jscode']=$res;
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;
	}
	//获取初始化信息
	public function getTicket(){
		header("Content-Type: text/html; charset=utf-8");
		$uri=I("post.ticket",'','strip_tags')."&fun=new";
		$uuid=I("post.uuid",'','strip_tags');
		$res =post($uri);
		if(xml_decode($res,'ret')!=0){
			$json['status']['err']=1;
			$json['status']['msg']=(string)$err->message;
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$data['skey']=xml_decode($res,'skey');
		$data['wxsid']=xml_decode($res,'wxsid');
		$data['wxuin']=xml_decode($res,'wxuin');
		$data['pass_ticket']=xml_decode($res,'pass_ticket');
		//提交字符串,初始化信息
		$postdata1 = array(
			"BaseRequest"=>array(
				"DeviceID"  => "e".GetRandStr(15),
				"Sid"   	=> $data['wxsid'],
				"Skey"  	=> $data['skey'],
				"Uin"   	=> $data['wxuin']
			)
		);
		
		$url1="https://wx2.qq.com/cgi-bin/mmwebwx-bin/webwxinit?r=".time()."&lang=ch_ZN&pass_ticket=".$data['pass_ticket'];
		$res1=post($url1,json_encode($postdata1));
		$result1=json_decode($res1, true);
		if($result1['BaseResponse']['Ret']!=0){
			$json['status']['err']=1;
			$json['status']['msg']="请刷新后重试#1";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}

		
		//开启微信状态通知
		$postdata2 = array(
			"BaseRequest"=>array(
				"DeviceID"  => "e".GetRandStr(15),
				"Sid"   	=> $data['wxsid'],
				"Skey"  	=> $data['skey'],
				"Uin"   	=> $data['wxuin']
			),
			"ClientMsgId"=>time(),
			"FromUserName"=>$result1["User"]["UserName"],
			"ToUserName"=>$result1["User"]["UserName"],
			"Code"=>3
		);
	
		$url2="https://wx2.qq.com/cgi-bin/mmwebwx-bin/webwxstatusnotify?lang=ch_ZN&pass_ticket=".$data['pass_ticket'];
		$res2=post($url2,json_encode($postdata2));
		$result2=json_decode($res2, true);
		if($result2['BaseResponse']['Ret']!=0){
			$json['status']['err']=1;
			$json['status']['msg']="请刷新后重试#2";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}		
		//获取好友列表
		$url3="https://wx2.qq.com/cgi-bin/mmwebwx-bin/webwxgetcontact?pass_ticket=".$data['pass_ticket']."&seq=0&skey=".$data['skey']."&r=".time();
		$res3 = post($url3,json_encode($postdata1));
		$result3=json_decode($res3, true);
		if($result3['BaseResponse']['Ret']!=0){
			$json['status']['err']=1;
			$json['status']['msg']="请刷新后重试#3";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}	
		$data['FromUserName']=$result1['User']['UserName'];
		$data['SyncKey']=$result1['SyncKey'];
		$data['MsgID']=$result2['MsgID'];
		
		$myfile = fopen(dirname(__FILE__)."/key.txt", "w") or die("Unable to open file!");
		fwrite($myfile,json_encode($data));
		fclose($myfile);
				
		$json['res1']=$result1;
		$json['res2']=$result2;
		$json['res3']=$result3;
		//$json['data']=$data;
		$json['status']['err']=0;
		$json['status']['msg']='请求成功';
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;
	}
	//心跳函数
	public function sendHeart(){
		$myfile = fopen(dirname(__FILE__)."/key.txt", "r") or die("Unable to open file!");
		$json1= fread($myfile,filesize(dirname(__FILE__)."/key.txt"));
		fclose($myfile);
		$json2=json_decode($json1, true);
		$SyncKey=$json2['SyncKey'];
		for($i=0;$i<$SyncKey['Count'];$i++){
			if($i==$SyncKey['Count']-1){
				$str .= $SyncKey['List'][$i]['Key']."_".$SyncKey['List'][$i]['Val'];	
			}else{
				$str .= $SyncKey['List'][$i]['Key']."_".$SyncKey['List'][$i]['Val']."|";
			}
		}
			
		$url="https://webpush.wx2.qq.com/cgi-bin/mmwebwx-bin/synccheck?r=".time()."&skey=".$json2['skey']."&sid=".$json2['wxsid']."&uin=".$json2['wxuin']."&deviceid=e".GetRandStr(15)."&synckey=".$str."&_=".time();
		$res = post($url,null,false);
		$result=json_decode($res, true);
		$json['res']=$res;
		$json['status']['err']=0;
		$json['status']['msg']='请求成功';
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;
	}
	//发送信息
	public function sendMsg(){
		$myfile = fopen(dirname(__FILE__)."/key.txt", "r") or die("Unable to open file!");
		$json1= fread($myfile,filesize(dirname(__FILE__)."/key.txt"));
		fclose($myfile);
		$json2=json_decode($json1, true);
		
		$postdata = array(
			"BaseRequest"=>array(
				"DeviceID"  => "e".GetRandStr(15),
				"Sid"   	=> $json2['wxsid'],
				"Skey"  	=> $json2['skey'],
				"Uin"   	=> $json2['wxuin']
			),
			"Msg"=>array(
				"Type" => 1,
				"Content" => I("post.content",'','strip_tags'),
				"FromUserName" => $json2['FromUserName'],
				"ToUserName" => I("post.ToUserName",'','strip_tags'),
				"LocalID" => time(),
				"ClientMsgId" => time()
			)
		);
		$url="https://wx2.qq.com/cgi-bin/mmwebwx-bin/webwxsendmsg";
		$res = post($url,json_encode($postdata,JSON_UNESCAPED_UNICODE));
		$result=json_decode($res, true);
		if($result['BaseResponse']['Ret']!=0){
			$json['status']['err']=1;
			$json['status']['msg']='发送失败';
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$json['res']=$result;
		$json['status']['err']=0;
		$json['status']['msg']='请求成功';
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;
		
	}
	//接收消息
	public function getMsg(){
		$myfile = fopen(dirname(__FILE__)."/key.txt", "r") or die("Unable to open file!");
		$json1= fread($myfile,filesize(dirname(__FILE__)."/key.txt"));
		fclose($myfile);
		$json2=json_decode($json1, true);
		$postdata = array(
			"BaseRequest"=>array(
				"DeviceID"  => "e".GetRandStr(15),
				"Sid"   	=> $json2['wxsid'],
				"Skey"  	=> $json2['skey'],
				"Uin"   	=> $json2['wxuin']
			),
			"SyncKey"=>$json2['SyncKey'],
			"rr"=>time()
		);
		$url="https://wx2.qq.com/cgi-bin/mmwebwx-bin/webwxsync?sid=".$json2['wxsid']."&skey=".$json2['skey'];
		$res = post($url,json_encode($postdata,JSON_UNESCAPED_UNICODE));
		$result=json_decode($res, true);
		if($result['BaseResponse']['Ret']!=0){
			$json['status']['err']=1;
			$json['status']['msg']='发送失败';
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$json2['SyncKey']=$result['SyncKey'];
		$myfile = fopen(dirname(__FILE__)."/key.txt", "w") or die("Unable to open file!");
		fwrite($myfile,json_encode($json2));
		fclose($myfile);
		
		$json['res']=$result;
		$json['status']['err']=0;
		$json['status']['msg']='请求成功';
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;
	}
	
	public function getT(){
		echo 1;	
	}
}
//XML解码
function xml_decode($xml, $root = 'so') {
    $search = '/<(' . $root . ')>(.*)<\/\s*?\\1\s*?>/s';
    $array = array();
    if(preg_match($search, $xml, $matches)){
		return $matches[2]; 
    }else{
		return "";	
	}
}
//POST提交
function post($url,$param,$ispost=ture){
	$headers = array(	
		'Content-Type' => 'application/json;charset=UTF-8'
	);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_TIMEOUT,60); //设置超时
    if(0 === strpos(strtolower($url),'https')) {
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0); //对认证证书来源的检查
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0); //从证书中检查SSL加密算法是否存在
    }
	if($ispost){
    	curl_setopt($ch,CURLOPT_POST, true);
	}
    curl_setopt($ch,CURLOPT_POSTFIELDS, $param);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_COOKIEJAR, dirname(__FILE__)."/cook.txt");
	curl_setopt($ch,CURLOPT_COOKIEFILE, dirname(__FILE__)."/cook.txt");
	$resp =@curl_exec($ch);
    curl_close($ch);
    return $resp;
 }
//设备编号 
function GetRandStr($len){ 
	$chars = array("0","1","2","3","4","5","6","7","8","9"); 
    $charsLen = count($chars) - 1; 
    shuffle($chars);   
    $output = ""; 
    for ($i=0; $i<$len; $i++){ 
        $output .= $chars[mt_rand(0, $charsLen)]; 
    }  
    return $output;  
}