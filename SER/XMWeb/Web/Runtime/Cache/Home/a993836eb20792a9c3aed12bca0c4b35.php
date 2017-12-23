<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<script src="/Public/jquery.js" type="text/javascript"></script>
<style>
*{ margin:0; padding:0}
ul,li{list-style-type:none}
.wxqr{ width:300px; height:350px; border:#eee solid 2px; position:absolute; left:50%; top:50%; margin-left:-152px; margin-top:-242px}
.wxstr{ padding-top:10px; line-height:30px; text-align:center;border-top:#eee solid 2px; font-size:14px; color:#888; letter-spacing:2px}
.wximg{ width:300px; height:300px; position:relative}
.wximg img{ width:300px; height:300px; position:absolute; left: 0; top:0}

.wxlist{ width:600px; height:500px; border:#eee solid 2px; position:absolute; left:50%;top:50%; margin-left:-302px; margin-top:-252px;display:none}
.wxleft{ float:left; border-right:#eee solid 2px; height:500px; width:150px;}
.wxright{ float:right; width:440px; margin-right:5px; height:500px}
.wxtop{ padding:5px; height:40px; line-height:40px; border-bottom:#eee solid 2px}
.wxtop span{ color:#787878; font-size:14px; margin-left:5px; letter-spacing:2px}

.wxcon{ height:445px; padding-left:5px; padding-right:5px;margin-top:2px; overflow-y:auto}
.wxconlist li{ padding-top:3px; padding-bottom:3px; border-bottom:#eee solid 1px}
.wxconlist li:hover,.wxconlist li.curli{ background:#f8f8f8}
.wxconlist li span{color:#787878; font-size:13px; margin-left:5px; letter-spacing:2px}

.wxrtop{border-bottom:#eee solid 2px; height:50px; line-height:50px; text-align:center; font-size:15px; letter-spacing:2px; color:#666;}
.wxrinfo{border-bottom:#eee solid 2px; height:315px; padding-top:5px; overflow-y:auto}
.wxrinput{border-bottom:#eee solid 2px}
.wxrinput textarea{ width:430px; border:0; height:70px; padding:5px; font-size:12px; color:#666; line-height:18px; letter-spacing:1px}
.wxrbtn{ padding-top:6px}
.wxrbtn a{ float:right; width:80px; height:30px; color:#666; font-size:13px; text-align:center; line-height:30px; border:#eee solid 1px; text-decoration:none}
.wxrbtn a:hover{ background:#f8f8f8}

.wxrmy{ background:#fafafa; border-radius:5px; padding:2px 10px; margin-bottom:8px; border:#eee solid 1px; width:90%; margin-left:auto; margin-right:auto}
.wxrmytop{ text-align:right; height:25px; line-height:25px; padding-bottom:2px; border-bottom:#ccc dashed 1px}
.wxrmytop span{ font-family:Verdana, Geneva, sans-serif; color:#666; font-size:13px; margin-right:10px}
.wxrmytop b{ color:#888; font-size:13px}
.wxrmytxt{ text-align:left; color:#777; font-size:12px; padding-top:5px; padding-bottom:5px; line-height:22px}
.wxrothertop{ text-align:left}
.wxrothertop span{  margin-right:0px; margin-left:10px;}

</style>
</head>

<body>
<div class="wxqr" id="wxqr">
<div class="wximg"><img id="ewm" /></div>
<div class="wxstr" id="wxstr">扫码登录</div>
</div>

<div class="wxlist" id="wxlist">
<!--左侧开始-->
<div class="wxleft">
<div class="wxtop">
<span id="myname">陆佳利</span>
</div>
<div class="wxcon">
<ul class="wxconlist" id="wxconlist">
</ul>
</div>
</div>
<!--左侧结束-->
<!--右侧开始-->
<div class="wxright">
<div class="wxrtop" id="wxrtoptit"></div>
<div class="wxrinfo">

<div class="wxrtxt" id="wxrtxt">

</div>

</div>

<div class="wxrinput">
<textarea placeholder="发送信息" id="txt"></textarea>
</div>
<div class="wxrbtn">
<a href="javascript:sendMsg()">发 送</a>
</div>

</div>
<!--右侧结束-->
</div>

<script>
Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}


//常量定义
var st=null,
	code=0,
	userAvatar ='',
	redirect_uri='',
	Mlist=null,
	cListid=0,
	synccheck,
	uname="",
	uuid="<?php echo ($code); ?>";
//JQ开始执行
$(function(){
	
	$("#ewm").attr("src","https://login.weixin.qq.com/qrcode/"+uuid)
	st=setTimeout(function(){
		chkLogin()
	},1000)

	//选择列表
	$("#wxconlist").delegate("li","click",function(){
		cListid=$(this).attr("rel")
		$("#wxrtxt").empty();
		$(this).addClass("curli").siblings("li").removeClass("curli")
		$("#wxrtoptit").text(Mlist[cListid]["RemarkName"]==""?Mlist[cListid]["NickName"]:Mlist[cListid]["RemarkName"])
	})
	
})
//循环检测是否登录
function chkLogin(){
	var POST_DATA={"uuid":uuid}
	$.ajax({
		url: '/index.php?s=/Home/Test/chk_login',
		type: 'POST',
        dataType:'json',
        data: POST_DATA
	}).done(function(d) {
		console.log(d)
		if(d["status"]["err"]==0){
			eval(d["jscode"])
			if(window.code==201){
				//console.log("已扫描")
				$("#wxstr").text("已扫描")
				$("#ewm").attr("src",window.userAvatar).css({"width":"120px","height":"120px","left":"90px","top":"90px"})
				st=setTimeout(function(){
					chkLogin()
				},500)	
			}else if(window.code==200){
				//console.log("已登录")	
				$("#wxstr").text("正在登录")
				getTicket()
			}else if(window.code==408){
				st=setTimeout(function(){
					chkLogin()
				},500)	
			}else if(window.code==400){
				alert("二维码超时")
			}
			
		}else{
			alert(d["status"]["msg"])	
		}
	}).fail(function(d) {
		alert("网络错误")
	})	
}
//获取初始化需要的数据
function getTicket(){
	var POST_DATA={ticket:window.redirect_uri,"uuid":uuid}
	console.log(POST_DATA)
	$.ajax({
		url: '/index.php?s=/Home/Test/getTicket',
		type: 'POST',
        dataType:'json',
        data: POST_DATA
	}).done(function(d) {
		console.log(d)
		if(d["status"]["err"]==0){
			uname=d["res1"]["User"]["NickName"]
			$("#myname").text(uname)
			var conlist="";
			if(d["res3"]["MemberCount"]>=0){
			
				for(var i=0;i<d["res3"]["MemberCount"];i++){
					if(i==0){
						conlist+="<li rel="+i+" class='curli'><span>"+(d["res3"]["MemberList"][i]["RemarkName"]==""?d["res3"]["MemberList"][i]["NickName"]:d["res3"]["MemberList"][i]["RemarkName"])+"</span></li>"
					}else{
						conlist+="<li rel="+i+"><span>"+(d["res3"]["MemberList"][i]["RemarkName"]==""?d["res3"]["MemberList"][i]["NickName"]:d["res3"]["MemberList"][i]["RemarkName"])+"</span></li>"	
					}
				}
				$("#wxrtoptit").text(d["res3"]["MemberList"][cListid]["RemarkName"]==""?d["res3"]["MemberList"][cListid]["NickName"]:d["res3"]["MemberList"][cListid]["RemarkName"])
			}
			$("#wxconlist").html(conlist)
			Mlist=d["res3"]["MemberList"];
			sendHeart();
			$("#wxqr").fadeOut()
			$("#wxlist").fadeIn()
		}else{
			alert(d["status"]["msg"])	
		}
	}).fail(function(d) {
		alert("网络错误")
	})	
}
//心跳
function sendHeart(){
	$.ajax({
		url: '/index.php?s=/Home/Test/sendHeart',
		type: 'POST',
        dataType:'json'
	}).done(function(d) {
		console.log(d)
		eval(d["res"])
		console.log(window.synccheck["retcode"]+"------"+window.synccheck["selector"])
		
		setTimeout(function(){
			sendHeart()	
		},5000)
		if(window.synccheck["retcode"]==0&&window.synccheck["selector"]==2){
			getMsg()
		}
	}).fail(function(d) {
		alert("网络错误")
	})	
}
//发送消息
function sendMsg(){
	if(cListid==-1){
		alert("请选择发送人")
		return 	
	}
	
	var POST_DATA={"content":$("#txt").val(),"ToUserName":Mlist[cListid]["UserName"]}
	$("#txt").val('')
	console.log(POST_DATA)
	$.ajax({
		url: '/index.php?s=/Home/Test/sendMsg',
		type: 'POST',
        dataType:'json',
        data: POST_DATA
	}).done(function(d) {
		console.log(d)
		var str='<div class="wxrmytop"><span>'+new Date().Format("yyyy-MM-dd hh:mm:ss")+'</span><b>'+uname+'</b></div><div class="wxrmytxt">'+POST_DATA['content']+'</div>';
		$('<div>',{"class":"wxrmy"}).html(str).appendTo("#wxrtxt")
		
		
	}).fail(function(d) {
		alert("网络错误")
	})	
	
	
}

function getMsg(){

	$.ajax({
		url: '/index.php?s=/Home/Test/getMsg',
		type: 'POST',
        dataType:'json'
	}).done(function(d) {
		console.log(d)
		var username=Mlist[cListid]["UserName"];
		console.log(username)
		var conlist=d['res']['AddMsgList']
		for(var i=0;i<conlist.length;i++){
			if(conlist[i]["FromUserName"]==username){
				var str='<div class="wxrmytop wxrothertop"><b>'+(Mlist[cListid]["RemarkName"]==""?Mlist[cListid]["NickName"]:Mlist[cListid]["RemarkName"])+'</b><span>'+(new Date(parseInt(conlist[i]['CreateTime']) * 1000)).Format("yyyy-MM-dd hh:mm:ss")+'</span></div><div class="wxrmytxt">'+conlist[i]['Content']+'</div>';
				$('<div>',{"class":"wxrmy"}).html(str).appendTo("#wxrtxt")
			}
		}
		
		
	}).fail(function(d) {
		alert("网络错误")
	})	
	
	
}


</script>
</body>
</html>