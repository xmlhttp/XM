<?php
/* 
 * 微信支付回调 by:ll 2016.1.29
 */

ini_set('date.timezone','Asia/Shanghai');
require_once ('WxPay.Api.php');  
require_once ('WxPay.Notify.php');  
class PayNotifyCallBack extends WxPayNotify  
{  
    //查询订单  
    public function Queryorder($transaction_id)  
    {  
	
        $input = new WxPayOrderQuery();  
        $input->SetTransaction_id($transaction_id);  
        $result = WxPayApi::orderQuery($input);  
        if(array_key_exists("return_code", $result)  
            && array_key_exists("result_code", $result)  
            && $result["return_code"] == "SUCCESS"  
            && $result["result_code"] == "SUCCESS")  
        {  
            return true;  
        }  
        return false;  
    }  
  
    //重写回调处理函数  
    public function NotifyProcess($data, &$msg)  
    {  
        //$notfiyOutput = array();  
        if(!array_key_exists("transaction_id", $data)){  
            $msg = "输入参数不正确";  
            return false;  
        }  
        //查询订单，判断订单真实性  
        if(!$this->Queryorder($data["transaction_id"])){  
            $msg = "订单查询失败";  
            return false;  
        }
		
		
		
		
		$arr = require_once(dirname(__file__)."/../../Web/Common/Conf/config.php");
        $mysqli = new mysqli($arr['DB_HOST'],$arr['DB_USER'],$arr['DB_PWD'],$arr['DB_NAME'],$arr['DB_PORT']);
		$sql = "select * from db_tempmoney where status=0 and orderid='".$data['out_trade_no']."'";
		$result = $mysqli->query($sql);
        if(!$result){
            $msg ='订单不存在!';
			return false;
		}
		
		$check = $result->fetch_assoc();
        if(empty($check)){
            $msg ='数据为空!';
			return false;
        }
		if($data['total_fee']!=$check['cuint']*100){
			$msg ='金额异常!';
			return false;
		}
		$sql="update db_tempmoney set status=1 where id=".$check["id"];
		$result1 = $mysqli->query($sql);
        if(!$result1){
            $msg ='修改状态失败!';
			return false;
		}	
        return true;  
    }  
}  
$notify = new PayNotifyCallBack();  
$notify->Handle(false); 