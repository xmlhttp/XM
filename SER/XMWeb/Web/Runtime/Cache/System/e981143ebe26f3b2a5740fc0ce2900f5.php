<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 -修改用户</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet"> 
    <script  src="/Public/jquery.js"></script>
    <script  src="/Public/jquery.form.js"></script>    
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a  href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/userAll">会员信息</a>><a href="javascript:void(0)">信息详情</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">信息详情</div>
	<form method="post">
    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">
    <tr>
      <td align="right"  style="width:28%">用户编号：</td>
      <td align="left"><span style="color:#06f; margin-left:5px" ><?php echo ($userinfo["id"]); ?></span>
        </td>
    </tr>
	
	<tr>
      <td align="right"  style="width:28%">用户类型：</td>
      <td align="left"><span style="color:#06f; margin-left:5px" ><?php echo ($utype); ?></span>
        </td>
    </tr>
	
	
     <tr>
      <td align="right">用户昵称：</td>
      <td  align="left"><span style="margin-left:5px"><?php echo ($userinfo["nickname"]); ?></span>    
      </td>
    </tr>
    <?php if($adminclass == 99 or $adminclass == 1): ?><tr>
      <td align="right"  style="width:28%">充电金额：</td>
      <td align="left"><span style="margin-left:5px"><?php echo ($userinfo["smoney"]); ?></span> 元</td>
    </tr>
	
	<tr>
      <td align="right"  style="width:28%">充电电量：</td>
      <td align="left"><span style="margin-left:5px"><?php echo ($userinfo["sele"]); ?></span> 度</td>
    </tr>
	
	<tr>
      <td align="right"  style="width:28%">充电时间：</td>
      <td align="left"><span style="margin-left:5px"><?php echo ($userinfo["stime"]); ?></span> 秒</td>
    </tr>
	
	<tr>
      <td align="right"  style="width:28%">充电次数：</td>
      <td align="left"><span style="margin-left:5px"><?php echo ($userinfo["snum"]); ?></span> 次</td>
    </tr><?php endif; ?>
    <tr>
      <td align="right"  style="width:28%">注册日期：</td>
      <td align="left"><span style="margin-left:5px"><?php echo ($userinfo["addtime"]); ?></span></td>
    </tr>
    
    <tr>
      <td align="right"  style="width:28%">最后登录：</td>
      <td align="left"><span style="margin-left:5px"><?php echo ($userinfo["lastaddtime"]); ?></span></td>
    </tr>
  </table>
  </form>
  </div>
<div id="footer" class="info_foot">
	<script>document.write(cmsname)</script>
</div>
</div>
</body>
</html>