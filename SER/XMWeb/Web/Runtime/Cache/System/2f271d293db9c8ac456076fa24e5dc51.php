<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head >
    <title>管理中心 - 起始页</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/Web/System/Public/css/main.css" type=text/css rel=stylesheet>
    <script  src="/Public/jquery.js"></script> 
</head>
<body>
<!--顶部导航开始-->
<div class="topnav">
<a href="/System.php?s=/System/ManagerPage/BaseInfo.html" class="home">首页</a>><a href="/System.php?s=/System/ManagerPage/BaseInfo.html">版权信息</a>
</div>
<!--顶部导航结束-->
<div class="cont_info">
<div class="tab_txt">
<div class="tab_tit">基本信息</div>
 
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ver_tab">
  
  <tr>
    <td width="35%"  style="text-align:center">
        版权信息</td>
    <td width="65%" align=""left><span class="STYLE2">
        您好,<?=session("admin")?>
        .&nbsp; 欢迎使用 充电平台VM.PHP 1.0 &nbsp;共享充电管理平台</span></td>
  </tr>
   
  <tr>
    <td></td>
    <td>
        产品开发  广州市充电平台管理系统 © 版权所有</td>
  </tr>
  <tr>
    <td></td>
    <td>
        技术支持 http://www.vmuui.com/</td>
  </tr>
    <tr>
        <td >
        </td>
        <td>
            联系方式 Email : &nbsp; 469100943@qq.com &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; TEL :&nbsp;138 2971 9806&nbsp;</td>
    </tr>
</table>
<div style="border-top:1px dashed #ccc; height:1px; width:96%; margin-left:auto; margin-right:auto"></div>
<table width="100%" border="0" cellpadding="0" cellspacing="2" class="FW_TAB">
  <tr>
    <td width="49%"><table width="100%" border="0" cellpadding="2" cellspacing="1" class="STYLE2">
      
      <tr>
        <td width="45%" height="20" align="right" >
           来访域名：</td>
        <td width="55%" align="left"><?php echo ($_SERVER["SERVER_NAME"]); ?></td>
      </tr>
      <tr>
        <td height="20" align="right" >服务器IP：</td>
        <td  align="left"><?php echo ($name); ?></td>
      </tr>
      <tr>
        <td align="right">服务器端口：</td>
        <td height="20"  align="left"> 
           <?php echo ($_SERVER["SERVER_PORT"]); ?></td>
      </tr>
      <tr>
        <td align="right">服务器操作系统：</td>
        <td height="20"  align="left">
            <?php echo ($os); ?></td>
      </tr>
      <tr>
        <td align="right">执行时间限制：</td>
        <td height="20"  align="left">
           <?php echo ($ctime); ?></td>
      </tr>
      <tr>
        <td align="right">
            服务器物理地址：</td>
        <td height="20"  align="left">
            <?php echo ($_SERVER["DOCUMENT_ROOT"]); ?></td>
      </tr>
      <tr>
        <td align="right">
            来访者IP：</td>
        <td height="20"  align="left">
            <?php echo ($_SERVER["REMOTE_ADDR"]); ?></td>
      </tr>
      <tr>
        <td align="right">
            当前语言版：</td>
        <td height="20"  align="left"> 
           简体中文</td>
      </tr>
    </table></td>
    
    <td style="border-left:1px dotted #cccccc"  border="1" cellpadding="0" cellspacing="0">&nbsp;</td>

    
    <td width="49%"><table width="100%" border="0" cellpadding="2" cellspacing="1" class="STYLE2">
      
      <tr>
        <td width="36%" height="20" align="right" >
            服务器协议：
        </td>
        <td width="64%"  align="left">
           <?php echo ($_SERVER['SERVER_PROTOCOL']); ?> - <script>document.write((document.location.protocol).replace(":","").toUpperCase())</script></td>
      </tr>
      <tr>
        <td height="20" align="right" >
            服务器CPU数量：</td>
        <td  align="left"><span class="STYLE3">
           4</span></td>
      </tr>
      <tr>
        <td align="right">
            服务器时间：</td>
        <td height="20"  align="left" >
            <?php echo (date('Y-m-d g:i a',time())); ?></td>
      </tr>
      <tr>
        <td align="right">Stream 文件流：</td>
        <td height="20"  align="left"><span class="STYLE3">√ 支持</span></td>
      </tr>
      <tr>
        <td align="right">
            数据库类型：</td>
        <td height="20"  align="left"><span class="STYLE3">
           MySQL </span></td>
      </tr>
      <tr>
        <td align="right">Tp版本：</td>
        <td height="20"  align="left"><span class="STYLE3"><?php echo (THINK_VERSION); ?></span></td>
      </tr>
      <tr>
        <td align="right">服务端版本：</td>
        <td height="20"  align="left">
            <?php echo ($_SERVER["SERVER_SOFTWARE"]); ?></td>
      </tr>
      <tr>
        <td align="right">CGI脚本规范：</td>
        <td height="20"  align="left"><?php echo ($_SERVER["GATEWAY_INTERFACE"]); ?></td>
      </tr>
    </table></td>
  </tr>
</table>
</DIV>
<DIV id="footer" class="info_foot">
   <script>document.write(cmsname)</script>
</DIV>
</div>
</body>
</html>