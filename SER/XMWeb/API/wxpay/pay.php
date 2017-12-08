<?php
ini_set('date.timezone','Asia/Shanghai');
require_once dirname(__FILE__)."/WxPay.Api.php";

class Pay{
    
    protected $order_info;
  //  protected $pay_config;
    
    public function __construct($order_info){
        $this->order_info = $order_info;
        //$this->pay_config = $pay_config;
    }
    //充值
    public function pay(){
        $input = new WxPayUnifiedOrder();

        $input->SetBody($this->order_info['order_info']);//商品或支付单简要描述
        $input->SetAttach($this->order_info['mid']);//附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetOut_trade_no($this->order_info['out_trade_no']);//商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee(floatval($this->order_info['total_fee']));//订单总金额，只能为整数，详见支付金额
        $input->SetTime_start(date("YmdHis",strtotime($this->order_info['add_time'])));//订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
        $input->SetTime_expire(date("YmdHis",strtotime($this->order_info['add_time']) + 108000));//设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
        $input->SetNotify_url($this->order_info['notify_url']);//接收微信支付异步通知回调地址
        $input->SetTrade_type('JSAPI');//设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        $input->SetOpenid($this->order_info['openid']);//用户openid        
		$order = WxPayApi::unifiedOrder($input);
		//header("Content-type: text/html; charset=utf-8"); 
		//var_dump($order);
		//exit;
        $result = $this->reSign($order);
        return $result;
    }
    //退款
    public function refund(){
        $input = new WxPayRefund();
        $input->SetOut_trade_no($this->order_info['out_trade_no']);//设置商户系统内部的订单号
        $input->SetTotal_fee(floatval($this->order_info['total_fee']));//设置订单总金额，单位为分，只能为整数
        $input->SetRefund_fee(floatval($this->order_info['refund_fee']));//设置退款总金额，订单总金额，单位为分，只能为整数
        $input->SetOut_refund_no($this->order_info['refund_trade_no']);//设置商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔
        $input->SetOp_user_id(WxPayConfig::MCHID);//设置操作员帐号, 默认为商户号
	    $result =WxPayApi::refund($input);
        return $result;
    }
    //重签名
    public function reSign($order){
        //重新签名 by:ll 2016.1.31 
		$jsapi =new WxPayJsApiPay();
 		$jsapi->SetAppid($order['appid']);
		$timeStamp=time();
		$order['timestamp']=$timeStamp;
		$jsapi->SetTimeStamp("$timeStamp");
		$rnd=WxPayApi::getNonceStr();
		$order['nonce_str']=$rnd;
		$jsapi->SetNonceStr($rnd);
		$jsapi->SetPackage('prepay_id='.$order['prepay_id']);
		$jsapi->SetSignType('MD5');
		$jsapi->SetPaySign($jsapi->MakeSign());
        $order['sign'] = $jsapi->GetPaySign();
        return $order;
    }
	//提现
	public function backMoney(){
		$input = new WxBackCharge();
		//$input->SetMch_Appid($this->order_info['mch_appid']);
		$input->SetDevice_info($this->order_info['device_info']);
		$input->SetPartner_Trade_No($this->order_info['partner_trade_no']);
		$input->SetOpenid($this->order_info['openid']);
		$input->SetCheck_Name('FORCE_CHECK');
		$input->SetRe_User_Name($this->order_info['re_user_name']);
		$input->SetAmount($this->order_info['amount']);
		$input->SetDesc($this->order_info['desc']);
		$result =WxPayApi::backCharge($input);
		return $result;
	}
    
}

