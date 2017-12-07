<?php
ini_set('date.timezone','Asia/Shanghai');
require_once dirname(__FILE__)."/WxPay.Api.php";

class Pay{
    
    protected $order_info;
    protected $pay_config;
    
    public function __construct($order_info,$pay_config){
        $this->order_info = $order_info;
        $this->pay_config = $pay_config;
    }
    
    public function pay(){
        $input = new WxPayUnifiedOrder();
        $input->SetAppid($this->pay_config['appid']);//微信分配的公众账号ID
        $input->SetMch_id($this->pay_config['mchid']);//微信支付分配的商户号
        $input->SetKey($this->pay_config['key']);//key
        $input->SetBody($this->order_info['order_info']);//商品或支付单简要描述
        $input->SetAttach("weixin");//附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetOut_trade_no($this->order_info['out_trade_no']);//商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee(floatval($this->order_info['total_fee']*100));//订单总金额，只能为整数，详见支付金额
        $input->SetSpbill_create_ip($this->order_info['spbill_create_ip']);//终端ip
        $input->SetTime_start(date("YmdHis",strtotime($this->order_info['add_time'])));//订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
				
        $input->SetTime_expire(date("YmdHis",strtotime($this->order_info['add_time']) + 108000));//设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
		
		
        $input->SetNotify_url($this->order_info['notify_url']);//接收微信支付异步通知回调地址
        $input->SetTrade_type($this->pay_config['paytype']);//设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        if($this->pay_config['paytype']=='JSAPI'){
            $input->SetOpenid($this->pay_config['openid']);//用户openid
        }
		//header("Content-type: text/html; charset=utf-8"); 
		//var_dump($input->ToXml());
		//exit;
        
		$order = WxPayApi::unifiedOrder($input);
		
        $result = $this->reSign($order,$this->pay_config['key'],$this->pay_config['paytype']);
        return $result;
    }
    
    public function refund(){
        $input = new WxPayRefund();
        $input->SetAppid($this->pay_config['appid']);//微信分配的公众账号ID
        $input->SetMch_id($this->pay_config['mchid']);//微信支付分配的商户号
        $input->SetKey($this->pay_config['key']);//key
        $input->SetOut_trade_no($this->order_info['out_trade_no']);//设置商户系统内部的订单号
        $input->SetTotal_fee(floatval($this->order_info['total_fee']*100));//设置订单总金额，单位为分，只能为整数
        $input->SetRefund_fee(floatval($this->order_info['refund_fee']*100));//设置退款总金额，订单总金额，单位为分，只能为整数
        $input->SetOut_refund_no($this->order_info['refund_trade_no']);//设置商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔
        $input->SetTransaction_id('');//微信订单号
        $input->SetOp_user_id($this->pay_config['mchid']);//设置操作员帐号, 默认为商户号
		$cer['cert']=$this->pay_config['cert'];
		$cer['keyt']=$this->pay_config['keyt'];
        $result = WxPayApi::refund($input,6,$cer);
        return $result;
    }
    
    public function reSign($order,$key,$paytype='APP'){
        //重新签名 by:ll 2016.1.31 
        $newinput = new WxPayUnifiedOrder();
        if($paytype=='APP'){
            $newinput->values['appid'] = $order['appid'];
            $newinput->values['partnerid'] = $order['mch_id'];
            $newinput->values['prepayid'] = $order['prepay_id'];
            $order['nonce_str'] = $newinput->values['noncestr'] = WxPayApi::getNonceStr();
            $order['timestamp'] = $newinput->values['timestamp'] = time();
            $newinput->values['package'] = "Sign=WXPay";
        }elseif($paytype=='JSAPI'){
            $newinput->values['appId'] = $order['appid'];
            $order['timestamp'] = $newinput->values['timeStamp'] = time();
            $order['nonce_str'] = $newinput->values['nonceStr'] = WxPayApi::getNonceStr();
            $newinput->values['package'] = 'prepay_id='.$order['prepay_id'];
            $newinput->values['signType'] = 'MD5';
        }
        $newinput->key = $key;
        $newinput->SetSign();
        $order['sign'] = $newinput->GetSign();
        return $order;
    }
    
}

