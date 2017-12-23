<?php
namespace System\Controller;
use Think\Controller;
class OrderListController extends Controller {

    public function index(){
		loadcheck(27);
		ob_clean();
   		$this->display('Index:orderlist');
    }

	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(27)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$count=M('temp')->where("bid=".session("uid"))->count();
			$T=M('temp')->where("bid=".session("uid"))-> order('id desc')->limit($page*$size,$size)->select();
		}else{
			$count=M('temp')->count();
			$T=M('temp')-> order('id desc')->limit($page*$size,$size)->select();
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
		if(!ajaxcheck(27)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(session("adminclass")!=1&&session("adminclass")!=99){
			$count=M('temp')->where("No='".I("post.searchtxt",'')."' and bid=".session("uid"))->count();
			$T=M('temp')->where("No='".I("post.searchtxt",'')."' and bid=".session("uid"))-> order('id desc')->limit($page*$size,$size)->select();
			
		}else{
			$count=M('temp')->where("No='".I("post.searchtxt",'')."'")->count();
			$T=M('temp')->where("No='".I("post.searchtxt",'')."'")-> order('id desc')->limit($page*$size,$size)->select();
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
	public function orderinfo(){
		loadcheck(27); 
		if(I("get.id",0)==0){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!";
			exit;
		}

		$order=M('temp')->where('id='.I("get.id",0).' and bid='.session("uid"))->find();
		if(!$order){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!";
			exit;
		}
		$order['uint']=sprintf("%1.2f",(float)$order['uint']/100);
		$order['smoney']=sprintf("%1.2f",(float)$order['smoney']/100);
		$order['tmoney']=sprintf("%1.2f",(float)$order['tmoney']/100);
		$order['money']=sprintf("%1.2f",(float)$order['money']/100);
		$order['elecount']=sprintf("%1.1f",(float)$order['elecount']/10);
		$order['eleend']=sprintf("%1.1f",(float)$order['eleend']/10);
		$order['cpower']=sprintf("%1.1f",(float)$order['cpower']/10);
		$order['isstatus']=$order['isstatus']==0?'正常':'过压保护';
		$order['isenable']=$order['isenable']==0?'未修改':'已修改';
		$order['isclose']=$order['isclose']==0?'充电中':'已结束';		
		$this->assign('order',$order);
		ob_clean();
    	$this->display('Index:orderinfo');
	}
	
}

//输出列表
function showitem($T){
	$data=array();
		foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		$data[$t]["data"][]=($v['No']==null?"NULL":$v['No']);
		$data[$t]["data"][]=$v['mname'];
		$data[$t]["data"][]=sprintf("%1.2f",(float)$v['money']/100);
		$data[$t]["data"][]=($v['isclose']==0?"充电中":"已结束");
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=$v['lasttime']==null?"-":$v['lasttime'];
		$data[$t]["data"][]="详情^/System.php?s=/System/OrderList/orderinfo&id=".$v['id']."^_self";
		}
	return $data;
}
