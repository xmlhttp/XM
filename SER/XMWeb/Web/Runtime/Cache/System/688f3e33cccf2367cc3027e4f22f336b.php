<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 修改管理员</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet"> 
    <link rel="stylesheet" type="text/css" href="/Web/System/Public/Tool/codebase/dhtmlxtree.css">
    <script  src="/Public/jquery.js"></script>
	<script  src="/Public/jquery.form.js"></script>
  	<script  src="/Web/System/Public/Tool/codebase/dhtmlxtree.js"></script>
	<script type="text/javascript" src="/Web/System/Public/js/vmupload.js"></script>
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a  href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/AdminAll">系统信息</a>><a href="javascript:void(0)">修改管理员</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">修改管理员</div>
	<form method="post" action="<?php echo U('/System/AdminAll/EditSave',array('id'=>I('get.id')));?>" id="form1"  enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">
    <tr>
      <td width="30%" align="right">登录账号：</td>
      <td align="left"><input type="text" id="username" name="username" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  value="<?php echo ($sysadmin["username"]); ?>"  disabled="disabled"/>
        </td>
    </tr>
    <tr>
      <td align="right">真实姓名：</td>
      <td  align="left"><input type="text" id="name" name="name" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  value="<?php echo ($sysadmin["name"]); ?>" /><span style=" margin-left:5px; color:#F00; display:none" id="name_tip">×姓名不能为空</span>
      </td>
    </tr>
    <tr>
      <td align="right">登录密码：</td>
      <td  align="left"><input type="password" id="pwd" name="pwd" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  /><span style=" margin-left:5px; color:#00F;" >*不填则不修改</span>
      </td>
    </tr>
    <tr>
      <td align="right">确认密码：</td>
      <td  align="left"><input type="password" id="repwd" name="repwd" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"   value=""/><span style=" margin-left:5px; color:#F00; display:none" id="repwd_tip">×两次密码不一致</span>
        </td>
    </tr>
	
	
    <tr>
      <td align="right">是否启用：</td>
      <td  align="left">
		<input type="radio" id="working" name="working" value="1" <?php if($sysadmin["working"] == 1): ?>checked="checked"<?php endif; ?>/>启用
      	<input type="radio" id="working1" name="working" value="0" style="margin-left:20px;" <?php if($sysadmin["working"] == 0): ?>checked="checked"<?php endif; ?>/>禁用
      </td>
    </tr>
	
	
	
    <tr>
      <td align="right">管理类别：</td>
      <td  align="left">
      	<input type="radio" id="adminclass" name="adminclass" value="0" <?php if($sysadmin["adminClass"] == 0): ?>checked="checked"<?php endif; ?> />商家
      	<input type="radio" id="adminclass1" name="adminclass" value="1" style="margin-left:20px;" <?php if($sysadmin["adminClass"] == 1): ?>checked="checked"<?php endif; ?>/>系统
      </td>
    </tr>
	<tr>
      <td align="right">备注信息：</td>
      <td  align="left"><textarea class="input1" id="mark" name="mark" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:424px; height:36px"><?php echo ($sysadmin["mark"]); ?></textarea>
      </td>
    </tr>
	
	
	<tr style="display:none">
      <td align="right">商家mchid：</td>
      <td  align="left"><input type="text" id="mchid" name="mchid" class="input1" value="<?php echo ($sysadmin["mchid"]); ?>" style="width:240px"  />
      </td>
    </tr>
	
	<tr  style="display:none">
      <td align="right">商家KEY：</td>
      <td  align="left"><input type="text" id="mchkey" name="mchkey" class="input1" value="<?php echo ($sysadmin["mchkey"]); ?>" style="width:240px"  />
      </td>
    </tr>
	
	<tr  style="display:none">
		<td align="right">商家证书：</td>
		<td  align="left" style="height:86px">
		
        <div class="vmupload" style="<?php if(empty($sysadmin['upfile'])): else: ?>background:none<?php endif; ?>">
        <?php if(empty($sysadmin['upfile'])): ?><img class="vmupimg" src="" />
        <?php else: ?>
        	<img class="vmupimg" src="<?php echo ($sysadmin['upfile']); ?>" style="display:block" /><?php endif; ?>
            <div class="vmsame">
        	<div class="vmuptxt">
            	<div class="vmuptxtbg"></div>
                <div class="vmupname">CER证书</div>
                <div class="vmupsize">.PEM格式</div>
			</div>
            <input type="file" id="upfile" name="upfile" />
            </div>
            <img src="/Web/System/Public/images/swfupload/fancy_close.png" class="vmupclose" <?php if(empty($sysadmin['upfile'])): else: ?> style=" display:block"<?php endif; ?> />
        </div>
		
		 
        <div class="vmupload" style="<?php if(empty($sysadmin['upfile2'])): else: ?>background:none<?php endif; ?>;">
        <?php if(empty($sysadmin['upfile2'])): ?><img class="vmupimg" src="" />
         <?php else: ?>
        	<img class="vmupimg" src="<?php echo ($sysadmin['upfile2']); ?>"  style="display:block"/><?php endif; ?>
            <div class="vmsame">
        	<div class="vmuptxt">
            	<div class="vmuptxtbg"></div>
                <div class="vmupname">KEY证书</div>
                <div class="vmupsize">.PEM格式</div>
			</div>
            <input type="file" id="upfile2" name="upfile2"/>
            </div>
            <img src="/Web/System/Public/images/swfupload/fancy_close.png" class="vmupclose" <?php if(empty($sysadmin['upfile2'])): else: ?> style=" display:block"<?php endif; ?>/>
        </div>
		
        <div class="vmimgdesc" style="width:192px">1、此处上传商家证书<br>2、将对应的证书上传指定控件中<br>3、格式为PEM</div> 		
		</td>
    </tr>

	<tr>
          <td align="right">
              权限设置：</td>
          <td  align="left"><span id=actinfo ><font color =green><img src="/Web/System/Public/images/msg/loading.gif"  style="vertical-align:middle; margin-left:2px; margin-right:2px;" /> 权限模块载入中…</font></span><div id="treeboxbox_tree" style="border:1px solid Silver; display:none ; overflow:auto; margin-top:0px; margin-bottom:0px; width:413px;" class="seo_desc"></div>
              <input type="hidden"  id="hiden" name="hiden"  value="<?php echo ($sysadmin["parts"]); ?>" />
          </td>
      </tr>
    <tr>
      <td align="right" height="50"></td>
      <td  align="left"><input type="button" class="btn" value="修改管理员" id="addsave" style=" width:144px; height:30px" /> 
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
	function toncheck(id,state){
		$("#hiden").val(tree.getAllChecked());
	}
			
	tree=new dhtmlXTreeObject("treeboxbox_tree","100%","100%",0);
	tree.setImagePath("/Web/System/Public/Tool/codebase/imgs/dhxtree_material/");
	tree.enableCheckBoxes(1);
	tree.enableThreeStateCheckboxes(true);
	tree.setOnCheckHandler(toncheck);
	tree.loadXML("/System.php?s=/System/AdminAll/adminMenu1&ids=<?php echo ($sysadmin["parts"]); ?>");
	show();
    
	function show(){
		$("#actinfo").hide()
		$('#treeboxbox_tree').show();
	}
	$("#addsave").click(function(){
		$("#username_tip,#name_tip,#pwd_tip,#repwd_tip").hide();
		if($("#name").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#name_tip").show();
			alert("姓名不能为空")
			return false;	
		}	
		if($("#pwd").val().replace(/(^\s*)|(\s*$)/g, "").length!=0){	
			if($("#pwd").val()!=$("#repwd").val()){
				$("#repwd_tip").show();
				alert("两次密码不一致")
				return false;	
			}
		}
		$("#form1").ajaxSubmit({
			dataType:'json',
			success: function(d) {
				if(d["status"]["err"]==0){
					window.location.href="<?php echo U('/System/AdminAll');?>"
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
		})
		return false
	})	

</script>


</body>
</html>