<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 添加管理员</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet"> 
    <link rel="stylesheet" type="text/css" href="/Web/System/Public/Tool/codebase/dhtmlxtree.css">
    <script src="/Public/jquery.js"></script>
	<script  src="/Public/jquery.form.js"></script>
  	<script src="/Web/System/Public/Tool/codebase/dhtmlxtree.js"></script>
	<script type="text/javascript" src="/Web/System/Public/js/vmupload.js"></script>
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a  href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/AdminAll">系统信息</a>><a href="javascript:void(0)">添加管理员</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">添加管理员</div>
	<form method="post" id="form1" action="<?php echo U('/System/AdminAll/AddSave');?>" enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">
    <tr>
      <td width="30%" align="right">登录账号：</td>
      <td align="left"><input type="text" id="username" name="username" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  /><span style=" margin-left:5px; color:#F00; display:none" id="username_tip">×登录名不能为空</span>
        </td>
    </tr>
    <tr>
      <td align="right">真实姓名：</td>
      <td  align="left"><input type="text" id="name" name="name" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  /><span style=" margin-left:5px; color:#F00; display:none" id="name_tip">×姓名不能为空</span>
      </td>
    </tr>
    <tr>
      <td align="right">登录密码：</td>
      <td  align="left"><input type="password" id="pwd" name="pwd" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  /><span style=" margin-left:5px; color:#F00; display:none" id="pwd_tip">×密码不能为空</span>
      </td>
    </tr>
    <tr>
      <td align="right">确认密码：</td>
      <td  align="left"><input type="password" id="repwd" name="repwd" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  /><span style=" margin-left:5px; color:#F00; display:none" id="repwd_tip">×两次密码不一致</span>
        </td>
    </tr>
	<tr>
      <td align="right">是否启用：</td>
      <td  align="left">
		<input type="radio" id="working" name="working" value="1" checked="checked" />启用
      	<input type="radio" id="working1" name="working" value="0" style="margin-left:20px;" />禁用
      </td>
    </tr>
	
	
    <tr>
      <td align="right">管理类别：</td>
      <td  align="left">
      	<input type="radio" id="adminclass" name="adminclass" value="0" checked="checked" />商家
      	<input type="radio" id="adminclass1" name="adminclass" value="1" style="margin-left:20px;" />系统
      </td>
    </tr>
	
	<tr>
      <td align="right">备注信息：</td>
      <td  align="left"><textarea class="input1" id="mark" name="mark" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:424px; height:36px" ></textarea>
      </td>
    </tr>
	
	<tr style="display:none">
      <td align="right">商家mchid：</td>
      <td  align="left"><input type="text" id="mchid" name="mchid" class="input1"  style="width:240px"  />
      </td>
    </tr>
	
	<tr  style="display:none">
      <td align="right">商家KEY：</td>
      <td  align="left"><input type="text" id="mchkey" name="mchkey" class="input1"  style="width:240px"  />
      </td>
    </tr>
	
	 <tr  style="display:none">
		<td align="right">商家证书：</td>
		<td  align="left" style="height:86px">
		
        <div class="vmupload">
    
        	<img class="vmupimg" src="" />
       
            <div class="vmsame">
        	<div class="vmuptxt">
            	<div class="vmuptxtbg"></div>
                <div class="vmupname">CER证书</div>
                <div class="vmupsize">.PEM格式</div>
			</div>
            <input type="file" id="upfile" name="upfile" />
            </div>
            <img src="/Web/System/Public/images/swfupload/fancy_close.png" class="vmupclose"/>
        </div>
		
		 
        <div class="vmupload">
        	<img class="vmupimg" src="" />
            <div class="vmsame">
        	<div class="vmuptxt">
            	<div class="vmuptxtbg"></div>
                <div class="vmupname">KEY证书</div>
                <div class="vmupsize">.PEM格式</div>
			</div>
            <input type="file" id="upfile2" name="upfile2"/>
            </div>
            <img src="/Web/System/Public/images/swfupload/fancy_close.png" class="vmupclose"/>
        </div>
		
        <div class="vmimgdesc" style="width:192px">1、此处上传商家证书<br>2、将对应的证书上传指定控件中<br>3、格式为PEM</div> 
		
		
		
		</td>
    </tr>
	
	
	
  

      <tr>
          <td align="right" >
              权限设置：</td>
          <td  align="left"><span id=actinfo ><font color =green><img src="/Web/System/Public/images/msg/loading.gif"  style="vertical-align:middle; margin-left:2px; margin-right:2px;" /> 权限模块载入中…</font></span><div id="treeboxbox_tree" style=" display:none ; overflow:auto; margin-top:0px; margin-bottom:0px; width:414px;" class="seo_desc"></div>
              <input type="hidden"  id="hiden" name="hiden" value="1,2,4,11,18,6,8,9,16,17,20,21,23,24,25,27,28" />
          </td>
      </tr>
    <tr>
      <td align="right" height="50"></td>
      <td  align="left"><input type="button" id="addsave" class="btn" value="添加管理员" style=" width:144px; height:30px" /> 
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
	};
			
	tree=new dhtmlXTreeObject("treeboxbox_tree","100%","100%",0);
	tree.setImagePath("/Web/System/Public/Tool/codebase/imgs/dhxtree_material/");
	tree.enableCheckBoxes(1);
	tree.enableThreeStateCheckboxes(true);
	tree.setOnCheckHandler(toncheck);
	tree.loadXML("/System.php?s=/System/AdminAll/adminMenu1&ids="+$("#hiden").val());
	show()
    
    function show(){
    	$("#actinfo").hide()
        $('#treeboxbox_tree').show();
    }
	$("#addsave").click(function(){
		$("#username_tip,#name_tip,#pwd_tip,#repwd_tip").hide();
		if($("#username").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#username_tip").show();
			alert("登录名不能为空");
			return false;	
		}
		if($("#name").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#name_tip").show();
			alert("姓名不能为空");
			return false;	
		}
		
		if($("#pwd").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#pwd_tip").show();
			alert("密码不能为空");
			return false;	
		}
		
		if($("#pwd").val()!=$("#repwd").val()){
			$("#repwd_tip").show();
			alert("两次密码不一致");
			return false;	
		}
		$("#form1").ajaxSubmit({
			dataType:  'json',
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