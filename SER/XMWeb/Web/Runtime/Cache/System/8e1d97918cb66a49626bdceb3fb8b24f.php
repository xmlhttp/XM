<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>Present by vmuui.com 管理中心 - 修改站点</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet"> 
    <script>var swfu,maxnum=5,webpath="/System.php?s=/System/SiteListAll/"</script>
    <script  src="/Public/jquery.js"></script>
    <script  src="/Public/jquery.form.js"></script>
	<script type="text/javascript" src="/Web/System/Public/js/swfupload.js"></script>
	<script type="text/javascript" src="/Web/System/Public/js/handlers.js"></script>
    <script type="text/javascript" src="/Web/System/Public/js/vmupload.js"></script>
	<style>
	.tab_tit span{ float:right; font-size:12px; margin-right:15px;letter-spacing:0}
	.tab_tit span b{ letter-spacing:0; color:#f66; font-family:Verdana; font-size:12px; padding-left:3px; padding-right:3px}
	</style>
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a  href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/SiteListAll">运营信息</a>><a href="javascript:void(0)">修改站点</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit"><span>站点统计：累计收入<b><?php echo ($count["smoney"]); ?></b>元，累计充电<b><?php echo ($count["sele"]); ?></b>度，累计服务时间<?php echo ($count["stime"]); ?></span>修改站点</div>
	<form method="post" action="<?php echo U('/System/SiteListAll/EditSave',array('id'=>I('get.id')));?>"  id="form1"  enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="2" cellspacing="0" class="info_tab">
    <?php if(session('adminclass') == 1 or session('adminclass') == 99): ?><tr>
      <td align="right">所属用户：</td>
      <td align="left"><input type="text" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px; background:#eee; color:#444" disabled="disabled"  value="<?php echo ($user["id"]); ?> — <?php echo ($user["username"]); ?>" />
      </td>
    </tr><?php endif; ?>
    <tr>
      <td align="right"  style="width:28%">站点名称：</td>
      <td align="left"><input type="text" id="sitename" name="sitename" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px"  value="<?php echo ($sitelist["sitename"]); ?>" /><span style=" margin-left:5px; color:#F00; display:none" id="sitename_tip">×站点名称不能为空</span>
        </td>
    </tr>
    <tr>
      <td align="right">站点地址：</td>
      <td  align="left"><input type="text" id="siteadd" name="siteadd" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:424px"  value="<?php echo ($sitelist["siteadd"]); ?>" /><span style=" margin-left:5px; color:#F00; display:none" id="siteadd_tip">×地址不能为空</span>
     
      </td>
    </tr>
    
     <tr style="display:none">
      <td align="right">站点详细地址：</td>
      <td  align="left">
     <textarea class="input1" id="siteinfoadd" name="siteinfoadd" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:424px; height:68px" ><?php echo ($sitelist["siteinfoadd"]); ?></textarea><span style=" margin-left:5px; color:#F00; display:none" id="siteinfoadd_tip">×站点详细地址不能为空</span>
      </td>
    </tr>
 	<tr>
      <td align="right">联系电话：</td>
      <td  align="left"><input type="text" id="sitetel" name="sitetel" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:424px"  value="<?php echo ($sitelist["sitetel"]); ?>" /><span style=" margin-left:5px; color:#F00; display:none" id="sitetel_tip">×联系电话不能为空</span>
     
      </td>
      </tr>
      <tr>
		<td align="right">添加图片：</td>
		<td  align="left" style="height:86px">
        
        <div class="vmupload" style="<?php if(empty($sitelist['siteimg'])): else: ?>background:none<?php endif; ?>">
        <?php if(empty($sitelist['siteimg'])): ?><img class="vmupimg" src="" />
        <?php else: ?>
        	<img class="vmupimg" src="<?php echo ($sitelist['siteimg']); ?>" style="display:block" /><?php endif; ?>
            <div class="vmsame">
        	<div class="vmuptxt">
            	<div class="vmuptxtbg"></div>
                <div class="vmupname">站点图片</div>
                <div class="vmupsize">大小:450*310</div>
			</div>
            <input type="file" id="img" name="img" />
            </div>
            <img src="/Web/System/Public/images/swfupload/fancy_close.png" class="vmupclose" <?php if(empty($sitelist['siteimg'])): else: ?> style=" display:block"<?php endif; ?> />
        </div>
        
        <div class="vmupload" style="<?php if(empty($sitelist['sitemap'])): else: ?>background:none<?php endif; ?>">
        <?php if(empty($sitelist['sitemap'])): ?><img class="vmupimg" src="" />
         <?php else: ?>
        	<img class="vmupimg" src="<?php echo ($sitelist['sitemap']); ?>"  style="display:block"/><?php endif; ?>
            <div class="vmsame">
        	<div class="vmuptxt">
            	<div class="vmuptxtbg"></div>
                <div class="vmupname">地形图片</div>
                <div class="vmupsize">大小任意</div>
			</div>
            <input type="file" id="bigimg" name="bigimg"/>
            </div>
            <img src="/Web/System/Public/images/swfupload/fancy_close.png" class="vmupclose" <?php if(empty($sitelist['sitemap'])): else: ?> style=" display:block"<?php endif; ?>/>
        </div>
        <div class="vmimgdesc">1、图片为站点显示图片和站点地形图<br>2、缩略图为等比缩放，站点地形图与前端显示无关<br>3、推荐上传指定大小的图片</div> 
                
		</td>
    </tr>
      <tr>
		<td style="vertical-align:top"><div style="text-align:right; height:30px; line-height:30px">轮播图片：</div></td>
		<td  align="left">
        <div style="height:30px" class="pl_topdiv">
			<div style="float:left; height:25px; width:100px; margin-top:3px;"><span id="spanButtonPlaceholder"></span></div>
			<div id="divFileProgressContainer" class="pl_hit"></div>
            <div class="progressjd" id="progressjd"></div>
        </div>
		<div id="thumbnails">
        	<ul id="pic_list" class="pic_list fixed"></ul>
		</div>
        <input type="hidden" name="pls" id="pls" value="<?php echo ($sitelist["siteimgs"]); ?>"/>
		</td>
    </tr>    
    <tr style="display:none">
      <td align="right">高德地图经纬度：</td>
      <td  align="left"><input type="button" id="sitxy" name="sitxy"  style="width:80px; height:30px" value="选择"  /><span style=" margin-left:10px; color:#06F;" id="sitxy_tip">经度：<?php echo ($sitelist["sitey"]); ?>，纬度：<?php echo ($sitelist["sitex"]); ?></span> 
      <span style="display:none"><input type="text" id="sitx" name="sitx"  value="<?php echo ($sitelist["sitex"]); ?>" /><input type="text" id="sity" name="sity"  value="<?php echo ($sitelist["sitey"]); ?>"/></span>
      </td>
    </tr>
    
    
     <tr style="display:none">
      <td align="right">百度地图经纬度：</td>
      <td  align="left"><input type="button" id="bsitxy" name="bsitxy"  style="width:80px; height:30px" value="选择"  /><span style=" margin-left:10px; color:#06F;" id="bsitxy_tip">经度：<?php echo ($sitelist["bsitey"]); ?>，纬度：<?php echo ($sitelist["bsitex"]); ?></span> 
      <span style="display:none"><input type="text" id="bsitx" name="bsitx" value="<?php echo ($sitelist["bsitex"]); ?>" /><input type="text" id="bsity" name="bsity" value="<?php echo ($sitelist["bsitey"]); ?>" /></span>
      </td>
    </tr>
    
     <tr>
      <td align="right">地图坐标：</td>
      <td  align="left"><input type="button" id="tsitxy" name="tsitxy"  style="width:80px; height:30px" value="选择"  /><span style=" margin-left:10px; color:#06F;" id="tsitxy_tip">经度：<?php echo ($sitelist["tsitey"]); ?>，纬度：<?php echo ($sitelist["tsitex"]); ?></span> 
      <span style="display:none"><input type="text" id="tsitx" name="tsitx" value="<?php echo ($sitelist["tsitex"]); ?>"/><input type="text" id="tsity" name="tsity" value="<?php echo ($sitelist["tsitey"]); ?>" /></span>
      </td>
    </tr>
   
    <tr>
      <td align="right">链接密码：</td>
      <td  align="left"><input type="text" id="linkpwd" name="linkpwd" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:240px" value="<?php echo ($sitelist["linkpwd"]); ?>"/><span style=" margin-left:5px; color:#06F" id="linkpwd_tip">*前端板卡必须与该密码相同</span> <span style=" margin-left:5px; color:#F00; display:none" id="innetip_tip">×链接密码不能为空</span>
      </td>
    </tr>
    
     <tr>
      <td align="right">单价：</td>
      <td  align="left"><input type="text" id="uint" name="uint" class="input1" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:140px" value="<?php echo ($sitelist["uint"]); ?>" /> 元<span style=" margin-left:5px; color:#F00; display:none" id="uint_tip">×单价不能为空</span>
      </td>
    </tr>
    
    
    <tr>
      <td align="right">备注信息：</td>
      <td  align="left"><textarea class="input1" id="mark" name="mark" onFocus="this.className='input1-bor'" onBlur="this.className='input1'"  style="width:424px; height:36px" ><?php echo ($sitelist["mark"]); ?></textarea>
        </td>
    </tr>

    <tr>
      <td align="right">是否启用：</td>
      <td  align="left">
		<input type="radio" id="isenable" name="isenable" value="1" checked="checked"  <?php if($sitelist["isenable"] == 1): ?>checked="checked"<?php endif; ?>/>是
      	<input type="radio" id="isenable1" name="isenable" value="0" style="margin-left:20px;"  <?php if($sitelist["isenable"] == 0): ?>checked="checked"<?php endif; ?>/>否
      </td>
    </tr>
    <tr>
      <td align="right" height="50"></td>
      <td  align="left"><input type="button" class="btn" value="修改站点" id="addsave" style=" width:144px; height:30px" /> 
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
	var pics='<?php echo ($sitelist["siteimgs"]); ?>';
	$(function(){
		$("#sitxy").click(function(){
			if($("#sitx").val()==""||$("#sity").val()==""){
				var sitxy={"x":38.32200483527823,"y":106.69221865624462,"z":4,"type":0}
			}else{
				var sitxy={"x":$("#sitx").val(),"y":$("#sity").val(),"z":19,"type":0}	
			}
			parent.M.showmap(sitxy);
		})
		
		$("#bsitxy").click(function(){
			if($("#bsitx").val()==""||$("#bsity").val()==""){
				var sitxy={"x":37.447122,"y":	105.514792,"z":5,"type":1}
			}else{
				var sitxy={"x":$("#bsitx").val(),"y":$("#bsity").val(),"z":19,"type":1}	
			}
			parent.M.showmap(sitxy);
		})
		
		$("#tsitxy").click(function(){
			if($("#tsitx").val()==""||$("#tsity").val()==""){
				var sitxy={"x":37.81037837267359,"y":101.38107635578348,"z":1,"type":2}
			}else{
				var sitxy={"x":$("#tsitx").val(),"y":$("#tsity").val(),"z":10,"type":2}	
			}
			parent.M.showmap(sitxy);
		})
		swfu = new SWFUpload({
				upload_url: "<?php echo U('/System/SiteListAll/Swfupload');?>",
				post_params: {"cid": "8"},
				file_size_limit : "2 MB",
				file_types : "*.jpg;*.png;*.gif;*.bmp",
				file_types_description : "JPG Images",
				file_upload_limit : maxnum,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				button_image_url : "/Web/System/Public/images/swfupload/upload.png",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 100,
				button_height: 25,
				button_text : '',
				button_text_style : '.spanButtonPlaceholder { font-family: Helvetica, Arial, sans-serif; font-size: 14pt;} ',
				button_text_top_padding: 0,
				button_text_left_padding: 0,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,			
				flash_url : "/Web/System/Public/images/swf/swfupload.swf",
				custom_settings : {
					upload_target : "divFileProgressContainer"
				},				
				debug: false
			});
		var picarr=pics.split("|");
		var j=0;
		
		for(var i=0;i<picarr.length;i++){
			if(picarr[i]!=""){
				 var newElement = "<li><img class='content'  src='" + picarr[i] + "' style=\"width:86px;height:86px; border:#fff solid 2px; background:#fff\"><img class='button' src="+path+"fancy_close.png></li>";
    			$("#pic_list").append(newElement);
    			$("img.button").last().bind("click", swfdel);
				j++;
			}	
		}
		if(j>0){
			$("#progressjd").width(400);
			$("#divFileProgressContainer,#progressjd").show();
			checkswf(j)
		}
	})

	function setpoint(a,b){
		if(b==0){
			$("#sitxy_tip").text("经度："+a["D"]+"，纬度："+a["J"]);
			$("#sitx").val(a["J"]);
			$("#sity").val(a["D"]);
		}else if(b==1){
			$("#bsitxy_tip").text("经度："+a["D"]+"，纬度："+a["J"]);
			$("#bsitx").val(a["J"]);
			$("#bsity").val(a["D"]);
		}else if(b==2){
			$("#tsitxy_tip").text("经度："+a["D"]+"，纬度："+a["J"]);
			$("#tsitx").val(a["J"]);
			$("#tsity").val(a["D"]);	
		}
	}
	
	$("#addsave").click(function(){
		$("#sitename_tip,#siteadd_tip,#sit_tip,#linkpwd_tip,#uint_tip,#sitetel_tip").hide();
		if($("#sitename").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#sitename_tip").show();
			return false;	
		}
		if($("#siteadd").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#siteadd_tip").show();
			return false;	
		}
		/*
		if($("#siteinfoadd").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#siteinfoadd_tip").show();
			return false;	
		}*/
		
		if($("#sitetel").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#sitetel_tip").show();
			return false;	
		}
		
		if($("#uint").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#uint_tip").show();
			alert('单价不能为空')
			return false;	
		}
		
		if(isNaN($("#uint").val().replace(/(^\s*)|(\s*$)/g, ""))){
			$("#uint_tip").show();
			alert('单价必须为数字')
			return false;		
		}
		if(parseFloat($("#uint").val().replace(/(^\s*)|(\s*$)/g, ""))<0.01){
			alert('单价必须大于1分钱')
			return false;		
		}
		
		
		/*
		if($("#sitx").val().replace(/(^\s*)|(\s*$)/g, "").length==0||$("#sity").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#sit_tip").show();
			return false;	
		}*/
		
		if($("#linkpwd").val().replace(/(^\s*)|(\s*$)/g, "").length==0){
			$("#linkpwd_tip").show();
			return false;	
		}
		
		
		$("#form1").ajaxSubmit({
			dataType:  'json',
			success: function(data) {
				if(data["status"]["err"]==0){
					window.location.href="<?php echo U('/System/SiteListAll');?>";
				}else if(data["status"]["err"]==1){
					alert(data["status"]["msg"]);
					window.parent.location.href="<?php echo U('/System/Index');?>"
				}else{
					alert(data["status"]["msg"])	
				}
			},
			error:function(xhr){
				alert("保存失败！")
			}
		});
		return false;	
	});
		

</script>


</body>
</html>