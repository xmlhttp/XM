<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 个人中心</title>
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
<a href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="javascript:void(0)">综合信息</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">综合信息</div>
 
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ver_tab" style="font-family:'宋体'; letter-spacing:1px">
  
  <tr>
    <td width="30%"  style="text-align:center">
        综合信息</td>
    <td width="65%" align=""left>
        您好,<span><?=session("admin")?></span>!&nbsp;劲驰网络充电桩系统为您统计的信息如下：</td>
	</tr>
	<tr>
    <td></td>
    <td>您的所有设备累计提供充电服务<b><?php echo ($time["h"]); ?></b>小时<b><?php echo ($time["m"]); ?></b>分<b><?php echo ($time["s"]); ?></b>秒，共计<b><?php echo ($person["sele"]); ?></b>度电。</td>
  <tr>
  <tr>
    <td></td>
    <td>其中累计充电<b><?php echo ($person["snum"]); ?></b>次，共计取款<b><?php echo ($person["tnum"]); ?></b>次。</td>
  <tr>
  
    <td></td>
    <td>
        您的剩余金额<b><?php echo ($person["money"]); ?></b>元，累计金额<b><?php echo ($person["smoney"]); ?></b>元，累计取款<b><?php echo ($person["tmoney"]); ?></b>元。</td>
  </tr>

</table>
<hr style="border-top:1px dashed #ccc; height:1px" width=96% />
<form method="post" action="<?php echo U('/System/Person/PersonSave');?>" id="form1">
   <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">
    <tr>
      <td width="30%" align="right">真实姓名：</td>
      <td align="left"> <input type="text"  class="input1" disabled="disabled" style="width:300px; background:#eee" value="<?php echo ($person["name"]); ?>"  /></td>
    </tr>

    <tr>
      <td align="right">支付账户：</td>
      <td align="left"> <input type="text" id="zhifu" name="zhifu" class="input1" style="width:300px" value="<?php echo ($person["zhifu"]); ?>"/></td>
    </tr>   

  	<tr>
      <td align="right">商家说明：</td>
      <td align="left"> <textarea class="input1" id="mark" name="mark"  style="width:424px; height:68px" ><?php echo ($person["mark"]); ?></textarea></td>
    </tr>  
	<tr>
      <td align="right" height="50"></td>
      <td  align="left"><input type="button" id="addsave" class="btn" value="更新信息" style=" width:144px; height:30px" /> 
      </td>
    </tr>
  </table>
  </form>
</DIV>
<DIV id="footer" class="info_foot">
   <script>document.write(cmsname)</script>
</DIV>
</div>
<script>
$("#addsave").click(function(){
	if($("#zhifu").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
		alert("取款信息不能为空")
		return false;	
	}	
	var postdata=$("#form1").serialize();
	var url=$("#form1").attr("action");
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data:postdata
	}).done(function(d) {
		if(d["status"]["err"]==0){
			alert("修改成功！")
		}else if(d["status"]["err"]==1){
			alert(d["status"]["msg"]);
			window.parent.location.href="<?php echo U('/System/Index');?>"
		}else{
			alert(d["status"]["msg"])	
		}
	}).fail(function() {
			alert("网络连接错误，请稍后再试！")
	})
	return false
})	

</script>


</body>
</html>