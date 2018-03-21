<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 添加管理员</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet"> 
    <link rel="stylesheet" type="text/css" href="/Web/System/Public/Tool/codebase/dhtmlxtree.css">
    <script  src="/Public/jquery.js"></script>
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a  href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/PileListAll">运营信息</a>><a href="javascript:void(0)">添加设备</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">添加设备</div>
	<form method="post" action="<?php echo U('/System/PileListAll/AddSave');?>" id="form1">
    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">
    <tr>
      <td width="30%" align="right">所属站点：</td>
      <td align="left"><select id="list1" name="list1"  style="width:250px; height:30px; line-height:30px"><?php echo ($option); ?></select>
        </td>
    </tr>
    
     <tr style="display:none">
      <td width="30%" align="right">充电类型：</td>
      <td align="left"><select id="list2" name="list2"  style="width:240px; height:30px; line-height:30px">
      					<option value="0">交流</option>
                        <option value="1">直流</option>
                        </select>
        </td>
    </tr>
    
    <tr>
      <td align="right">板卡名称：</td>
      <td align="left"><input type="text" id="pilenum" name="pilenum" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  /><span style=" margin-left:5px; color:#F00; display:none" id="pilenum_tip">×板卡名称不能为空</span>
        </td>
    </tr>  
	
	<tr>
      <td align="right">桩体编号：</td>
      <td align="left"><input type="text" id="pileNo" name="pileNo" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  /><span style=" margin-left:5px; color:#F00; display:none" id="pileNo_tip">×桩体编号不能为空</span>
        </td>
    </tr> 
	
	  
    <tr>
      <td align="right">车位选定：</td>
      <td  align="left" style="height:30px">
		<input type="button" id="setland" name="setland"  style="width:80px; height:30px" value="设定"  /><span style=" margin-left:10px; color:#06F;" id="setland_tip">*未设置</span> 
      <span style="display:none"><input type="text" id="landx" name="landx" /><input type="text" id="landy" name="landy" /><input type="text" id="landr" name="landr" /></span>
      </td>
    </tr>
   

    <tr>
      <td align="right">是否启用：</td>
      <td  align="left"  style="height:30px">
		<input type="radio" id="isenable" name="isenable" value="1" checked="checked" />是
      	<input type="radio" id="isenable1" name="isenable" value="0" style="margin-left:20px;" />否
      </td>
    </tr>
    <tr>
      <td align="right" height="50"></td>
      <td  align="left"><input type="button" id="addsave" class="btn" value="添加充电桩" style=" width:144px; height:30px" /> 
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
		if($("#pileNo").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#pileNo_tip").show();
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