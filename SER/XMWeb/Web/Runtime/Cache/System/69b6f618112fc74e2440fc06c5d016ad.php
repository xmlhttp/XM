<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 提交申请</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet"> 
    <link rel="stylesheet" type="text/css" href="/Web/System/Public/Tool/codebase/dhtmlxtree.css">
    <script  src="/Public/jquery.js"></script>
    <script  src="/Public/jquery.form.js"></script>
    <script type="text/javascript" src="/Web/System/Public/js/vmupload.js"></script>
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a  href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/Person">个人中心</a>><a href="javascript:void(0)">处理申请</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">处理申请</div>
	<form method="post" action="<?php echo U('/System/Person/EditSave',array('id'=>I('get.id')));?>" id="form1" enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">
    <tr>
      <td align="right">用户账号：</td>
      <td  align="left" style="height:30px">
		<input type="text" class="input1"  disabled="disabled" style="width:240px; background:#eee" value="<?php echo ($T["username"]); ?>" />
      </td>
    </tr>
    <tr style="display:none">
      <td align="right">用户邮箱：</td>
      <td  align="left" style="height:30px">
		<input type="text" class="input1"  disabled="disabled" style="width:240px; background:#eee" value="<?php echo ($T["email"]); ?>" />
      </td>
    </tr>
    <tr style="display:none">
      <td align="right">用户电话：</td>
      <td  align="left" style="height:30px">
		<input type="text" class="input1"  disabled="disabled" style="width:240px; background:#eee" value="<?php echo ($T["tel"]); ?>" />
      </td>
    </tr>
    <tr>
      <td align="right" style="width:35%">提现金额：</td>
      <td align="left"><input type="text" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'" readonly="readonly"  id="money" name="money" style="width:240px; background:#eee" value="<?php echo ($Te["money"]); ?>"  /><span style=" margin-left:5px; color:#63F" id="pilenum_tip">当前可申请提现 <font style="color:#f00; font-family:Verdana, Geneva, sans-serif; font-weight:bold; font-size:13px"><?php echo ($T["money"]); ?></font> 元</span>
        </td>
    </tr>    
    <tr>
      <td align="right">转入账户：</td>
      <td  align="left" style="height:30px">
		<input type="text" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'" disabled="disabled" style="width:240px; background:#eee" value="<?php echo ($Te["Account"]); ?>" />
      </td>
    </tr>
    <tr>
      <td align="right">取款备注：</td>
      <td  align="left"> <textarea class="input1" id="desctxt" name="desctxt" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:424px; height:68px"><?php echo ($Te["desctxt"]); ?></textarea>
      </td>
    </tr>
    <tr>
      <td align="right">凭证图片：</td>
      <td  align="left" style="height:86px"> 
        <div class="vmupload" style="<?php if(empty($Te['prove'])): else: ?>background:none<?php endif; ?>">
        <?php if(empty($Te['prove'])): ?><img class="vmupimg" src="" />
        <?php else: ?>
        	<img class="vmupimg" src="<?php echo ($Te['prove']); ?>" style="display:block" /><?php endif; ?>
            <div class="vmsame">
        	<div class="vmuptxt">
            	<div class="vmuptxtbg"></div>
                <div class="vmupname">凭证图片</div>
                <div class="vmupsize">大小不限</div>
			</div>
            <input type="file" id="img" name="img" />
            </div>
            <img src="/Web/System/Public/images/swfupload/fancy_close.png" class="vmupclose" <?php if(empty($Te['prove'])): else: ?> style=" display:block"<?php endif; ?> />
        </div>
      
      <div class="vmimgdesc">1、图片为打款后的凭证，一般为转账截图或扫描文件<br>2、用户可以查看凭证，请不要上传隐私信息<br>3、上传尺寸不限，文件大小最大不要超过1M</div> 
      </td>
    </tr>
    <tr>
      <td align="right">是否转账：</td>
      <td  align="left" style="height:30px"><?php if($Te['isdone'] == 1): ?>已转账
      <?php else: ?>
      <input type="radio" id="isdone1" name="isdone" value="1" <?php if($Te['isdone'] == 1): ?>checked="checked"<?php endif; ?> />转账
      	<input type="radio" id="isdone2" name="isdone" value="0" style="margin-left:20px;"  <?php if($Te['isdone'] == 0): ?>checked="checked"<?php endif; ?>/>不转账<span style="color:#f00; margin-left:10px;"><b>说明：</b>选择转账后无法撤销</span><?php endif; ?>
      </td>
    </tr>
    
    <tr>
      <td align="right" style="height:30px">是否处理：</td>
      <td  align="left"><input type="radio" id="isset1" name="isset" value="1" <?php if($Te['isset'] == 1): ?>checked="checked"<?php endif; ?> />是
      	<input type="radio" id="isset2" name="isset" value="0" style="margin-left:20px;"  <?php if($Te['isset'] == 0): ?>checked="checked"<?php endif; ?>/>否
      </td>
    </tr>
    
    
    <tr>
      <td align="right" height="40"></td>
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
$("#addsave").click(function(){
	if($("#money").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
		alert("取款金额不能为空")
		return false;	
	}
	if(isNaN($("#money").val())){
		alert("取款金额只能是数字")
		return false;		
	}	
	if(parseFloat($("#money").val())><?php echo ($T["money"]); ?>){
		alert("取款金额超出上限")
		return false;
	}	
	
	$("#form1").ajaxSubmit({
		dataType:'json',
		success: function(d) {
			if(d["status"]["err"]==0){
				alert(d["status"]["msg"])
				window.location.href="/System.php?s=/System/Person/GetMoneyAll"
			}else if(d["status"]["err"]==1){
				alert(d["status"]["msg"]);
				window.parent.location.href="<?php echo U('/System/Index');?>"
			}else{
				alert(d["status"]["msg"])	
			}
		},
		error:function(xhr){
			alert("保存失败！")
		}
	});
})
	
	
</script>


</body>
</html>