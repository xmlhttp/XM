<?php
namespace System\Controller;
use Think\Controller;
class USouController extends Controller {

    public function index(){
		loadcheck(4); 
		ob_clean();
   		$this->display('Index:USou');
    }

	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(4)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$count=M('pulog')->where('bid='.session("uid"))->count();
			$T=M('pulog')->where('bid='.session("uid"))-> order('id desc')->limit($page*$size,$size)->select();
		}else{
			$count=M('pulog')->count();
			$T=M('pulog')-> order('id desc')->limit($page*$size,$size)->select();
		}
		
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);
		$json['status']['err']=0;
		$json['status']['tt']=$str;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
	}
	
	
	
	//搜索
	public function search(){
		$json = array();
		if(!ajaxcheck(4)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
			
		if(session("adminclass")!=1&&session("adminclass")!=99){
			if(I("post.searchtxt",'')==''){
				$count=M('pulog')->where('bid='.session("uid"))->count();
				$T=M('pulog')->where('bid='.session("uid"))-> order('id desc')->limit($page*$size,$size)->select();
			}else{
				$count=M('pulog')->where('bid='.session("uid").' and uid = '.I("post.searchtxt",''))->count();
				$T=M('pulog')->where('bid='.session("uid").' and uid = '.I("post.searchtxt",''))-> order('id desc')->limit($page*$size,$size)->select();
			}
		}else{
			if(I("post.searchtxt",'')==''){
				$count=M('pulog')->count();
				$T=M('pulog')-> order('id desc')->limit($page*$size,$size)->select();
			}else{
				$count=M('pulog')->where('uid = '.I("post.searchtxt",''))->count();
				$T=M('pulog')->where('uid = '.I("post.searchtxt",''))-> order('id desc')->limit($page*$size,$size)->select();
			}
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
	if(session("adminclass")!=1&&session("adminclass")!=99){
		foreach($T as $t=>$v){
			$time=ItoTime($v['ctime']);
			$data[$t]["id"]=$v['id'];
			$data[$t]["data"][]=$v['id'];
			$data[$t]["data"][]=$v['uname']."^/System.php?s=/System/UserAll/EditRead&id=".$v['uid']."^_self";
			$data[$t]["data"][]=sprintf("%1.2f",(float)$v['cmoney']/100);
			$data[$t]["data"][]=sprintf("%1.1f",(float)$v['cele']/10);
			$data[$t]["data"][]=$time['h']."小时".$time['m']."分".$time['s']."秒";
			$data[$t]["data"][]=$v['addtime'];
		}
	}else{
		foreach($T as $t=>$v){
			$time=ItoTime($v['ctime']);
			$stime=ItoTime($v['ustime']);
			$data[$t]["id"]=$v['id'];
			$data[$t]["data"][]=$v['id'];
			$data[$t]["data"][]=$v['uname']."^/System.php?s=/System/UserAll/EditRead&id=".$v['uid']."^_self";
			$data[$t]["data"][]=$v['usnum'];
			$data[$t]["data"][]=sprintf("%1.2f",(float)$v['cmoney']/100);
			$data[$t]["data"][]=sprintf("%1.2f",(float)$v['usmoney']/100);
			$data[$t]["data"][]=sprintf("%1.1f",(float)$v['cele']/10);
			$data[$t]["data"][]=sprintf("%1.1f",(float)$v['usele']/10);
			$data[$t]["data"][]=$time['h']."小时".$time['m']."分".$time['s']."秒";
			$data[$t]["data"][]=$stime['h']."小时".$stime['m']."分".$stime['s']."秒";
			$data[$t]["data"][]=$v['addtime'];
		}
	}
	return $data;
}

function ItoTime($t){
	$d=array();
	$d['h']=intval($t/3600);
	$d['m']=intval(($t%3600)/60);
	$d['s']=intval(($t%3600)%60);
	return $d;
}