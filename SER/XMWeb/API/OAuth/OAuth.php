<?php
    /*
     * 第三方登录验证——QQ、微信、微博
     * 
     * @author ll
     * @date 2016.5.26 10:35
     * 
     * return code
     */
    ini_set('date.timezone','Asia/Shanghai');
    class OAuth{
        const GET_QQ_OPENID_URL='https://graph.qq.com/oauth2.0/me';
        const GET_WEIXIN_CHECK_URL='https://api.weixin.qq.com/sns/auth';
        const GET_WEIBO_UID_URL='https://api.weibo.com/2/account/get_uid.json';
        private $access_token;
        private $openid;//微信加到请求参数直接校验，QQ与微博需从获取结果中比对
        private $type;//1.QQ,2.微信,3.微博
        public function __construct($access_token,$openid,$type){
            $this->access_token = $access_token;
            $this->openid = $openid;
            $this->type = $type;
        }
        public function verify(){
            if($this->type==1){
                //QQ
                $url = self::GET_QQ_OPENID_URL.'?access_token='.$this->access_token;
                $response = $this->curl_get($url);
                if(!empty($response)){
                    $lpos = strpos($response, "(");
                    $rpos = strrpos($response, ")");
                    $response  = json_decode(substr($response, $lpos + 1, $rpos - $lpos -1),true);
                    if(!empty($response['openid'])&&$response['openid']==$this->openid){
                        return $this->returnCode(1);
                    }else{
                        return $this->returnCode(2,$response['error_description']);
                    }
                }else{
                    return $this->returnCode(3);
                }
            }else if($this->type==2){
                //微信
                $url = self::GET_WEIXIN_CHECK_URL.'?access_token='.$this->access_token.'&openid='.$this->openid;
                $response = json_decode($this->curl_get($url),true);
                if(!empty($response)){
                    if($response['errcode']==0){
                        return $this->returnCode(1);
                    }else{
                        return $this->returnCode(4,$response['errmsg']);
                    }
                }else{
                    return $this->returnCode(5);
                }
            }else if($this->type==3){
                //微博
                $url = self::GET_WEIBO_UID_URL.'?access_token='.$this->access_token;
                $response = json_decode($this->curl_get($url),true);
                if(!empty($response)){
                    if(!empty($response['uid'])&&$response['uid']==$this->openid){
                        return $this->returnCode(1);
                    }else{
                        return $this->returnCode(6,$response['error']);
                    }
                }else{
                    return $this->returnCode(7);
                }
            }
        }
        public function curl_get($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
            return $response;
        }
        public function returnCode($num,$msg=''){
            $returnData=array();
            $returnData['code'] = 0;
            switch($num){
                case 1;$returnData['msg'] = '校验通过！';$returnData['code']=1;break;
                case 2:$returnData['msg'] = $msg?$msg:'QQ校验出错！';break;
                case 3:$returnData['msg'] = '请求QQ失败！';break;
                case 4:$returnData['msg'] = $msg?$msg:'微信校验出错！';break;
                case 5:$returnData['msg'] = '请求微信失败！';break;
                case 6:$returnData['msg'] = $msg?$msg:'微博校验出错！';break;
                case 7:$returnData['msg'] = '请求微博失败！';break;
            }
            return $returnData;
        }
    }