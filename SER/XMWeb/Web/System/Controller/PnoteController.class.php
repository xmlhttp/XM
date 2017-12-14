<?php
namespace System\Controller;
use Think\Controller;
class PnoteController extends Controller {

    public function index(){
		loadcheck(18); 
		ob_clean();
   		$this->display('Index:Pnote');
    }

	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(18)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		
		if(session("adminclass")!=99&&session("adminclass")!=1){
			/*$sitetab=M('sitelist')->where("isdelete=0 and bid=".session("uid"))->select();
			if(count($sitetab)>0){
				$str="";
				for($i=0;$i<count($sitetab);$i++){
					if($i==count($sitetab)-1){
						$str.=$sitetab[$i]["id"];
					}else{
						$str.=$sitetab[$i]["id"].",";	
					}	
				}
				$sql="select count(*) as num from db_pnote left join db_pile on db_pile.id=db_pnote.pid  where db_pile.parentid in(".$str.")";
				$Tc=M()->query($sql);
				$count=$Tc[0]["num"];
				$sql="select db_pnote.* from db_pnote left join db_pile on db_pile.id=db_pnote.pid  where db_pile.parentid in(".$str.") order by db_pnote.id desc LIMIT ".$page*$size.",".$size;
				$T=M()->query($sql);
			}else{
				$count=0;
				$T=array();	
			}*/
			$count=M('pnote')->where('bid='.session('uid'))->count();
			$T=M('pnote')->where('bid='.session('uid'))-> order('id desc')->limit($page*$size,$size)->select();
		}else{
			$count=M('pnote')->count();
			$T=M('pnote')-> order('id desc')->limit($page*$size,$size)->select();
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
		if(!ajaxcheck(18)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		$searchtxt=I("post.searchtxt",'');
		if(session("adminclass")!=99&&session("adminclass")!=1){
			/*$sitetab=M('sitelist')->where("isdelete=0 and bid=".session("uid"))->select();
			if(count($sitetab)==0){
				$count=0;
				$T=array();
			}else{
				$str="";
				for($i=0;$i<count($sitetab);$i++){
					if($i==count($sitetab)-1){
						$str.=$sitetab[$i]["id"];
					}else{
						$str.=$sitetab[$i]["id"].",";	
					}	
				}
				//商家查找开始	
				
				$sql="select count(*) as num from db_pnote left join db_pile on db_pile.id=db_pnote.pid  where db_pile.parentid in(".$str.") and db_pnote.pid=".$searchtxt;
				$Tc=M()->query($sql);
				$count=$Tc[0]["num"];
				$sql="select db_pnote.* from db_pnote left join db_pile on db_pile.id=db_pnote.pid  where db_pile.parentid in(".$str.") and db_pnote.pid=".$searchtxt." order by db_pnote.id desc  LIMIT ".$page*$size.",".$size;
				$T=M()->query($sql);	

				//商家查找结束
			}
			*/
			$count=M('pnote')->where("pid =".$searchtxt." and bid=".session('uid'))->count();
			$T=M('pnote')->where("pid =".$searchtxt." and bid=".session('uid'))-> order('id desc')->limit($page*$size,$size)->select();
		}else{
			$count=M('pnote')->where("pid =".$searchtxt)->count();
			$T=M('pnote')->where("pid =".$searchtxt)-> order('id desc')->limit($page*$size,$size)->select();		
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
		if(session('adminclass')==1||session('adminclass')==99){
			$data[$t]["data"][]=$v['bname'];
		}
		$data[$t]["data"][]=$v['pname'];
		$data[$t]["data"][]=sprintf("%1.1f",(float)$v['w']/10);
		$data[$t]["data"][]=sprintf("%1.2f",(float)$v['v']/100);
		$data[$t]["data"][]=sprintf("%1.3f",(float)$v['a']/1000);
		$data[$t]["data"][]=$v['mark'];
		$data[$t]["data"][]=$v['addtime'];
	}
	return $data;
}
