<?php
namespace System\Controller;
use Think\Controller;
class WxDataListController extends Controller {

    public function index(){
		loadcheck(31); 
   		$this->display('Index:wxdatalist');
    }

	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(31)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}else{
			$count=M('wxcode')->count();
			$T=M('wxcode')-> order('id desc')->limit($page*$size,$size)->select();
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
		if(!ajaxcheck(31)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}else{
			$count=M('wxcode')->where("tit like '".I("post.searchtxt",'')."%'")->count();
			$T=M('wxcode')->where("tit like '".I("post.searchtxt",'')."%'")-> order('id desc')->limit($page*$size,$size)->select();
		}
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
	}
	//原始数据
	public function showData(){
		$json = array();
		if(I("get.id",0)==0){
			$json['status']['err']=1;
			$json['status']['msg']="数据有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(!ajaxcheck(31)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$T=M('wxcode')->where('id='.I("get.id",0))->select();
		if(count($T)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="数据不对！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$json=json_decode($T[0]['code']);
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
		$data[$t]["data"][]=$v['tit'];
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]="查看^/System.php?s=/System/WxDataList/showData&id=".$v['id']."^_blank";
		}
	return $data;
}
