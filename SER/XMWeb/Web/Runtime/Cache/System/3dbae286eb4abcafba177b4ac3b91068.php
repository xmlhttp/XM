<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Present by vmuui.com 管理中心 - 用户管理</title>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> 
    <link href="/Web/System/Public/css/main.css" type="text/css" rel="stylesheet">
	<link rel="STYLESHEET" type="text/css" href="/Web/System/Public/Tool/codebase/dhtmlxgrid.css">
	<script  src="/Public/jquery.js"></script>
	<script  src="/Web/System/Public/Tool/codebase/dhtmlxgrid.js"></script>
	<script type="text/javascript" src="/Web/System/Public/Tool/page/jquery.pagination.js"></script>
	<script  src="/Web/System/Public/js/System.js"></script>
<style>
     body{ overflow-y:hidden}
</style>
</head>
<body scroll="no">
<!--顶部导航开始-->
<div class="topnav">
<a href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/UserAll">会员信息</a>><a href="javascript:void(0)">会员列表</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">

<div class="tab_txt">
<div class="tab_tit">会员列表</div>

<div class="meun_tab">

  <table  width="95%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="30" align="center"><img src="/Web/System/Public/images/icon_search.gif" width="26" height="22"></td>
      <td width="60" align="right">关键字：</td>
      <td style="width:170px; text-align:center">
        <input name="Input" class="input1" id="keywords" onFocus="this.className='input1-bor'" onBlur="this.className='input1'" style="width:150px; height:18px; line-height:18px"/></td>
        <td align="left">
        <input name="button" type="button" class="btn" style="width:55px"  value="查询" id="search" onclick="searchitem()" /></td>
      <td  align="right">
        </td>
    </tr>
  </table>
</div>
<div class="db_div" >
<div id="gridbox" width="100%"></div>
</div>
<div id="setpage" class="paged" >
</div>
</div>
<DIV id="DIV1" class="info_foot">
  Action Info: <span id=act_info style="display:inline"></span>
</DIV>
</div>
<script>
	$("#keywords").val("");
var	myGridmode='/System.php?s=/System/UserAll/', //模块地址
	myGrid = new dhtmlXGridObject('gridbox');
	myGrid.setImagePath("/Web/System/Public/Tool/codebase/imgs/dhxgrid_material/");
<?php if((session('adminclass') == 1) OR (session('adminclass') == 99)): ?>var	myGridnum=9; //选中框所在的下标
	myGrid.setHeader("ID,会员来源,会员名称,充电次数,充电金额,充电电度,注册时间,最后登录,启用,详情");
	myGrid.setInitWidths("80,80,*,100,100,120,150,150,80,80");
	myGrid.setColAlign("center,center,left,center,center,center,center,center,center,center");
	myGrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ch,link");
	myGrid.setColSorting("int,str,str,str,str,str,str,str,str,str");
	myGrid.attachEvent("onEditCell",doOnCellEdit);
	myGrid.attachEvent("onCheckbox",doOnCheckEdit);
<?php else: ?>
	var	myGridnum=8; //选中框所在的下标
	myGrid.setHeader("ID,会员来源,会员名称,注册时间,最后登录,启用,详情");
	myGrid.setInitWidths("80,120,*,150,150,80,80");
	myGrid.setColAlign("center,center,left,center,center,center,center");
	myGrid.setColTypes("ro,ro,ro,ro,ro,img,link");
	myGrid.setColSorting("int,str,str,str,str,str,str");<?php endif; ?>
	myGrid.init();
</script>
</body>
</html>