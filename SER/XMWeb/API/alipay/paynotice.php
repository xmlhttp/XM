<?php
/*
 * 支付宝回调 by:ll 2016/1/25
 * 
 * return #
 */

require_once(dirname(__file__)."/config.class.php");
require_once(dirname(__file__)."/alipay_notify.class.php");

class Pay_Notice{
    public function Notice(){

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify();
        $verify_result = $alipayNotify->verifyNotify();

        //处理验证结果
        if($verify_result){
            
            $total_fee = $_POST['total_fee'];
            $out_trade_no = $_POST['out_trade_no'];
            $trade_status = $_POST['trade_status'];

            if($trade_status == 'TRADE_SUCCESS'){
                //业务处理
                $res2 = $this->pay_result_manage($out_trade_no);
            }

            //响应支付宝 by:ll 2016/1/27
            echo 'success';

            if(!isset($res2)){
                $res2 = 0;
            }

            //写入日志记录
            $this->payLog($_POST, $total_fee, $out_trade_no, $res2);

        }else{
            echo 'fail';
        }
    }
    
    private function pay_result_manage($out_trade_no){
        $arr = require_once(dirname(__file__)."/../../Apps/Home/conf/config.php");
        $mysqli = new mysqli($arr['DB_HOST'],$arr['DB_USER'],$arr['DB_PWD'],$arr['DB_NAME'],$arr['DB_PORT']);

        $sql = "select id,username,total_fee,status from cdb_orderno where paytype='alipay' and out_trade_no='".$out_trade_no."'";
        $result = $mysqli->query($sql);

        if(!$result){
            return 0;
        }
        
        $check = $result->fetch_assoc();

        if(empty($check)){
            return 0;
        }

        if($check['status'] == 0){
            $sql = "select money from cdb_user where username='".$check['username']."'";
            $res1 = $mysqli->query($sql);
            if(!$res1){
                exit('找不到这个用户');
            }
            $money = $res1->fetch_assoc();

            $resultmoney = (float)$money['money'] + (float)$check['total_fee'];
            $updatemoney = (string)$resultmoney;

            $sql = "update cdb_user set money=".$updatemoney." where username='".$check['username']."'";
            $mysqli->query($sql);
            $res2 = $mysqli->affected_rows;

            $sql = "update cdb_orderno set status=1 where id='".$check['id']."'";
            $mysqli->query($sql);//添加一个验证状态 by:ll 2016/1/19
            
            $sql = "update cdb_fee_record set balance=".$resultmoney.",is_show=1 where username='".$check['username']."' and recordtype='3' and recordid='".$check['id']."'";
            $mysqli->query($sql);//更新流水

            //触发优惠检测 by:ll 2016.6.20 17:17
            $this->curl_check($check['id']);
            
            return $res2;
        }
    }
    
    private function payLog($postData,$total_fee,$out_trade_no,$res2){
        $file_path= dirname(__file__).'/../../Apps/Runtime/Logs/Home/'.date('Y-m-d', time()).'-alipaylog.txt';
        $con=date('Y-m-d H:i:s', time()).PHP_EOL."POST数据：".print_r($postData,true).PHP_EOL."total_fee数据：".$total_fee.PHP_EOL."out_trade_no数据：".$out_trade_no.PHP_EOL."数据库返回结果：".$res2.PHP_EOL;
        file_put_contents($file_path,$con,FILE_APPEND);
    }
    
    /*
    * 执行curl触发优惠检测 by:ll 2016.6.20
    * 
    * return #
    */
    public function curl_check($id){
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch , CURLOPT_URL, 'http://'.$_SERVER['HTTP_HOST'].'/Ningzhi/index.php?s=/Home/Base/check_rechargeCoupon&id='.$id);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch , CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
    }
}

$payNotice = new Pay_Notice();
$payNotice->Notice();
 exit;
?>