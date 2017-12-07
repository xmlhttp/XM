<?php
namespace System\Controller;
use Think\Controller;
class PSouController extends Controller {

    public function index(){
			loadcheck(3); 
   			$this->display('Index:PSou');
    }
	
	//查询
	public function paged(){
		$json = array();

		if(!ajaxcheck(3)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('psou')->count();
		$T=M('psou')-> order('id desc')->limit($page*$size,$size)->select();
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		$this->ajaxReturn($json, 'json');
	}
	
	
	
	//搜索
	public function search(){
		$json = array();
		if(!ajaxcheck(3)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('psou')->where("uname like '%".I("post.searchtxt",'')."%' or Adesc like '%".I("post.searchtxt",'')."%'")->count();
		$T=M('psou')->where("uname like '%".I("post.searchtxt",'')."%' or Adesc like '%".I("post.searchtxt",'')."%'")-> order('id desc')->limit($page*$size,$size)->select();
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		$this->ajaxReturn($json, 'json');
	}
	
}

//输出列表
function showitem($T){
	$data=array();
	foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		$data[$t]["data"][]=$v['uname'];
		$data[$t]["data"][]=$v['Adesc'];
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=$v['cnum'];
		$data[$t]["data"][]=$v['enum'];
	}
	return $data;
}
