<?php
/* 
 * 支付宝支付回调 by:ll 2017.11.29
 */
ini_set('date.timezone','Asia/Shanghai');
require_once 'AopSdk.php'; 
$key = require_once(dirname(__file__)."/../../Web/Home/Conf/config.php"); 
$aop = new AopClient;
$aop->alipayrsaPublicKey = $key['APUBKEY'];
$flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
if($flag){
	$arr = require_once(dirname(__file__)."/../../Web/Common/Conf/config.php");
   	if($_POST['trade_status']!='TRADE_SUCCESS'){
		echo 'failure';
		exit;
	}
  	$mysqli = new mysqli($arr['DB_HOST'],$arr['DB_USER'],$arr['DB_PWD'],$arr['DB_NAME'],$arr['DB_PORT']);
	$sql = "select * from db_amoney where status=0 and No='".$_POST['out_trade_no']."'";
	$result = $mysqli->query($sql);
	if(!$result){
		echo 'failure';
		exit;
	}
		
	$check = $result->fetch_assoc();
	if(empty($check)){
		echo 'failure';
		exit;
	}
	if($_POST['total_amount']*100!=$check['money']){
		echo 'failure';
		exit;
	}
	$sql="update db_amoney set status=1 where id=".$check["id"];
	$result1 = $mysqli->query($sql);
	if(!$result1){
		echo 'failure';
		exit;
	}	
	echo 'success';
}else{
	echo 'failure';
}