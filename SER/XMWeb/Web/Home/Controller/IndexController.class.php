<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {    

    /*
     * 主页
     * 
     * return #
    */
    public function index(){
		$T=M('sys_userinfo')->where('id=2')->find();
		$this->assign('T',$T);	
		$this->display('Index:mode');
    }
	/********************************************************************
	*
	*站点列表
	*/
	public function Site_list(){
		//$T=M('sitelist')->where(' isenable = 1 AND isdelete = 0')->select();
		$sql="select *,(select count(*) from db_pile where parentid=db_sitelist.id and isenable=1 and isdelete=0) as Cpower,(select count(*) from db_pile where parentid=db_sitelist.id  and isenable=1 and isdelete=0 and islink=1 and ptype=0 ) as Kpower,(select count(*) from db_pile where parentid=db_sitelist.id  and isenable=1 and isdelete=0 and islink=1 and ptype=0 and isnone<>1) as Kcar from db_sitelist where isenable = 1 AND isdelete = 0";
		
		
		
		if($T=M()-> query($sql)){
			$data=array();
			foreach($T as $t=>$v){
				$data[$t]['id']=$t;
				$data[$t]['sid']=(int)$v['id'];
				$data[$t]['sitename']=$v['sitename'];
				$data[$t]['sitetel']=$v['sitetel'];
				$data[$t]['siteimg']=$v['siteimg'];
				$data[$t]['siteimgs']=$v['siteimgs'];
				$data[$t]['siteadd']=$v['siteadd']==null?'':$v['siteadd'];
				$data[$t]['latitude']=$v['tsitex']==null?'':(float)$v['tsitex'];
				$data[$t]['longitude']=$v['tsitey']==null?'':(float)$v['tsitey'];
				$data[$t]['uint']=$v['uint']==null?'0.00':sprintf("%1.2f",(float)$v['uint']/100);
				$data[$t]['iconPath']="/resources/marker@2x.png";
				$data[$t]['width']=30;
				$data[$t]['height']=41;
				$data[$t]['Cpower']=$v['Cpower'];
				$data[$t]['Kpower']=$v['Kpower'];
				$data[$t]['Kcar']=$v['Kcar'];
			}
		   	$json['site']=$data;
			$json['tel']="13829719806";
			$json['status']['err']=0;
			$json['status']['msg']="执行成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;  
			
			
			
		}else{
			$json['status']['err']=1;
			$json['status']['msg']="数据库命令执行错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
	}
	/*************************
	*登录验证
	*/
	public function onLogin(){
		$code = I('post.code','','strip_tags');
		if($code==""){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		$result1=file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=".C('APPID')."&secret=".C('SECRET')."&js_code=".$code."&grant_type=authorization_code");
		$result=json_decode($result1, true);
		
		if(!isset($result["openid"])){
			$json['url']="https://api.weixin.qq.com/sns/jscode2session?appid=".C('APPID')."&secret=".C('SECRET')."&js_code=".$code."&grant_type=authorization_code";
			$json['status']['err']=1;
			$json['status']['msg']="参数解析有误！#".$result["errcode"];
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$openid=$result["openid"];
		$sessionkey=$result["session_key"];
		$T=M('sys_userinfo')->where('openid = "'.$openid.'" and utype=1')->select();
		if(count($T)==1){
			
			if($T[0]["ucheck"]==0){
				$json['status']['err']=2;
				$json['status']['msg']="账号被禁用！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
			$data['sessionid']=$sessionkey;
			$data['lastaddtime']=date('Y-m-d H:i:s');
			if(M('sys_userinfo')->where('id='.$T[0]['id'])->save($data)){
				
				$json['uid']=$T[0]['id'];
				$json['sessionid']=$sessionkey;
				$json['cele']=sprintf("%1.1f",(float)$T[0]['sele']/10);
				$json['cmoney']=sprintf("%1.2f",(float)$T[0]['smoney']/100);
				$json['headimg']=($T[0]['headimg']=="")?"/resources/headimg.jpg":$T[0]['headimg'];
				$json['nickname']=($T[0]['nickname']=="")?"未授权":$T[0]['nickname'];
				
				$json['status']['err']=0;
				$json['status']['msg']="执行成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{
				$json['status']['err']=3;
				$json['status']['msg']="登录异常，退出小程序后重新登录！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}

		}else if(count($T)==0){
			$data['sessionid']=$sessionkey;
			
			$data['addtime']=date('Y-m-d H:i:s');
			$data['lastaddtime']=date('Y-m-d H:i:s');
			$data['ucheck']=1;
			$data['utype']=1;
			$data['openid']=$openid;
			if($lastInsId =M('sys_userinfo')->add($data)){
				$json['uid']=$lastInsId;
				$json['sessionid']=$sessionkey;
				$json['nickname']="未授权";
				$json['cele']="0.0";
				$json['cmoney']="0.00";
				$json['headimg']="/resources/headimg.jpg";
				
				$json['status']['err']=0;
				$json['status']['msg']="执行成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{
				$json['status']['err']=4;
				$json['status']['msg']="登录异常，退出小程序后重新登录！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
			
		}else{
			$json['status']['err']=5;
			$json['status']['msg']="异常账号！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
	}
	/*************************
	*修改昵称
	*/
	public function chageNick(){
		$uid = I('post.uid','','strip_tags');
		$sessionid=I('post.sessionid','','strip_tags');
		$nickname=I('post.nickname','','strip_tags');
		$headimg=I('post.headimg','','strip_tags');
		if($uid==""||$sessionid==""||$nickname==""||$headimg==""){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		//判断用户名session是否正确
		$T=M('sys_userinfo')->where('id = '.$uid.' and ucheck=1')->select();
		if(count($T)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="您的状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($T[0]["sessionid"]!=$sessionid){
			$json['status']['err']=1;
			$json['status']['msg']="您在其他地方登录！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		
		$data['nickname']=$nickname;
		$data['headimg']=$headimg;
		$result=M('sys_userinfo')->where('id='.$T[0]['id'])->save($data);
		if($result||$result===0){
			$json['status']['err']=0;
			$json['status']['msg']="修改成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}else{
			$json['status']['err']=1;
			$json['status']['msg']="修改失败！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}		
	}
	
	/*************************
	*车位带数据详情,详情页info
	*/	
	public function Site_one(){
		$sid = I('post.sid',0,'intval');
		if($sid==0){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		$sql="select *,(select count(*) from db_pile where parentid=".$sid." and isenable=1 and isdelete=0) as Cpower,(select count(*) from db_pile where parentid=".$sid."  and isenable=1 and isdelete=0 and islink=1 and ptype=0 ) as Kpower,(select count(*) from db_pile where parentid=".$sid."  and isenable=1 and isdelete=0 and islink=1 and ptype=0 and isnone<>1) as Kcar from db_sitelist where isenable = 1 AND isdelete = 0 and id=".$sid;	
		$T=M()-> query($sql);
		
		$siteinfo['sid']=(int)$T[0]['id'];
		$siteinfo['sitename']=$T[0]['sitename'];
		$siteinfo['sitetel']=$T[0]['sitetel'];
		$siteinfo['siteimgs']=$T[0]['siteimgs'];
		$siteinfo['siteadd']=$T[0]['siteadd']==null?'':$T[0]['siteadd'];
		$siteinfo['latitude']=$T[0]['tsitex']==null?'':(float)$T[0]['tsitex'];
		$siteinfo['longitude']=$T[0]['tsitey']==null?'':(float)$T[0]['tsitey'];
		$siteinfo['uint']=$T[0]['uint']==null?'0.00':sprintf("%1.2f",(float)$T[0]['uint']/100);
		$siteinfo['Cpower']=$T[0]['Cpower'];
		$siteinfo['Kpower']=$T[0]['Kpower'];
		$siteinfo['Kcar']=$T[0]['Kcar'];
		$json['siteinfo']=$siteinfo;
		$sql="select * from db_pile where isenable=1 and isdelete=0 and parentid=".$sid." order by orderid desc";
		if($T=M()-> query($sql)){
			$data=array();
			foreach($T as $t=>$v){
				$data[$t]['id']=(int)$v['id'];
				$data[$t]['pilenum']=$v['pilenum']==null?'':$v['pilenum'];
				$data[$t]['sta']=ItoStr($v['islink'],$v['isnone'],$v['ptype']);				
			}
		}
		
	   	$json['data']=$data;
		$json['status']['err']=0;
		$json['status']['msg']="执行成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit; 
	}
	
	/*************************
	*车位状态图验证
	*/	
	public function Site_None(){
		$sid = I('post.sid',0,'intval');
		$uid = I('post.uid',0,'intval');
		$sessionid=I('post.sessionid','','strip_tags');
		if($sid==0||$uid==0||$sessionid==""){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		//判断用户名session是否正确
		$T=M('sys_userinfo')->where('id = '.$uid.' and ucheck=1')->select();
		if(count($T)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="您的状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($T[0]["sessionid"]!=$sessionid){
			$json['status']['err']=1;
			$json['status']['msg']="您在其他地方登录！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		//判断站点
		$T1=M('sitelist')->where('id = '.$sid)->find();
		if (!file_exists($_SERVER["DOCUMENT_ROOT"].$T1['sitemap'])){
			$json['status']['err']=2;
			$json['status']['msg']="图片不存在！";
			$json['sitename']=$T1['sitename'];
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		//判断设备
		$Tp=M('pile')->where('parentid = '.$sid.' and isenable=1 and isdelete=0')->select();
		if(count($Tp)==0){
			$json['status']['err']=2;
			$json['status']['msg']="没有设备！";
			$json['sitename']=$T1['sitename'];
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//合成图片
		$bgimg = new \Imagick($_SERVER["DOCUMENT_ROOT"].$T1['sitemap']);
		$image_info = getimagesize($_SERVER["DOCUMENT_ROOT"].$T1['sitemap']);
		//车位状态图
		$small1="/Web/System/Public/images/park/pche1.png";
		
		
		$wh=getimagesize($_SERVER["DOCUMENT_ROOT"].$small1);
		$w=$wh[0];
		$h=$wh[1];
		$l=sqrt($w*$w+$h*$h)/2;
		
		foreach($Tp as $t=>$v){
			if($v['islink']==0){
				$smallimg="/Web/System/Public/images/park/pche5.png";
				$small = new \Imagick($_SERVER["DOCUMENT_ROOT"].$smallimg);
				$small->rotateImage(new \ImagickPixel('none'), $v['cr']);
				$top=$l*sin(deg2rad(45+fmod(abs((float)$v['cr']),90)))-$h/2;
				$left=$l*cos(deg2rad(45-fmod(abs((float)$v['cr']),90)))-$w/2;
				$bgimg->compositeImage($small, \Imagick::COMPOSITE_OVER, $v['cx']-$left,$v['cy']-$top);
				$img[]=$small;
			}else if($v['ptype']==1){
				$smallimg="/Web/System/Public/images/park/pche2.png";
				$small = new \Imagick($_SERVER["DOCUMENT_ROOT"].$smallimg);
				$small->rotateImage(new \ImagickPixel('none'), $v['cr']);
				$top=$l*sin(deg2rad(45+fmod(abs((float)$v['cr']),90)))-$h/2;
				$left=$l*cos(deg2rad(45-fmod(abs((float)$v['cr']),90)))-$w/2;
				$bgimg->compositeImage($small, \Imagick::COMPOSITE_OVER, $v['cx']-$left,$v['cy']-$top);
				$img[]=$small;
			}else if($v['isnone']==1){
				$smallimg="/Web/System/Public/images/park/pche1.png";
				$small = new \Imagick($_SERVER["DOCUMENT_ROOT"].$smallimg);
				$small->rotateImage(new \ImagickPixel('none'), $v['cr']);
				$top=$l*sin(deg2rad(45+fmod(abs((float)$v['cr']),90)))-$h/2;
				$left=$l*cos(deg2rad(45-fmod(abs((float)$v['cr']),90)))-$w/2;
				$bgimg->compositeImage($small, \Imagick::COMPOSITE_OVER, $v['cx']-$left,$v['cy']-$top);
				$img[]=$small;
			}else if($v['isnone']==2){
				$smallimg="/Web/System/Public/images/park/pche3.png";
				$small = new \Imagick($_SERVER["DOCUMENT_ROOT"].$smallimg);
				$small->rotateImage(new \ImagickPixel('none'), $v['cr']);
				$top=$l*sin(deg2rad(45+fmod(abs((float)$v['cr']),90)))-$h/2;
				$left=$l*cos(deg2rad(45-fmod(abs((float)$v['cr']),90)))-$w/2;
				$bgimg->compositeImage($small, \Imagick::COMPOSITE_OVER, $v['cx']-$left,$v['cy']-$top);
				$img[]=$small;
			}else if($v['isnone']==3){
				$smallimg="/Web/System/Public/images/park/pche4.png";
				$small = new \Imagick($_SERVER["DOCUMENT_ROOT"].$smallimg);
				$small->rotateImage(new \ImagickPixel('none'), $v['cr']);
				$top=$l*sin(deg2rad(45+fmod(abs((float)$v['cr']),90)))-$h/2;
				$left=$l*cos(deg2rad(45-fmod(abs((float)$v['cr']),90)))-$w/2;
				$bgimg->compositeImage($small, \Imagick::COMPOSITE_OVER, $v['cx']-$left,$v['cy']-$top);
				$img[]=$small;
			}
		}
		
		$endimg = $bgimg->getImageBlob();
		foreach($img as $t=>$v){
			$v->clear();   
			$v->destroy();
		}
		$bgimg->clear();   
		$bgimg->destroy();
		$json['img'] = 'data:'.$image_info['mime'].';base64,'.str_replace("\\r\\n","",base64_encode($endimg));
		$json['sitename']=$T1['sitename'];
		$json['status']['err']=0;
		$json['status']['msg']="执行成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit; 
	}
	
	/*************************
	*订单列表
	*/
	public function getOrder(){
		$uid=I('post.uid',0,'intval');
		$sessionid=I('post.sessionid','','strip_tags');
		$maxid=I('post.maxid',0,'intval');
		$pagesize=I('post.pagesize',10,'intval');
		
		if($uid==0||$sessionid==""){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		//判断用户名session是否正确
		$T=M('sys_userinfo')->where('id = '.$uid.' and ucheck=1')->select();
		if(count($T)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="您的状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($T[0]["sessionid"]!=$sessionid){
			$json['s']=$T[0]["sessionid"];
			$json['status']['err']=1;
			$json['status']['msg']="您在其他地方登录！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//查询总条数
		if($maxid==0){
			$sql="select db_pulog.*,db_temp.No as No from db_pulog left join db_temp on db_pulog.tid=db_temp.id  where db_pulog.uid=".$uid." order by db_pulog.addtime desc limit 0,".$pagesize;
		}else{
			$sql="select db_pulog.*,db_temp.No as No from db_pulog left join db_temp on db_pulog.tid=db_temp.id  where db_pulog.uid=".$uid." and db_pulog.id<".$maxid." order by db_pulog.addtime desc limit 0,".$pagesize;
		}
		$T=M()-> query($sql);
	   	$data=array();
		foreach($T as $t=>$v){
			$data[$t]['id']=(int)$v['id'];
			$data[$t]['No']=$v['No'];
			$data[$t]['pname']=$v['pname'];
			$data[$t]['sname']=$v['sname'];
			$data[$t]['cuint']=$v['cuint']==null?'0.00':sprintf("%1.2f",(float)$v['cuint']/100);
			$data[$t]['cmoney']=$v['cmoney']==null?'0.00':sprintf("%1.2f",(float)$v['cmoney']/100);
			$data[$t]['addtime']=date("Y年m月d日",strtotime($v['addtime']));
 			$time=ItoTime($v['ctime']);
			$data[$t]['ctime']=$time['h'].':'.$time['m'].':'.$time['s'];
			$data[$t]['usmoney']=$v['usmoney']==null?'0.00':sprintf("%1.2f",(float)$v['usmoney']/100);
			$ustime=ItoTime($v['ustime']);
			$data[$t]['ustime']=$ustime['h'].'时'.$ustime['m'].'分'.$ustime['s'].'秒';
			$data[$t]['usnum']=$v['usnum'];
			$data[$t]['usele']=$v['usele']==null?'0.0':sprintf("%1.1f",(float)$v['usele']/10);
			$data[$t]['cele']=$v['cele']==null?'0.0':sprintf("%1.1f",(float)$v['cele']/10);
		}
		$json['data']=$data;
		$json['maxid']=count($T)==0?0:$T[count($T)-1]["id"];
		$json['status']['err']=0;
		$json['status']['msg']="执行成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;
	}
	
	/*************************
	*开启扫码，检测是否在充电中
	*/
	public function getPowerNum(){
		$uid = I('post.uid',0,'intval');
		$sessionid=I('post.sessionid','','strip_tags');
		if($uid==0||$sessionid==""){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		//判断用户名session是否正确
		$T=M('sys_userinfo')->where('id = '.$uid.' and ucheck=1')->select();
		if(count($T)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="您的状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($T[0]["sessionid"]!=$sessionid){
			$json['status']['err']=1;
			$json['status']['msg']="您在其他地方登录！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//获取充电记录
		$Temp=M('temp')->where('uid = '.$uid.' and isclose=0 and isenable=1')->order("id desc")->select();
		if(count($Temp)==0){
			$json['status']['err']=0;
			$json['count']=0;
			$json['status']['msg']="无充电记录，直接扫码！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		$data=array();
		foreach($Temp as $t=>$v){
			if($t<5){
				$data[$t]['id']=(int)$v['id'];
				$data[$t]['No']=$v['No'];
			}
		}
		$json['status']['err']=0;
		//$json['count']=count($Temp)>5?5:count($Temp);
		$json['data']=$data;
		$json['status']['msg']="有充电记录，弹出选项！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;
	}	
	/*************************
	*电费单价查询
	*/
	public function getUint(){
		$pid = I('post.pid',0,'intval');
		if($pid==0){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		$sql="select db_pile.pilenum,db_pile.islink,db_pile.ptype,db_sitelist.sitename,db_sitelist.uint,db_sitelist.siteimg from db_pile left join db_sitelist on db_pile.parentid=db_sitelist.id where db_pile.isenable=1 and db_pile.isdelete=0 and  db_sitelist.isenable=1 and db_sitelist.isdelete=0 and db_pile.id=".$pid;
		$T=M()-> query($sql);
		if(count($T)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="设备不存在！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}	
		if($T[0]['islink']==0){
			$json['status']['err']=1;
			$json['status']['msg']="设备不在线！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($T[0]['ptype']==1){
			$json['status']['err']=1;
			$json['status']['msg']="设备充电中！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		$json['sitename']=$T[0]['sitename'];
		$json['pname']=$T[0]['pilenum'];
		$json['uint']=$T[0]['uint']==null?'0.00':sprintf("%1.2f",(float)$T[0]['uint']/100);
		$json['siteimg']=$T[0]['siteimg'];
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;	
		
	}
	
	/*************************
	*获取充电信息-充电中
	*/
	public function getPower(){
		$oid = I('post.oid',0,'intval');
		$uid = I('post.uid',0,'intval');
		$sessionid=I('post.sessionid','','strip_tags');
		if($oid==0||$uid==0||$sessionid==""){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		//判断用户名session是否正确
		$T=M('sys_userinfo')->where('id = '.$uid.' and ucheck=1')->select();
		if(count($T)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="您的状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($T[0]["sessionid"]!=$sessionid){
			$json['status']['err']=1;
			$json['status']['msg']="您在其他地方登录！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//判断站点
		$T1=M('temp')->where('id = '.$oid.' and isclose=0')->find();
		if(!$T1){
			$json['status']['err']=1;
			$json['status']['msg']="订单信息有误";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		if($T1['uid']!=$uid ){
			$json['status']['err']=1;
			$json['status']['msg']="订单信息有误";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		
		
		//获取站点图片
		$T2=M('sitelist')->where('id = '.$T1['sid'])->find();
		if(!$T2){
			$json['status']['err']=1;
			$json['status']['msg']="站点错误";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		
		$json['sitename']=$T1['sname'];
		$json['pname']=$T1['pname'];
		$json['uint']=$T1['uint']==null?'0.00':sprintf("%1.2f",(float)$T1['uint']/100);
		$json['siteimg']=$T2['siteimg'];
		$json['smoney']=$T1['smoney']==null?'0.00':sprintf("%1.2f",(float)$T1['smoney']/100);
		$json['cele']=sprintf("%1.1f",(float)($T1['eleend']-$T1['elecount'])/10);
		$json['ctime']=time()-strtotime($T1['addtime']);
		if($T1['isstatus']==0){
			$json['color']='#0c0';	
			$json['statu']='充电中';
		}else if($T3['isstatus']==1){
			$json['color']='#c00';	
			$json['statu']='过压保护';
		}else{
			$json['color']='#c00';	
			$json['statu']='断线续充';	
		}
		
		
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;
		
	}
	/*************************
	*获取签名
	*/
	public function getSign(){

		$uid= I('post.uid',0,'intval');
		$sessionid= I('post.sessionid','','strip_tags');
		$pid = I('post.pid',0,'intval');
		$puint = I('post.puint',0,'intval');
		$pmoney=I('post.pmoney',0,'intval');
	
		if($uid==0||$sessionid==""||$pid==0||$pmoney==0||$puint ==0){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		//判断用户名session是否正确
		$U=M('sys_userinfo')->where('id = '.$uid.' and ucheck=1')->select();
		if(count($U)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="您的状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($U[0]["sessionid"]!=$sessionid){
			$json['status']['err']=1;
			$json['status']['msg']="您在其他地方登录！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//验证设备是否正常
		$P=M('pile')->where('id = '.$pid.' and isenable=1 and isdelete=0')->select();
		if(count($P)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="设备不存在！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($P[0]['ptype']==1){
			$json['status']['err']=1;
			$json['status']['msg']="设备处于充电中！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($P[0]['islink']!=1){
			$json['status']['err']=1;
			$json['status']['msg']="设备未连线#22！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//验证站点是否正常
		$S=M('sitelist')->where('id = '.$P[0]['parentid'].' and isenable=1 and isdelete=0')->select();
		if(count($S)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="站点信息有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($S[0]['uint']!=$puint){
			$json['status']['err']=1;
			$json['status']['msg']="服务端单价被修改，请重新扫码！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//验证商家
		$B=M('sys_admin')->where('id = '.$S[0]['bid'].' and adminClass=0 and working=1')->select();
		if(count($B)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="商家信息有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//订单数据
		$tit=$S[0]['sitename']."-".$P[0]['pilenum'];
		$No='RIC-'.GetRandStr(10);
		$addtime=date('Y-m-d H:i:s');
		//入库数据
		$data = array();
		$data['orderid']=$No;
		$data['uid']=$U[0]['id'];
		$data['pid']=$pid;
		$data['bid']=$B[0]['id'];
		$data['tit']=$tit;
		$data['money']=$pmoney;
		$data['cuint']=$puint;
		$data['addtime']=$addtime;
		$data['status']=0;
		$lastMoneyId =M('tempmoney')->add($data);
		if(!$lastMoneyId){
			$json['status']['err']=1;
			$json['status']['msg']="临时订单入库错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}		
		//进入充值
		$order_info = array();
        $order_info['order_info'] =$tit;
        $order_info['out_trade_no'] = $No;
		$order_info['total_fee'] = $pmoney;
		$order_info['add_time'] =$addtime;
		$order_info['mid'] =$lastMoneyId;
		$order_info['openid']= $U[0]['openid'];  
		$order_info['notify_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/API/wxpay/paynotice.php';
		require_once dirname(__FILE__).'/../../../API/wxpay/pay.php';
		$pay = new \Pay($order_info);
        $res = $pay->pay();
		if(!$res){
			$json['status']['err'] = 1;
        	$json['status']['msg'] = '获取参数错误';
			ob_clean();
			$this->ajaxReturn($json);
			exit; 
		}
		//微信返回字符串入库
		$wx['tit']=$No."（预充）";
		$wx['code']=json_encode($res);
		$wx['type']=1;
		$wx['outid']=$lastMoneyId;
		$wx['uid']=$U[0]['id'];
		$wx['addtime']=date('Y-m-d H:i:s');
		M('wxcode')->add($wx);
		//返回结果检测
		if($res['return_code']!='SUCCESS'){
			$json['status']['err'] = 1;
        	$json['status']['msg'] = $res['return_msg'];
			ob_clean();
			$this->ajaxReturn($json);
			exit;
		}	
		//返回数据
		$pdata = array();
		$pdata['timestamp'] = (string)$res['timestamp'];
		$pdata['noncestr'] = $res['nonce_str'];	
		$pdata['package'] = "prepay_id=".$res['prepay_id'];
		$pdata['paySign'] = $res['sign'];
		$pdata['id']=$lastMoneyId;
		$json['status']['err'] = 0;
		$json['status']['msg'] = '执行成功！';
		$json['data'] = $pdata;
		ob_clean();
		$this->ajaxReturn($json);
		exit;
		
	}
	/*************************
	*充值成功启动充电
	*/
	public function startCharge(){
			
		$_id= I('post.id',0,'intval');
		$uid= I('post.uid',0,'intval');		
		$sessionid= I('post.sessionid','','strip_tags');
		
		if($_id==0||$uid==0||$sessionid==""){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		//验证用户登录
		$U=M('sys_userinfo')->where('id = '.$uid.' and ucheck=1')->select();
		if(count($U)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="您的状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($U[0]["sessionid"]!=$sessionid){
			$json['status']['err']=1;
			$json['status']['msg']="您在其他地方登录！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//核对订单
		$Temp=M('tempmoney')->where('id = '.$_id.' and status=1')->select();
		if(count($Temp)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="订单状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//验证用户和订单是否对应
		if($Temp[0]['uid']!=$uid){
			$json['status']['err']=1;
			$json['status']['msg']="订单状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		//验证商家
		$B=M('sys_admin')->where('id = '.$Temp[0]['bid'].' and adminClass=0 and working=1')->select();
		if(count($B)!=1){
			Tmoney($Temp[0]);
			$json['status']['err']=1;
			$json['status']['msg']="商家信息有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		//验证设备是否正常
		$P=M('pile')->where('id = '.$Temp[0]['pid'].' and isenable=1 and isdelete=0')->select();
		if(count($P)!=1){
			Tmoney($Temp[0]);
			$json['status']['err']=1;
			$json['status']['msg']="设备不存在！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($P[0]['ptype']==1){
			Tmoney($Temp[0]);
			$json['status']['err']=1;
			$json['status']['msg']="设备处于充电中！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($P[0]['islink']!=1){
			Tmoney($Temp[0]);
			$json['status']['err']=1;
			$json['status']['msg']="设备未连线#1！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//验证站点是否正常
		$S=M('sitelist')->where('id = '.$P[0]['parentid'].' and isenable=1 and isdelete=0')->select();
		if(count($S)!=1){
			Tmoney($Temp[0]);
			$json['status']['err']=1;
			$json['status']['msg']="站点信息有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		//关闭临时订单
		$dstat['status']=2;
		//写入充电临时表
		$data['No']=$Temp[0]['orderid'];
		$data['wxno']=$Temp[0]['wxno'];
		$data['mid']=$Temp[0]['id'];
		$data['mname']=$Temp[0]['tit'];
		$data['bid']=$B[0]['id'];
		$data['bname']=$B[0]['username'];
		$data['uid']=$U[0]['id'];
		$data['uname']=$U[0]['nickname'];
		$data['pid']=$P[0]['id'];
		$data['pname']=$P[0]['pilenum'];
		$data['sid']=$S[0]['id'];
		$data['sname']=$S[0]['sitename'];
		$data['uint']=$Temp[0]['cuint'];
		$data['smoney']=$Temp[0]['money'];
		$data['tmoney']=0;
		$data['money']=$Temp[0]['money'];
		$data['addtime']=date('Y-m-d H:i:s');
		
		M()->startTrans();
		$sub=M('tempmoney')->where('id='.$Temp[0]['id'])->save($dstat);
		$lastId =M('temp')->add($data);
		
		if($sub&&$lastId){
			M()->commit();
			require_once 'Gateway.php';
			Gateway::$registerAddress = '127.0.0.1:1241';
			Gateway::sendToClient($P[0]['client_id'],'{"type":"StartChage","Orderid":'.$lastId.',"uint":'.$Temp[0]['cuint'].',"smoney":'.$Temp[0]['money'].'}');
			$start=false;
			/*******sleep***********/
			$Temp1=M('temp')->where('id = '.$lastId)->find();
			if($Temp1['isenable']==1){
				$start=true;
			}else{
				for($i=0;$i<8;$i++){
					$Temp1=M('temp')->where('id = '.$lastId)->find();
					if($Temp1['isenable']==1){
						$start=true;
						break;
					}else{
						sleep(1);
					}		
				}	
			}
			
			/*******usleep***********/
			if($start){
				$json['status']['err']=0;
				$json['status']['msg']="执行成功！";
				$json['tid']=$lastId;
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{
				Tmoneyq($Temp1);
				$json['status']['err']=1;
				$json['status']['msg']="启动充电超时！";
				$json['uu']=$i;
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
		}else{
			M()->rollback();
			Tmoney($Temp[0]);
			$json['status']['err']=1;
			$json['status']['msg']="写入数据失败#1！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
	}
	/*************************
	*停止充电
	*/
	public function stopCharge(){
		$_id= I('post.id',0,'intval');
		$uid= I('post.uid',0,'intval');		
		$sessionid= I('post.sessionid','','strip_tags');
		
		if($_id==0||$uid==0||$sessionid==""){
			$json['status']['err']=1;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		//验证用户登录
		$U=M('sys_userinfo')->where('id = '.$uid.' and ucheck=1')->select();
		if(count($U)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="您的状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($U[0]["sessionid"]!=$sessionid){
			$json['status']['err']=1;
			$json['status']['msg']="您在其他地方登录！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//核对订单
		$Temp=M('temp')->where('id = '.$_id)->select();
		if(count($Temp)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="订单状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//验证用户和订单是否对应
		if($Temp[0]['uid']!=$uid){
			$json['status']['err']=1;
			$json['status']['msg']="订单状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//验证订单状态
		if($Temp[0]['isenable']==0){
			$json['status']['err']=1;
			$json['status']['msg']="订单状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($Temp[0]['isclose']==1){
			$json['status']['err']=1;
			$json['status']['msg']="订单已经关闭！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		$P=M('pile')->where('id = '.$Temp[0]['pid'])->select();
		if(count($P)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="设备状态有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if($P[0]['islink']==0){
			$json['status']['err']=1;
			$json['status']['msg']="设备未连线，请直接按急停！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//发送命令
		require_once 'Gateway.php';
		Gateway::$registerAddress = '127.0.0.1:1237';
		Gateway::sendToClient($P[0]['client_id'],'{"type":"StopChage","code":0}');
		$start=false;
		/*******sleep***********/
		$Temp1=M('temp')->where('id = '.$_id)->find();
		if($Temp1['isclose']==1){
			$start=true;
		}else{
			for($i=0;$i<8;$i++){
				$Temp1=M('temp')->where('id = '.$_id)->find();
				if($Temp1['isclose']==1){
					$start=true;
					break;
				}else{
					sleep(1);
				}		
			}	
		}
		if($start){
			$json['status']['err']=0;

			if($Temp1['endfacode']!=0){
				$json['status']['msg']=$Temp1['endfatxt'];
			}else{
				$json['status']['msg']="执行成功！";	
			}
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}else{
			$json['status']['err']=1;
			$json['status']['msg']="停止充电超时！";
			$json['uu']=$i;
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
	}
	
	/**
	*退款
	**/
	public function tuikuan(){
		$id=I('get.id',0,'intval');
		$reto=M('temp')->where('id = '.$id)->find();
		$order_info = array();
		$order_info['out_trade_no'] = $reto['No'];
		$order_info['refund_trade_no'] ='RIC-'.GetRandStr(10);
    	$order_info['total_fee'] = $reto['smoney'];
    	$order_info['refund_fee'] =  $reto['money'];		
		require_once dirname(__FILE__).'/../../../API/wxpay/pay.php';
		$pay = new \Pay($order_info);
    	$res=$pay->refund();
		var_dump($res);
	}
	/**
	*下载版本
	*/
	public function NewBin(){
		$T1=M('down')->where("isdelete=0 and putout=1 and treeid=1")-> order('orderid desc')->find();
		if(!$T1){
			ob_clean();
			header("Content-type: text/html; charset=utf-8");
            echo "File not found2!";
			exit;
		}else{
			if (!file_exists($_SERVER["DOCUMENT_ROOT"].$T1["upfile"])){
				ob_clean();
            	header("Content-type: text/html; charset=utf-8");
           		echo "File not found3!";
           		exit; 
       		} else {
				ob_clean();
				$name_tmp = explode(".",$T1["upfile"]);
				$type=$name_tmp[count($name_tmp)-1];
				$file_Size=filesize($_SERVER["DOCUMENT_ROOT"].$T1["upfile"]);
            	Header("Content-type: application/octet-stream;charset=utf-8");
            	Header("Accept-Ranges: bytes");
           		Header("ACCEPT-LENGTH: ".$file_Size);
            	Header("Content-Disposition: attachment; filename=".$T1["newtitle"].".".$type);
				$file = fopen($_SERVER["DOCUMENT_ROOT"].$T1["upfile"],"r"); 
				echo fread($file, filesize($_SERVER["DOCUMENT_ROOT"].$T1["upfile"]));		
           		fclose($file);
			}
		}
	}
		
	/********************************************************************
	*********************************************************************
	*********************************************************************
	*/
	
}
//车位状态和颜色
function ItoStr($link,$isnone,$ptype){
	$data=array();
	if($link==0){	//空闲
		$data['txt']='断线';
		$data['col']='stared';	
	}else{
		if($ptype==1){
			$data['txt']='充电';
			$data['col']='';		
		}else{
			if($isnone==0){
				$data['txt']='空闲';
				$data['col']='stagre';	
			}else if($isnone==1){
				$data['txt']='占用';
				$data['col']='stared';
			}else if($isnone==2){
				$data['txt']='遮挡';
				$data['col']='stared';
			}else{
				$data['txt']='未知';
				$data['col']='stared';	
			}
		}
	}
	return $data;
}
//时间转换
function ItoTime($t){
	$d=array();
	$d['h']=intval($t/3600);
	$d['m']=intval(($t%3600)/60);
	$d['s']=intval(($t%3600)%60);
	return $d;
}
//获取充电时间
function getTime($sta,$addtime,$time){
	if($sta==0){
		return $time;
	}else{
		$time1=date('Y-m-d H:i:s');
		$time2=strtotime($addtime);
		return $time2-$time1;
	}
}
//订单号生成
function GetRandStr($len){ 
	$chars = array( 
        "A", "B", "C", "D", "E", "F", "G",  
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",  
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",  
        "3", "4", "5", "6", "7", "8", "9" 
    ); 
    $charsLen = count($chars) - 1; 
    shuffle($chars);   
    $output = ""; 
    for ($i=0; $i<$len; $i++){ 
        $output .= $chars[mt_rand(0, $charsLen)]; 
    }  
    return $output;  
}
//启动充电前订单未生成，验证失败全额退款
function Tmoney($t){
	if($t['status']==1){
		$order_info = array();
		$order_info['out_trade_no'] = $t['orderid'];
		$order_info['refund_trade_no'] =$t['orderid'];
    	$order_info['total_fee'] = $t['money'];
    	$order_info['refund_fee'] =  $t['money'];		
		require_once dirname(__FILE__).'/../../../API/wxpay/pay.php';
		$pay = new \Pay($order_info);
    	$res=$pay->refund();
		if($res){
			//微信返回字符串入库
			$tit=$t['tit']."-订单生成失败退款";
			$wx['tit']=$t['orderid']."（退款#2）";
			$wx['code']=json_encode($res);
			$wx['type']=2;
			$wx['uid']=$t['uid'];
			$wx['outid']=$t['id'];
			$wx['addtime']=date('Y-m-d H:i:s');
			M('wxcode')->add($wx);
			//返回结果检测
			if($res['return_code']=='SUCCESS'){
				$dstat['status']=3;
				$dstat['tit']=$tit;
				M('tempmoney')->where('id='.$t['id'])->save($dstat);
			}	
		}
	}
}

//启动充电前订单已经生成全额退款
function Tmoneyq($t){
	//确定充电未执行，订单未被修改
	if($t['isclose']==0&&$t['isenable']==0){
		//退款
		$order_info = array();
		$order_info['out_trade_no'] = $t['No'];
		$order_info['refund_trade_no'] =$t['No'];
    	$order_info['total_fee'] =$t['smoney'];
    	$order_info['refund_fee'] = $t['smoney'];
		require_once dirname(__FILE__).'/../../../API/wxpay/pay.php';
		$pay = new \Pay($order_info);
   		$res=$pay->refund();
		if($res){
			//微信记录入库
			$tit=$t['No']."-充电启动失败退款";
			$wx['tit']=$t['No']."（退款#3）";
			$wx['code']=json_encode($res);
			$wx['type']=3;
			$wx['uid']=$t['uid'];
			$wx['outid']=$t['id'];
			$wx['addtime']=date('Y-m-d H:i:s');
			M('wxcode')->add($wx);	
			//还原数据
			$data['tmoney']=$t['smoney'];
			$data['money']=0;
			$data['isclose']=1;
			$data['endcode']=50;
			$data['endtxt']="启动充电失败";
			$data['lasttime']=date('Y-m-d H:i:s');
			M('temp')->where('id='.$t['id'])->save($data);
		}
	}
}
