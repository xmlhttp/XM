<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 平台统计</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/Web/System/Public/css/main.css" type=text/css rel=stylesheet>
    <script  src="/Public/jquery.js"></script> 
	<style>
	.ver_tab span{text-transform:capitalize; font-family:Verdana; letter-spacing:0; font-size:13px}
	.ver_tab b{ padding-left:3px; padding-right:3px; color:#f66;letter-spacing:0; font-size:13px}
	</style>
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="javascript:void(0)">统计信息</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">统计信息</div>
 
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ver_tab" style="font-family:'宋体'; letter-spacing:1px">
  
  <tr>
    <td width="30%"  style="text-align:center">
        平台统计信息</td>
    <td width="65%" align=""left>
        您好,<span><?=session("admin")?></span>!&nbsp;充电平台管理系统平台统计信息如下：</td>
	</tr>
	<tr>
    <td></td>
    <td>提供总充电服务<b><?php echo ($time["h"]); ?></b>小时<b><?php echo ($time["m"]); ?></b>分<b><?php echo ($time["s"]); ?></b>秒，共计<b><?php echo ($site["sele"]); ?></b>度电。</td>
  <tr>
  <tr>
    <td></td>
    <td>其中共计提供<b><?php echo ($site["snum"]); ?></b>次充电，商家共计提供取款<b><?php echo ($site["tnum"]); ?></b>次。</td>
  <tr>
  
    <td></td>
    <td>
        平台实有金额<b><?php echo ($site["money"]); ?></b>元，累计充电金额<b><?php echo ($site["smoney"]); ?></b>元，累计取款<b><?php echo ($site["tmoney"]); ?></b>元。</td>
  </tr>

</table>

</DIV>
<DIV id="footer" class="info_foot">
   <script>document.write(cmsname)</script>
</DIV>
</div>

</body>
</html>