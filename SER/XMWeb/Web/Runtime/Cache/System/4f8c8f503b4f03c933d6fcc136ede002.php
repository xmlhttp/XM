<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 订单详情</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet"> 
    <script  src="/Public/jquery.js"></script>
    <script  src="/Public/jquery.form.js"></script>   
	<style>
	.info_tab{width:98%; margin-left:auto; margin-right:auto}
	.info_tab tr td{ height:30px; padding-top:3px; border-bottom:2px solid #f1f6fb; }
	</style> 
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a  href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/ManagerPage/Count">平台信息</a>><a href="javascript:void(0)">订单详情</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">订单详情</div>
    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">
    <tr>
		<td align="right" style="width:20%">本地编号：</td>
		<td align="left"><?php echo ($order["id"]); ?></td>
		<td align="right">微信编号：</td>
		<td align="left"><?php echo ($order["wxno"]); ?></td>
		<td align="right">订单编号：</td>
		<td align="left" style="width:20%"><?php echo ($order["No"]); ?></td>
    </tr>
	
	<tr>
		<td align="right">站点编号：</td>
		<td align="left"><?php echo ($order["sid"]); ?></td>
		<td align="right">站点名称：</td>
		<td align="left"><?php echo ($order["sname"]); ?></td>
		<td align="right">充电单价：</td>
		<td align="left"><?php echo ($order["uint"]); ?> 元/度</td>
    </tr>
	
	<tr>
		<td align="right">设备编号：</td>
		<td align="left"><?php echo ($order["pid"]); ?></td>
		<td align="right">设备名称：</td>
		<td align="left"><?php echo ($order["pname"]); ?></td>
		<td align="right">充值总额：</td>
		<td align="left"><?php echo ($order["smoney"]); ?> 元</td>
    </tr>
	
	<tr>
		<td align="right">商家编号：</td>
		<td align="left"><?php echo ($order["bid"]); ?></td>
		<td align="right">商家名称：</td>
		<td align="left"><?php echo ($order["bname"]); ?></td>
		<td align="right">实收金额：</td>
		<td align="left"><?php echo ($order["money"]); ?> 元</td>
    </tr>
	<tr>
		<td align="right">用户编号：</td>
		<td align="left"><?php echo ($order["uid"]); ?></td>
		<td align="right">用户名称：</td>
		<td align="left"><?php echo ($order["uname"]); ?></td>
		<td align="right">退款金额：</td>
		<td align="left"><?php echo ($order["tmoney"]); ?> 元</td>
    </tr>
	<tr>
		<td align="right">临时编号：</td>
		<td align="left"><?php echo ($order["mid"]); ?></td>
		<td align="right">临时名称：</td>
		<td align="left"><?php echo ($order["mname"]); ?></td>
		<td align="right">充电状态：</td>
		<td align="left"><?php echo ($order["isstatus"]); ?></td>
    </tr>
	
	<tr>
		<td align="right">开始电量：</td>
		<td align="left"><?php echo ($order["elecount"]); ?> 度</td>
		<td align="right">结束电量：</td>
		<td align="left"><?php echo ($order["eleend"]); ?> 度</td>
		<td align="right">充电度数：</td>
		<td align="left"><?php echo ($order["cpower"]); ?> 度</td>
    </tr>
	
	<tr>
		<td align="right">开始时间：</td>
		<td align="left"><?php echo ($order["addtime"]); ?></td>
		<td align="right">结束时间：</td>
		<td align="left"><?php echo ($order["lasttime"]); ?></td>
		<td align="right">修改状态：</td>
		<td align="left"><?php echo ($order["isenable"]); ?></td>
    </tr>
	
	<tr>
		<td align="right">停止原因：</td>
		<td align="left"><?php echo ($order["endtxt"]); ?></td>
		<td align="right">充电失败：</td>
		<td align="left"><?php echo ($order["starterrtxt"]); ?></td>
		<td align="right">结束说明：</td>
		<td align="left"><?php echo ($order["endfatxt"]); ?></td>
    </tr>
	<tr>
		<td align="right">订单状态：</td>
		<td align="left"><?php echo ($order["isclose"]); ?></td>
		<td align="right"></td>
		<td align="left"></td>
		<td align="right"></td>
		<td align="left"></td>
    </tr>
	
  </table>
  </div>
<div id="footer" class="info_foot">
	<script>document.write(cmsname)</script>
</div>
</div>
</body>
</html>