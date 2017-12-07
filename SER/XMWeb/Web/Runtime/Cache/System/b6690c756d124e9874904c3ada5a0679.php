<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 提交申请</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet"> 
    <link rel="stylesheet" type="text/css" href="/Web/System/Public/Tool/codebase/dhtmlxtree.css">
    <script  src="/Public/jquery.js"></script>
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a  href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/Person">个人中心</a>><a href="javascript:void(0)">申请提现</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">申请提现</div>
	<form method="post" action="<?php echo U('/System/Person/AddSave');?>" id="form1">
    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">   
    <tr>
      <td align="right" style="width:35%">提现金额：</td>
      <td align="left"><input type="text" id="money" name="money" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  /><span style=" margin-left:5px; color:#63F" id="pilenum_tip">当前可申请提现<?php echo ($person["money"]); ?>元</span>
        </td>
    </tr>    
    <tr>
      <td align="right">转人账户：</td>
      <td  align="left" style="height:30px">
		<input type="text" id="account" name="account" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  value="<?php echo ($person["zhifu"]); ?>" />
      </td>
    </tr>
    <tr>
      <td align="right">取款说明：</td>
      <td  align="left"> <textarea class="input1" id="desc" name="desc" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:424px; height:68px"></textarea>
      </td>
    </tr>
    
    <tr>
      <td align="right" height="50"></td>
      <td  align="left"><input type="button" id="addsave" class="btn" value="申请提现" style=" width:144px; height:30px" /> 
      </td>
    </tr>
  </table>
  </form>
  </div>
<div id="footer" class="info_foot">
	<script>document.write(cmsname)</script>
</div>
</div>

<script>	
$("#setland").click(function(){
	if($("#landx").val()==""){
		parent.M.setpark(10,10,0,$("#list1").val())	
	}else{
		parent.M.setpark($("#landx").val(),$("#landy").val(),$("#landr").val(),$("#list1").val())	
	}
})

function setland(x,y,r){
	$("#setland_tip").text("左边距："+x+"，上边距："+y+"，旋转："+r+"度")
	$("#landx").val(x);
	$("#landy").val(y);
	$("#landr").val(r);
}



	$("#addsave").click(function(){
		$("#pilenum_tip").hide();
		if($("#pilenum").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#pilenum_tip").show();
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
				window.location.href="<?php echo U('/System/PileListAll');?>"
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