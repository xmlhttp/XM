<?php
namespace System\Controller;
use Think\Controller;
class InComeController extends Controller {

    public function index(){
		loadcheck(25);
		ob_clean();
   		$this->display('Index:income');
    }

	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(25)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$count=M('sys_aslog')->where("bid=".session("uid"))->count();
			$T=M('sys_aslog')->where("bid=".session("uid"))-> order('id desc')->limit($page*$size,$size)->select();
		}else{
			$count=M('sys_aslog')->count();
			$T=M('sys_aslog')-> order('id desc')->limit($page*$size,$size)->select();
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
		if(!ajaxcheck(25)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$count=M('sys_aslog')->where("id=".I("post.searchtxt",'')." and bid=".session("uid"))->count();
			$T=M('sys_aslog')->where("id=".I("post.searchtxt",'')." and bid=".session("uid"))-> order('id desc')->limit($page*$size,$size)->select();
			
		}else{
			$count=M('sys_aslog')->where("bid=".I("post.searchtxt",''))->count();
			$T=M('sys_aslog')->where("bid=".I("post.searchtxt",''))-> order('id desc')->limit($page*$size,$size)->select();
		}
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
	}
	
}

//输出列表
function showitem($T){
	$data=array();
	
	foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		if(session("adminclass")==1||session("adminclass")==99){
			$data[$t]["data"][]=$v['bname']."^/System.php?s=/System/AdminAll/EditRead&id=".$v['bid']."^_self";
		}
		$data[$t]["data"][]=$v['type']==1?"充电":"取款";
		$data[$t]["data"][]=$v['bsnum'];
		$data[$t]["data"][]=($v['type']==1?"+":"-").sprintf("%1.2f",(float)$v['cmoney']/100);
		$data[$t]["data"][]=sprintf("%1.2f",(float)$v['bmoney']/100);
		$data[$t]["data"][]=sprintf("%1.1f",(float)$v['cele']/10);
		$data[$t]["data"][]=sprintf("%1.1f",(float)$v['bsele']/10);
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$time=ItoTime($v['ctime']);
			$stime=ItoTime($v['bstime']);
			$data[$t]["data"][]=$time['h'].":".$time['m'].":".$time['s'];	
			$data[$t]["data"][]=$stime['h'].":".$stime['m'].":".$stime['s'];	
		}
		$data[$t]["data"][]=$v['addtime'];
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