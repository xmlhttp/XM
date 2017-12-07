<?php
namespace System\Controller;
use Think\Controller;
class UserAllController extends Controller {

    public function index(){
		loadcheck(11); 
   		$this->display('Index:userall');
    }
	
	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(11)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sys_userinfo')->count();
		$T=M('sys_userinfo')->order('id desc')->limit($page*$size,$size)->select();
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

		if(!ajaxcheck(11)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sys_userinfo')->where("nickname like '%".I("post.searchtxt",'')."%'")->count();
		$T=M('sys_userinfo')->where("nickname like '%".I("post.searchtxt",'')."%'")->order('id desc')->limit($page*$size,$size)->select();
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
	}
	
	//编辑
	public function edit(){
		$json = array();
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(!ajaxcheck(11)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$v=I("post.nValue","");
		switch (I("post.cInd",0)){
			case 8:
				$field="ucheck";
				$v=$v=="true"?1:0;
				break;
		}
		$T=M('sys_userinfo');
		if($T){
			$data[$field] = $v;
			$T->where('id='.I("post.rId",0))->save($data);  	
			login_info("【更新】 信息ID为[".I("post.rId",0). "] 更新成功", "sys_admin");
			$json['status']['err']=0;
			$json['status']['msg']="<span class='msgright'>ID为<font style='padding-left:2px; padding-right:2px; font-size:13px'>".I("post.rId",0)."</font>的第<font  style='padding-left:2px; padding-right:2px; font-size:13px'>".(I("post.cInd",0)+1)."</font>列的数据已经更新为:".I("post.nValue","")."</span>";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="数据连接错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		
	}
		
	//修改管理员信息-读取
	public function EditRead(){
		
		loadcheck(11);
		$userinfo=M('sys_userinfo')->where('id='.I("get.id"),0)->find();
		$this->assign('utype',utostr($userinfo['utype']));
		$this->assign('adminclass',session("adminclass"));
		$userinfo['smoney']=sprintf("%1.2f",(float)$userinfo['smoney']/100);
		$userinfo['sele']=sprintf("%1.1f",(float)$userinfo['sele']/10);
		
		$this->assign('userinfo',$userinfo);	
		$this->display('Index:userUpdata');
	}
}

//输出列表
function showitem($T){
	$data=array();
	if(session("adminclass")==1||session("adminclass")==99){
		foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		$data[$t]["data"][]=utostr($v['utype']);
		$data[$t]["data"][]=$v['nickname'];
		$data[$t]["data"][]=$v['snum'];
		$data[$t]["data"][]=sprintf("%1.2f",(float)$v['smoney']/100);
		$data[$t]["data"][]=sprintf("%1.1f",(float)$v['sele']/10);
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=$v['lastaddtime'];
		$data[$t]["data"][]=$v['ucheck'];
		$data[$t]["data"][]="详情^/System.php?s=/System/UserAll/EditRead&id=".$v['id']."^_self";
		}
	}else{
		foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		$data[$t]["data"][]=utostr($v['utype']);
		$data[$t]["data"][]=$v['nickname'];
		//$data[$t]["data"][]=$v['sunum'];
		//$data[$t]["data"][]=$v['money'];
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=$v['lastaddtime'];
		$data[$t]["data"][]=gstate($v['ucheck']);
		$data[$t]["data"][]="详情^/System.php?s=/System/UserAll/EditRead&id=".$v['id']."^_self";
		}	
	}
	return $data;
}

//用户类型
function utostr($str){
	switch($str){
		case 1:
			return "微信";
			break;
		case 2:
			return "支付宝";
			break;
		default:
			return "未知";
			break;
	}
}

//用户状态对应的图片
function gstate($n){
	switch ($n){
		case 1:
			return C("__GRIDIMG__")."item_chk1_dis.gif";
			break;
		case 0:
			return C("__GRIDIMG__")."item_chk0_dis.gif";
			break;
	}	
	
}