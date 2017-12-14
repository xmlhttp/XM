<?php
namespace System\Controller;
use Think\Controller;
class PersonController extends Controller {

    public function index(){
		loadcheck(21);
		$T=M('sys_admin')->where('id='.session('uid').' and isact=1')->find();
		if(!$T){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo '请求信息有误！';
		}
		$time=ItoTime($T['stime']);
		$this->assign('time',$time);
		$T['sele']=sprintf("%1.1f",(float)$T['sele']/10);
		$T['smoney']=sprintf("%1.2f",(float)$T['smoney']/100);
		$T['tmoney']=sprintf("%1.2f",(float)$T['tmoney']/100);
		$T['money']=sprintf("%1.2f",(float)$T['money']/100);
		$this->assign('person',$T);
		ob_clean();
    	$this->display('Index:person');		
    }
	//申请列表
	public function GetMoneyAll(){
		loadcheck(24);
		ob_clean();
    	$this->display('Index:getmoneylist');
	}
	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(24)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(session('adminclass')==0){
			$count=M('sys_money')->where('bid='.session('uid'))->count();
			$T=M('sys_money')->where('bid='.session('uid'))-> order('addtime desc')->limit($page*$size,$size)->select();
		}else{
			$count=M('sys_money')->count();
			$T=M('sys_money')-> order('addtime desc')->limit($page*$size,$size)->select();	
		}
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
	}
	//搜索
	public function search(){
		$json = array();
		if(!ajaxcheck(24)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		
		if(session('adminclass')==0){
			$count=M('sys_money')->where("bid=".session('uid')." and desctxt like '%".I("post.searchtxt",'')."%'")->count();
			$T=M('sys_money')->where("bid=".session('uid')." and desctxt like '%".I("post.searchtxt",'')."%'")-> order('addtime desc')->limit($page*$size,$size)->select();	
		}else{
			$count=M('sys_money')->where("desctxt like '%".I("post.searchtxt",'')."%'")->count();
			$json['tt']=I("post.searchtxt",'');
			$T=M('sys_money')->where("desctxt like '%".I("post.searchtxt",'')."%'")-> order('addtime desc')->limit($page*$size,$size)->select();	
		}
	
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);;
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
	}
	

	//信息修改
	public function PersonSave(){
		$json = array();
		if(!ajaxcheck(21)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(I('post.zhifu', '')==""){
			$json['status']['err']=2;
			$json['status']['msg']="提交参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$data['zhifu']=I('post.zhifu', '');
		$data['mark']=I('post.mark', '');
		if(M('sys_admin')->where('id="'.session('uid').'" and working=1 and isact=1')->save($data)){
				$json['status']['err']=0;
				$json['status']['msg']="修改成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
		}else{
				$json['status']['err']=2;
				$json['status']['msg']="修改数据失败1！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
		}
	}
	
	//读取申请列表
	public function AddRead(){
		loadcheck(23);
		$T=M('sys_admin')->where('id="'.session('uid').'" and working=1 and isact=1')->find();
		if(!$T){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo '请求信息有误！';
		}
		$this->assign('money',sprintf("%1.2f",(float)$T['money']/100));
		ob_clean();
    	$this->display('Index:getmoneyAdd');
	}
	//保存申请列表
	public function AddSave(){
		$json = array();
		if(!ajaxcheck(23)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==1||session("adminclass")==99){
			$json['status']['err']=2;
			$json['status']['msg']="管理员不能申请取款！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if(session("adminclass")!=0){
			$json['status']['err']=1;
			$json['status']['msg']="您的身份验证错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		$T=M('sys_admin')->where('id="'.session('uid').'" and working=1 and isact=1')->find();
		if(!$T){
			$json['status']['err']=3;
			$json['status']['msg']="数据查询有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(I('post.money', 0,'float')==0||I('post.account', '')==""){
			$json['status']['err']=2;
			$json['status']['msg']="数据参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if(I('post.money', 0)>$T["money"]){
			$json['status']['err']=5;
			$json['status']['msg']="取款金额有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$data['bid']=session("uid");
		$data['money']=I('post.money', 0,'float')*100;
		$data['Account']=I('post.account',"");
		$data['desctxt']=I('post.desc',"");
		$data['addtime']=date('Y-m-d H:i:s');
		if($lastInsId =M('sys_money')->add($data)){
			$json['status']['err']=0;
			$json['status']['msg']="添加成功！";
			login_info("【取款申请】ID为[".$lastInsId."]的取款申请提交成功", "sys_money");
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}else{
			$json['status']['err']=4;
			$json['status']['msg']="写入数据库失败！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}		
	}
	//修改申请-读取
	public function EditRead(){
		loadcheck(24);
		if(I("get.id",0)==0){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页！";
			exit;
		}
		if(session('adminclass')!=1&&session('adminclass')!=99){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页！";
			exit;
		}
		$Te=M('sys_money')->where('id='.I("get.id"),0)->select();
		if(count($Te)!=1){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页！";
			exit;
		}
		$T=M('sys_admin')->where('id="'.$Te[0]["bid"].'" and working=1 and isact=1')->find();
		if(!$T){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo '请求信息有误！';
			exit;
		}
		$T['money']=sprintf("%1.2f",(float)$T['money']/100);
		$Te[0]['money']=sprintf("%1.2f",(float)$Te[0]['money']/100);
		$this->assign('Te',$Te[0]);	
		$this->assign('T',$T);
		ob_clean();
		$this->display('Index:getmoneyUpdata');
	}
	//修改申请-保存
	public function EditSave(){
		$json = array();
		if(session('adminclass')!=1&&session('adminclass')!=99){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if(I('get.id',0)==0){
			$json['status']['err']=2;
			$json['status']['msg']="信息提交有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//操作身份验证
		$T=M('sys_admin')->where('id="'.session('uid').'" and working=1 and isact=1')->find();
		if(!$T){
			$json['status']['err']=3;
			$json['status']['msg']="数据查询有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if($T['adminClass']==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够#1！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		$Te=M('sys_money')->where('id='.I("get.id"),0)->select();
		if(count($Te)!=1){
			$json['status']['err']=3;
			$json['status']['msg']="您已经退出或权限不够#2！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if((int)(I('post.money', 0,'float')*100)!=(int)$Te[0]["money"]){
			$json['status']['err']=6;
			$json['status']['msg']="操作金额被修改！";	
			ob_clean();	
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		//被操作身份验证
		$Ts=M('sys_admin')->where('id='.$Te[0]["bid"].' and working=1 and isact=1')->find();
		if(!$Ts){
			$json['status']['err']=7;
			$json['status']['msg']="申请用户不存在！";
			ob_clean();		
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if((I('post.money', 0,'float')*100)>$Ts["money"]){
			$json['status']['err']=5;
			$json['status']['msg']="取款超额！";
			ob_clean();		
			$this->ajaxReturn($json, 'json');
			exit;
		}
		//平台信息查询
		$Tv=M('sys_site')->where('ver =0')->find();
		if(!$Tv){
			$json['status']['err']=7;
			$json['status']['msg']="平台信息查询！";
			ob_clean();		
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		
		$chagemoney=false;
		$data["desctxt"]=I('post.desctxt', '');
		if(I('post.isdone', 0)==1){
			$data["isdone"]=1;
			if($Te[0]["isdone"]==0){
				$chagemoney=true;	
			}
		}
		$data["isset"]=I('post.isset', 0);
		$picname = $_FILES['img']['name'];
		if($picname!=""){
			$upload = new \Think\Upload(); // 实例化上传类
        	$upload->maxSize = 3145728; // 设置附件上传大小
        	$upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        	$upload->rootPath = './'; // 设置附件上传根目录
			$upload->autoSub = true;
			$upload->subName = array('date','Y-m-d');
			$upload->savePath = '/Web/UploadFile/Person/'; // 设置附件上传（子）目录
        	$info = $upload->uploadOne($_FILES['img']);
			if($info) {// 上传错误提示错误信息
        		$data['prove']=$info['savepath'].$info['savename'];
				$src=$_SERVER["DOCUMENT_ROOT"]. $Te[0]["prove"];
				if (file_exists($src)){
					unlink($src);
				}
    		}
		}
		$data["mid"]=session('uid');
		$result=M('sys_money')->where('id='.I('get.id',0))->save($data);
		if($result||$result===0){
			//login_info("【申请处理】 信息ID为[".I('get.id',0)."]的项修改成功", "sys_money");
			if($chagemoney){
				//修改商家信息
				$data1["money"]=(int)$Ts["money"]-(int)$Te[0]["money"];
				$data1["tmoney"]=(int)$Ts["tmoney"]+(int)$Te[0]["money"];
				$data1["tnum"]=(int)$Ts["tnum"]+1;
				//修改平台信息
				$data2["money"]=(int)$Tv["money"]-(int)$Te[0]["money"];
				$data2["tmoney"]=(int)$Tv["tmoney"]+(int)$Te[0]["money"];
				$data2["tnum"]=(int)$$Tv["tnum"]+1;
				//添加订单
				$data3["tid"]=$Te[0]['id'];
				$data3["type"]=2;
				$data3["bid"]=$Ts['id'];
				$data3["bname"]=$Ts['username'];
				$data3["cuint"]=0;
				$data3["cmoney"]=$Te[0]["money"];
				$data3["ctime"]=0;
				$data3["cele"]=0;
				$data3["addtime"]=date('Y-m-d H:i:s');
				$data3["bsmoney"]=$Ts["smoney"];
				$data3["bstime"]=$Ts["stime"];
				$data3["bsnum"]=$Ts["snum"];
				$data3["bsele"]=$Ts["sele"];
				$data3["bmoney"]=(int)$Ts["money"]-(int)$Te[0]["money"];
				$data3["btmoney"]=(int)$Ts["tmoney"]+(int)$Te[0]["money"];
				$data3["btnum"]=(int)$Ts["tnum"]+1;
				$data3["ssmoney"]=$Tv["smoney"];
				$data3["sstime"]=$Tv["stime"];
				$data3["ssnum"]=$Tv["snum"];
				$data3["ssele"]=$Tv["sele"];
				$data3["smoney"]=(int)$Tv["money"]-(int)$Te[0]["money"];
				$data3["stmoney"]=(int)$Tv["tmoney"]+(int)$Te[0]["money"];
				$data3["stnum"]=(int)$$Tv["tnum"]+1;
				$m=M('sys_aslog');
				M('sys_admin')->where('id='.$Ts["id"])->save($data1);
				M('sys_site')->where('id='.$Tv["id"])->save($data2);
				M('sys_aslog')->add($data3);
				$m->commit();
			}
			$json['status']['err']=0;
			$json['status']['msg']="提交成功！";
			ob_clean();		
			$this->ajaxReturn($json, 'json');
			exit;
			
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="修改数据失败！#33";
			ob_clean();		
			$this->ajaxReturn($json, 'json');
			exit;
		}		
	}	
}

//状态对应图片
function carstate($n){
	switch ($n){	
		case 0:
			return C("__GRIDIMG__")."item_chk0_dis.gif";
			break;
		case 1:
			return C("__GRIDIMG__")."item_chk1_dis.gif";
			break;
	}	
}

//输出列表
function showitem($T){
	$data=array();
	foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		$data[$t]["data"][]=$v['money'];
		$data[$t]["data"][]=$v['Account'];
		$data[$t]["data"][]=$v['desctxt'];
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=carstate($v['isset']);
		$data[$t]["data"][]=carstate($v['isdone']);
		$data[$t]["data"][]="查看^".($v['prove']==""?"javascript:alert(\"没有凭证\")^_self":$v['prove']."^_blank");
		if(session('adminclass')==1||session('adminclass')==99){
			$data[$t]["data"][]="处理^/System.php?s=/System/Person/EditRead&id=".$v['id']."^_self";
		}
	}
	
	return $data;
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
//时间转换
function ItoTime($t){
	$d=array();
	$d['h']=intval($t/3600);
	$d['m']=intval(($t%3600)/60);
	$d['s']=intval(($t%3600)%60);
	return $d;
}
