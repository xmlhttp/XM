<?php
    require_once(dirname(__file__)."/TopSdk.php");

    //将下载到的SDK里面的TopClient.php的$gatewayUrl的值改为沙箱地址:http://gw.api.tbsandbox.com/router/rest
    //正式环境时需要将该地址设置为：http://gw.api.taobao.com/router/rest

    class sendMsg{
        public function send($msg){
            $returnData = array();
            $c = new TopClient;
            $c->gatewayUrl = "http://gw.api.taobao.com/router/rest";
            $c->appkey = '23416466';
            $c->secretKey = '964f9749b1c4a3db1961f5c9d243ccbe';
            $req = new AlibabaAliqinFcSmsNumSendRequest;
//            $req->setExtend("123456");//发送扩展字段
            $req->setSmsType("normal");//固定字段
            $req->setSmsFreeSignName("测试平台");//短信签名
            $req->setSmsParam("{\"number\":\"".$msg['code']."\"}");//模板变量
            $req->setRecNum($msg['username']);//发送号码
            $req->setSmsTemplateCode($msg['template']);//发送模板
            $resp = $c->execute($req);
            if($resp->result&&$resp->result->err_code==0){
                //发送成功
                $returnData['status'] = 1;
                $returnData['msg'] = 'success!';
            }else{
                //发送失败
                $returnData['status'] = $resp->code;
                $returnData['msg'] = $resp->sub_msg;
            }
            return $returnData;
        }
		
		public function sendHit($msg){
            $returnData = array();
            $c = new TopClient;
            $c->gatewayUrl = "http://gw.api.taobao.com/router/rest";
            $c->appkey = '23416466';
            $c->secretKey = '964f9749b1c4a3db1961f5c9d243ccbe';
            $req = new AlibabaAliqinFcSmsNumSendRequest;
//            $req->setExtend("123456");//发送扩展字段
            $req->setSmsType("normal");//固定字段
            $req->setSmsFreeSignName($msg['tempname']);//短信签名
            $req->setSmsParam("{\"name\":\"".$msg['tel']."\",\"num\":\"".$msg['num']."\"}");//模板变量
            $req->setRecNum($msg['tel']);//发送号码
            $req->setSmsTemplateCode($msg['template']);//发送模板
            $resp = $c->execute($req);
            if($resp->result&&$resp->result->err_code==0){
                //发送成功
                $returnData['status'] = 1;
                $returnData['msg'] = 'success!';
            }else{
                //发送失败
                $returnData['status'] = $resp->code;
                $returnData['msg'] = $resp->sub_msg;
            }
            return $returnData;
        }
		
		
    }
?>