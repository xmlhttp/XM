<?php
namespace System\Controller;
use Think\Controller;
class ResourcesAllController extends Controller {

    public function index(){
		loadcheck(5); 
		ob_clean();
   		$this->display('Index:ResourcesAll');
    }
	
	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(5)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(session("adminclass")==1||session("adminclass")==99){
			$count=M('Resources')->table('db_Resources a,db_sitelist b,db_pile c,db_sys_userinfo d')->where(" a.uid=d.id and a.siteid=b.id and a.pid=c.id and b.isdelete=0 and c.isdelete=0 and c.parentid=b.id and a.type=1")->count();
			$T=M('Resources')->table('db_Resources a,db_sitelist b,db_pile c,db_sys_userinfo d')->where(" a.uid=d.id and a.siteid=b.id and a.pid=c.id and b.isdelete=0 and c.isdelete=0 and c.parentid=b.id and a.type=1")->field("a.id,d.uname,b.sitename,c.pilenum,a.gid,a.type,a.addtime,a.cnum")->order('a.id desc')->limit($page*$size,$size)->select();
		}else{		
			$count=M('Resources')->table('db_Resources a,db_sitelist b,db_pile c,db_sys_userinfo d')->where(" a.uid=d.id and a.siteid=b.id and a.pid=c.id and b.isdelete=0 and c.isdelete=0 and c.parentid=b.id and a.type=1  and b.userid=".session("uid"))->count();
			$T=M('Resources')->table('db_Resources a,db_sitelist b,db_pile c,db_sys_userinfo d')->where(" a.uid=d.id and a.siteid=b.id and a.pid=c.id and b.isdelete=0 and c.isdelete=0 and c.parentid=b.id and a.type=1  and b.userid=".session("uid"))->field("a.id,d.uname,b.sitename,c.pilenum,a.gid,a.type,a.addtime,a.cnum")->order('a.id desc')->limit($page*$size,$size)->select();
			
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

		if(!ajaxcheck(34)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sys_userinfo')->where("truename like '%".I("post.searchtxt",'')."%' or uname like '%".I("post.searchtxt",'')."%'")->count();
		$T=M('sys_userinfo')->where("truename like '%".I("post.searchtxt",'')."%' or uname like '%".I("post.searchtxt",'')."%'")->order('orderid desc')->limit($page*$size,$size)->select();
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
		$data[$t]["data"][]=$v['uname'];
		$data[$t]["data"][]=$v['sitename'];
		$data[$t]["data"][]=$v['pilenum']."-".$v['gid']."号枪";
		$data[$t]["data"][]=$v['type']?"充电":"充值";
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=$v['cnum'];
	}
	return $data;
}
