<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
*{ font-size:14px}
</style>
</head>

<body>
<form method="post" action="/index.php?s=/Home/Site/Scancode">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="scode" name="scode" value="http://temp.vmuui.com/index.php?s=/Home/Down/index/00000000000000000001" />
<input type="submit" value="充电" />
</form>

<form method="post" action="/index.php?s=/Home/Site/GetClostStatus">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="chargeid" name="chargeid" value="54" />
<input type="submit" value="获取停止状态" />
</form>



<form method="post" action="/index.php?s=/Home/Site/StopCharge">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="chargeid" name="chargeid" value="43" />
<input type="submit" value="停止充电" />
</form>
<div style="border-top:#aaa solid 2px; padding:5px; margin-top:10px">
<b>名称：</b>用户登录&lt;Android&gt;Login.java&lt;IOS&gt;<br>
<b>URL：</b>/index.php?s=/Home/User/Login<br>
<b>参数：</b><br>
username——<String>手机号11位<br>
userpwd——<String>密码6-16位<br>
<b>返回：</b><br>
username	"13829719806"<br>
sessionid	"9e95e2b31c97f12990c41653805b4b52"<br>
nickname	"发给yy"<br>
userimg	"/Web/UploadFile/UserInfo/2017-02-05/589688e487fc6.jpg"<br>
umoney	"9787.70"<br>
wx	0<br>
qq	0<br>
wb	0<br>
chargeid	0<br>
sitename	""<br>
pilenum	""<br>
uint	""<br>
status->	<br>
err	0<br>
msg	"登录成功！"<br>
</div>
<form method="post" action="/index.php?s=/Home/User/Login">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="userpwd" name="userpwd" value="123456" />
<input type="submit" value="登录" />
</form>
<div style="border-top:#aaa solid 2px; padding:5px; margin-top:10px">
<b>名称：</b>获取注册和修改密码验证码&lt;Android&gt;Reg.java|FindPwd.java&lt;IOS&gt;<br>
<b>注册URL：</b>/index.php?s=/Home/User/Regcode<br>
<b>找回密码URL：</b>/index.php?s=/Home/User/Getpwdcode<br>
<b>参数：</b><br>
tel——<String>手机号11位<br>
<b>返回：</b><br>
tel	"13829719806"<br>
code "231456"<br>
status->	<br>
err	0<br>
msg	"发送成功！"<br>
</div>

<form method="post" action="/index.php?s=/Home/User/Regcode">
<input type="text" id="tel" name="tel" value="<?php echo ($T["uname"]); ?>" />
<input type="submit" value="短信验证" />
</form>
<div style="border-top:#aaa solid 2px; padding:5px; margin-top:10px">
<b>名称：</b>用户注册与找回密码&lt;Android&gt;Reg.java|FindPwd.java&lt;IOS&gt;<br>
<b>用户注册URL：</b>/index.php?s=/Home/User/Register<br>
<b>找回密码URL：</b>/index.php?s=/Home/User/Getpwd<br>
<b>参数：</b><br>
tel——<String>手机号11位<br>
code——<String>验证码6位<br>
pwd——<String>密码6-16位<br>
<b>返回：</b><br>
status->{	<br>
err	0<br>
msg	"注册成功！"<br>
}
</div>
<form method="post" action="/index.php?s=/Home/User/Register">
<input type="text" id="tel" name="tel" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="code" name="code" value="" />
<input type="text" id="pwd" name="pwd" value="123456" />
<input type="submit" value="注册" />
</form>


<form method="post" action="/index.php?s=/Home/User/Getpwd">
<input type="text" id="tel" name="tel" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="code" name="code" value="" />
<input type="text" id="pwd" name="pwd" value="123456" />
<input type="submit" value="修改密码" />
</form>

<div style="border-top:#aaa solid 2px; padding:5px; margin-top:10px">
<b>名称：</b>修改昵称&lt;Android&gt;Cname.java&lt;IOS&gt;<br>
<b>URL：</b>/index.php?s=/Home/User/person_info_update<br>

<b>参数：</b><br>
username——<String>手机号11位<br>
sessionid——<String>sessionid 32位<br>
nickname——<String>昵称2-20位<br>
<b>返回：</b><br>
status->{	<br>
err	0<br>
msg	"注册成功！"<br>
}
</div>


<form method="post" action="/index.php?s=/Home/User/person_info_update">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="tel" name="nickname" value="<?php echo ($T["truename"]); ?>" />
<input type="submit" value="修改昵称" />
</form>
<div style="border-top:#aaa solid 2px; padding:5px; margin-top:10px">
<b>名称：</b>修改头像&lt;Android&gt;SetHead.java&lt;IOS&gt;<br>
<b>URL：</b>/index.php?s=/Home/User/person_info_update<br>

<b>参数：</b><br>
username——<String>手机号11位<br>
sessionid——<String>sessionid 32位<br>
pto——<String>File头像<br>
<b>返回：</b><br>
userimg "头像路径"<br>
status->{	<br>
err	0<br>
msg	"上传成功！"<br>
}
</div>

<form method="post" action="/index.php?s=/Home/User/person_info_update" enctype="multipart/form-data">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="file" id="pto1" name="pto" />
<input type="submit" value="修改头像" />
</form>


<div style="border-top:#aaa solid 2px; padding:5px; margin-top:10px">
<b>名称：</b>登录修改密码&lt;Android&gt;Cpwd.java&lt;IOS&gt;<br>
<b>URL：</b>/index.php?s=/Home/User/updatePwd<br>

<b>参数：</b><br>
username——<String>手机号11位<br>
sessionid——<String>sessionid 32位<br>
password1——<String>旧密码6-16位<br>
password2——<String>新密码6-16位<br>
password3——<String>再次密码6-16位<br>
<b>返回：</b><br>
status->{	<br>
err	0<br>
msg	"修改成功！"<br>
}
</div>
<form method="post" action="/index.php?s=/Home/User/updatePwd">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="password1" name="password1" value="" />
<input type="text" id="password2" name="password2" value="" />
<input type="text" id="password3" name="password3" value="" />
<input type="submit" value="登录修改密码" />
</form>




<form method="post" action="/index.php?s=/Home/Site/Site_list">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="submit" value="获取站点" />
</form>


<form method="post" action="/index.php?s=/Home/Site/Site_one">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="sid" name="sid" value="1" />
<input type="submit" value="获取单个站点" />
</form>



<form method="post" action="/index.php?s=/Home/Site/Site_list_one">
<input type="text" id="sid" name="sid" value="1" />
<input type="submit" value="获取单个站点" />
</form>


<form method="post" action="/index.php?s=/Home/Site/Pile_list">
<input type="text" id="sid" name="sid" value="1" />
<input type="submit" value="获取单个站点详情" />
</form>


<form method="post" action="/index.php?s=/Home/Site/SiteGetNum">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="chargeid" name="chargeid" value="3" />

<input type="submit" value="获取状111态" />
</form>

<form method="post" action="/index.php?s=/Home/User/my_order">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="submit" value="收藏" />
</form>


<form method="post" action="/index.php?s=/Home/User/paysave">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="out_trade_no" name="out_trade_no" value="dsfdsgfsdgsfdg" />
<input type="text" id="total_fee" name="total_fee" value="1000" />
<input type="text" id="paytype" name="paytype" value="weixin" />
<input type="submit" value="支付" />
</form>
<div style="border-top:#aaa solid 2px; padding:5px; margin-top:10px">
<b>名称：</b>获取版本&lt;Android&gt;SET.java&lt;IOS&gt;<br>
<b>URL：</b>/index.php?s=/Home/User/person_info_update<br>

<b>参数：</b><br>
username——<String>手机号11位<br>
sessionid——<String>sessionid 32位<br>
cid——<String>版本号：安卓为1，IOS位2<br>
<b>返回：</b><br>
ver	"2"<br>
url	"www.android.com"<br>
status->{	<br>
err	0<br>
msg	"获取成功！"<br>
}
</div>
<form method="post" action="/index.php?s=/Home/Index/getVer">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<select name="cid">
<option value="1">安卓</option><option value="2">IOS</option>
</select>
<input type="submit" value="获取版本" />
</form>

<form method="post" action="/index.php?s=/Home/User/my_collection">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="maxid" name="maxid" value="0" />
<input type="text" id="page" name="page" value="0" />
<input type="submit" value="我的收藏" />
</form>

<form method="post" action="/index.php?s=/Home/User/Myorder">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="maxid" name="maxid" value="0" />
<input type="text" id="page" name="page" value="0" />
<input type="submit" value="交易记录" />
</form>

<form method="post" action="/index.php?s=/Home/Site/StopCharge">
<input type="text" id="username" name="username" value="<?php echo ($T["uname"]); ?>" />
<input type="text" id="sessionid" name="sessionid" value="<?php echo ($T["sessionid"]); ?>" />
<input type="text" id="chargeid" name="chargeid" value="1" />
<input type="submit" value="订单结算" />
</form>

</body>
</html>