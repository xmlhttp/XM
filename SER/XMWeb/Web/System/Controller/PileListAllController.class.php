<?php
namespace System\Controller;
use Think\Controller;
class PileListAllController extends Controller {

    public function index(){
		loadcheck(9);
		$this->assign('option',sitelist_menu(0));
		ob_clean();
    	$this->display('Index:plielistall');
    }
	
	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(9)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(session("adminclass")!=99 && session("adminclass")!=1){ //商家
			$sitetab=M('sitelist')->where("isdelete=0 and bid=".session("uid"))->select();
			if(count($sitetab)>0){
				$str="";
				for($i=0;$i<count($sitetab);$i++){
					if($i==count($sitetab)-1){
						$str.=$sitetab[$i]["id"];
					}else{
						$str.=$sitetab[$i]["id"].",";	
					}	
				}
				$count=M('pile')->where("isdelete=0 and parentid in(".$str.")")->count();
				$T=M('pile')->where("isdelete=0 and parentid in(".$str.")")-> order('orderid desc')->limit($page*$size,$size)->select();
				
			}else{
				$count=0;
				$T=(object)array();
			}
		}else{ //管理
			$count=M('pile')->where("isdelete=0")->count();
			$T=M('pile')->where("isdelete=0")-> order('orderid desc')->limit($page*$size,$size)->select();
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
		if(!ajaxcheck(9)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(session("adminclass")==0){ 
		//商家开始
			$sitetab=M('sitelist')->where("isdelete=0 and bid=".session("uid"))->select();
			if(count($sitetab)>0){
				$str="";
				for($i=0;$i<count($sitetab);$i++){
					if($i==count($sitetab)-1){
						$str.=$sitetab[$i]["id"];
					}else{
						$str.=$sitetab[$i]["id"].",";	
					}	
				}
				if(I("post.searchid",0)==0){
					$count=M('pile')->where("isdelete=0 and pilenum like '%".I("post.searchtxt",'')."%' and parentid in(".$str.")")->count();
					$T=M('pile')->where("isdelete=0 and pilenum like '%".I("post.searchtxt",'')."%' and parentid in(".$str.")")-> order('orderid desc')->limit($page*$size,$size)->select();
				}else{
					$count=M('pile')->where("isdelete=0 and parentid=".I("post.searchid",0)." and pilenum like '%".I("post.searchtxt",'')."%' and parentid in(".$str.")")->count();
					$T=M('pile')->where("isdelete=0 and parentid=".I("post.searchid",0)." and pilenum like '%".I("post.searchtxt",'')."%' and parentid in(".$str.")")-> order('orderid desc')->limit($page*$size,$size)->select();
				}
				
			}else{
				$count=0;
				$T=(object)array();
			}
		//商家结束
		}else{
		/*管理开始*/
		if(I("post.searchid",0)==0){
			$count=M('pile')->where("isdelete=0 and pilenum like '%".I("post.searchtxt",'')."%'")->count();
			$T=M('pile')->where("isdelete=0 and pilenum like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit($page*$size,$size)->select();	
		}else{
			$count=M('pile')->where("isdelete=0 and parentid=".I("post.searchid",0)." and pilenum like '%".I("post.searchtxt",'')."%'")->count();
			$T=M('pile')->where("isdelete=0 and parentid=".I("post.searchid",0)." and pilenum like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit($page*$size,$size)->select();	
		}
		/*管理结束*/
		}
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);;
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
	}
	
	//编辑
	public function edit(){
		$json = array();
		if(!ajaxcheck(9)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$v=I("post.nValue","");
		switch (I("post.cInd",0)){
			case 0:
				break;
		
			case 4:
				$field="isenable";
				$v=$v=="true"?1:0;
				break;
		}
		$T=M('pile');
		if($T){
			$data[$field] = $v;
			if(session("adminclass")==0){
				$sql="select * from db_pile left join db_sitelist on db_pile.parentid=db_sitelist.id  where db_pile.id=".I("post.rId",0)." and db_pile.isdelete=0 and db_sitelist.isdelete=0 and db_sitelist.bid=".session("uid");
				$S=M()->query($sql);
				if(count($S)==1){ //数据检测正常
					$T->where('id='.I("post.rId",0).' and isdelete=0')->save($data);  
					login_info("【更新】 信息ID为[".I("post.rId",0). "] 更新成功", "pile");
					$json['status']['err']=0;
					$json['status']['msg']="<span class='msgright'>ID为<font style='padding-left:2px; padding-right:2px; font-size:13px'>".I("post.rId",0)."</font>的第<font  style='padding-left:2px; padding-right:2px; font-size:13px'>".(I("post.cInd",0)+1)."</font>列的数据已经更新为:".I("post.nValue","")."</span>";		
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;
				}else{
					$json['status']['err']=3;
					$json['status']['msg']="修改失败，权限不够";
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;
				}
			}else{
				$T->where('id='.I("post.rId",0).' and isdelete=0')->save($data);  
				login_info("【更新】 信息ID为[".I("post.rId",0). "] 更新成功", "pile");
				$json['status']['err']=0;
				$json['status']['msg']="<span class='msgright'>ID为<font style='padding-left:2px; padding-right:2px; font-size:13px'>".I("post.rId",0)."</font>的第<font  style='padding-left:2px; padding-right:2px; font-size:13px'>".(I("post.cInd",0)+1)."</font>列的数据已经更新为:".I("post.nValue","")."</span>";		
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="数据连接错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		
	}
	
	//删除
	public function del(){
		$json = array();
		if(!ajaxcheck(9)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){ 
		$sitetab=M('sitelist')->where("isdelete=0 and bid=".session("uid"))->select();
		if(count($sitetab)>0){
			$str="";
			for($i=0;$i<count($sitetab);$i++){
				if($i==count($sitetab)-1){
					$str.=$sitetab[$i]["id"];
				}else{
					$str.=$sitetab[$i]["id"].",";	
				}	
			}
			$str=" and parentid in(".$str.")";
		}else{
			$json['status']['err']=5;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		}

		$data["isdelete"]=1;
		if(M('pile')->where('id in('.I("post.ids","-1").')'.$str)->save($data)){ //删除成功后刷新数据
			$page=I("post.page",0);
			$size=I("post.size",5);
			$count=M('pile')->where("isdelete=0".$str)->count();
			$T=M('pile')->where("isdelete=0".$str)-> order('orderid desc')->limit($page*$size,$size)->select();	

			if($T){ //数据表有数据时
				$json['pagecount']=ceil($count/$size);
				$json['pagecurrent']=$page;
				$json['data']['rows']=showitem($T);
				$json['status']['err']=0;
				$json['status']['msg']="请求成功";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{ //查询结果为空自动返回上一页
				if($page==0){
					$json['pagecount']=0;
					$json['pagecurrent']=0;
					$json['data']['rows']=array();
					$json['status']['err']=0;
					$json['status']['msg']="请求成功，数据已被清空";
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;	
				}else{
					$page=I("post.page",0)-1;
					$count=M('pile')->where("isdelete=0".$str)->count();
					$T=M('pile')->where("isdelete=0".$str)-> order('orderid desc')->limit($page*$size,$size)->select();
					$json['pagecount']=ceil($count/$size);
					$json['pagecurrent']=$page;
					$json['data']['rows']=showitem($T);
					$json['status']['err']=0;
					$json['status']['msg']="请求成功，当前页面没有数据系统自动向上翻页";
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;
				}
			}	
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="命令执行错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
	}
	
	
	//带查询的删除
	public function delsearch(){
		$json = array();
		if(!ajaxcheck(9)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		if(session("adminclass")==0){ 
		$sitetab=M('sitelist')->where("isdelete=0 and bid=".session("uid"))->select();
		if(count($sitetab)>0){
			$str="";
			for($i=0;$i<count($sitetab);$i++){
				if($i==count($sitetab)-1){
					$str.=$sitetab[$i]["id"];
				}else{
					$str.=$sitetab[$i]["id"].",";	
				}	
			}
			$str=" and parentid in(".$str.")";
		}else{
			$json['status']['err']=5;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		}
		
		$data["isdelete"]=1;
		if(M('pile')->where('id in('.I("post.ids","-1").')'.$str)->save($data)){ //删除成功后刷新数据
			$page=I("post.page",0);
			$size=I("post.size",5);
			if(I("post.searchid",0)==0){
				$count=M('pile')->where("isdelete=0 and pilenum like '%".I("post.searchtxt",'')."%'".$str)->count();
				$T=M('pile')->where("isdelete=0 and pilenum like '%".I("post.searchtxt",'')."%'".$str)-> order('orderid desc')->limit($page*$size,$size)->select();	
			}else{
				$count=M('pile')->where("isdelete=0 and parentid=".I("post.searchid",0)." and pilenum like '%".I("post.searchtxt",'')."%'".$str)->count();
				$T=M('pile')->where("isdelete=0 and parentid=".I("post.searchid",0)." and pilenum like '%".I("post.searchtxt",'')."%'".$str)-> order('orderid desc')->limit($page*$size,$size)->select();
			}
			
			if($T){ //数据表有数据时
				$json['pagecount']=ceil($count/$size);
				$json['pagecurrent']=$page;
				$json['data']['rows']=showitem($T);
				$json['status']['err']=0;
				$json['status']['msg']="请求成功";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{ //查询结果为空自动返回上一页
				if($page==0){
					$json['pagecount']=0;
					$json['pagecurrent']=0;
					$json['data']['rows']=array();
					$json['status']['err']=0;
					$json['status']['msg']="请求成功，数据已被清空";
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;	
				}else{
					$page=I("post.page",0)-1;
					if(I("post.searchid",0)==0){
						$count=M('pile')->where("isdelete=0 and pilenum like '%".I("post.searchtxt",'')."%'".$str)->count();
						$T=M('pile')->where("isdelete=0 and pilenum like '%".I("post.searchtxt",'')."%'".$str)-> order('orderid desc')->limit($page*$size,$size)->select();
					}else{
						$count=M('pile')->where("isdelete=0 and parentid=".I("post.searchid",0)." and pilenum like '%".I("post.searchtxt",'')."%'".$str)->count();
						$T=M('pile')->where("isdelete=0 and parentid=".I("post.searchid",0)." and pilenum like '%".I("post.searchtxt",'')."%'".$str)-> order('orderid desc')->limit($page*$size,$size)->select();	
					}
					$json['pagecount']=ceil($count/$size);
					$json['pagecurrent']=$page;
					$json['data']['rows']=showitem($T);
					$json['status']['err']=0;
					$json['status']['msg']="请求成功，当前页面没有数据系统自动向上翻页";
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;
				}
			}	
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="命令执行错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
	}
	
	
	

	
	
	//添加桩-显示
	 public function AddRead(){
		loadcheck(9); 
		$this->assign('option',sitelist_menu(0));
		ob_clean();
    	$this->display('Index:plielistAdd');
    }

	//添加桩-添加
	public function AddSave(){
		$json = array();
		if(!ajaxcheck(9)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$site=M('sitelist')->where('id='.I('post.list1', ''))->select();
		if(count($site)!=1){
			$json['status']['err']=2;
			$json['status']['msg']="站点不存在！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if(session("adminclass")==0){ 
			if($site[0]["bid"]!=session("uid")){
				$json['status']['err']=3;
				$json['status']['msg']="站点不存在！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
		}
		
		if(I('post.pilenum', '')!=""){
			$T=M('pile')->where('pilenum="'.I('post.pilenum', '').'" and parentid='.I('post.list1', ''))->select();
			if(count($T)==0){
				$data['parentid']=I('post.list1', '');
				$data['pilenum']=I('post.pilenum', '');
				$data['isenable']=I('post.isenable',0);
				$data['addtime']=date('Y-m-d H:i:s');
				if(I('post.landx', '')!=""&&I('post.landy', '')!=""&&I('post.landr', '')!=""){
					$data['cx']=I('post.landx', '');
					$data['cy']=I('post.landy', '');
					$data['cr']=I('post.landr', '');
				}
				
				if($lastInsId =M('pile')->add($data)){
					$data['orderid']=$lastInsId;
					if(M('pile')->where('id='.$lastInsId)->save($data)){
						$json['status']['err']=0;
						$json['status']['msg']="添加成功！";
						ob_clean();
						$this->ajaxReturn($json, 'json');
						exit;	
					}else{
						$json['status']['err']=2;
						$json['status']['msg']="写入数据库失败！";
						ob_clean();
						$this->ajaxReturn($json, 'json');
						exit;	
					}
				}else{
					$json['status']['err']=2;
					$json['status']['msg']="写入数据库失败！";
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;
				}
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="站点名称已被使用！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="内容填写有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
	}
	
	//修改管理员信息-读取
	public function EditRead(){
		loadcheck(9);
		if(I("get.id",0)==0){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!";
			exit;
		}
		$Te=M('pile')->where('id='.I("get.id",0))->select();
		if(count($Te)!=1){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!";
			exit;
		}
		$site=M('sitelist')->where('id='.$Te[0]["parentid"])->select();
		if(count($site)!=1){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!";
			exit;
		}
		if(session("adminclass")==0){ 
			if($site[0]["bid"]!=session("uid")){
				ob_clean();
				header("Content-Type:text/html;charset=utf-8");
				echo "你无权访问本页!";
				exit;
			}
		}
		
		
		$this->assign('option',sitelist_menu($Te[0]["parentid"]));
		$this->assign('T',$Te[0]);	
		ob_clean();
		$this->display('Index:plielistUpdata');
	}
	
	//修改桩-修改
	public function EditSave(){	
		$json = array();
		if(!ajaxcheck(9)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$site=M('sitelist')->where('id='.I('post.list1', ''))->select();
		if(count($site)!=1){
			$json['status']['err']=1;
			$json['status']['msg']="站点有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			if($site[0]["bid"]!=session("uid")){
				$json['status']['err']=3;
				$json['status']['msg']="修改有误！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
		}
		if(I('post.pilenum', '')!=""){
			$T1=M('pile')->where('id<>'.I("get.id").' and isdelete=0 and pilenum="'.I('post.pilenum', '').'"',0)->select();
			if(count($T1)==0){
			$T=M('pile')->where('id='.I("get.id",0).' and isdelete=0')->find();
			$data['parentid']=I('post.list1', '');
			$data['pilenum']=I('post.pilenum', '');
			$data['isenable']=I('post.isenable',0);
			if(I('post.landx', '')!=""&&I('post.landy', '')!=""&&I('post.landr', '')!=""){
				$data['cx']=I('post.landx', '');
				$data['cy']=I('post.landy', '');
				$data['cr']=I('post.landr', '');
			}
			if(M('pile')->where('id='.I('get.id',0).' and isdelete=0')->save($data)){
				$json['status']['err']=0;
				$json['status']['msg']="修改成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="内容填写有误或者没有修改！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="站点名称已被使用！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}
			
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="内容填写有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;			
		}
	}
	//编辑地图
	 public function Park(){
		loadcheck(9); 
		if(I("get.sid",0)==0||I('get.x', '')==""||I('get.y', '')==""||I('get.r', '')==""){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "<script>alert('请求有误#1！');</script>";
			exit;
		}
		$sitelist=M('sitelist')->where('id='.I("get.sid",0).' and isdelete=0')->find();
		if(!$sitelist){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "<script>alert('请求有误#2！');</script>";	
			exit;
		}
		if(session("adminclass")==0){
			if($sitelist["bid"]!=session("uid")){
				ob_clean();
				header("Content-Type:text/html;charset=utf-8");
				echo "<script>alert('请求有误#3！');</script>";
				exit;
			}
		}
		
		
		$this->assign('attr',getimagesize($_SERVER["DOCUMENT_ROOT"].$sitelist["sitemap"]));
		$this->assign('url',$sitelist["sitemap"]);
		$this->assign('x',I('get.x', ''));
		$this->assign('y',I('get.y', ''));
		$this->assign('r',I('get.r', ''));
    	$this->display('Index:plielistPark');
	
			
    }
	//模拟器界面
	public function Mode(){
		loadcheck(9);
		if(I("get.id",0)==0){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!#1";
			exit;
		}
		
		if(session("adminclass")==0){
			$T=M('pile')->where('id='.I("get.id",0).' and isdelete=0')->find();
			if(!$T){
				ob_clean();
				header("Content-Type:text/html;charset=utf-8");
				echo "你无权访问本页!#2";
				exit;	
			}
			$sitelist=M('sitelist')->where('id='.$T["parentid"].' and isdelete=0')->find();
			if(!$sitelist){
				ob_clean();
				header("Content-Type:text/html;charset=utf-8");
				echo "你无权访问本页!#3";
				exit;
			}
			if($sitelist["bid"]!=session("uid")){
				ob_clean();
				header("Content-Type:text/html;charset=utf-8");
				echo "你无权访问本页!#4";
				exit;
			}
		}
		
		
		
		
		$this->assign('pid',I("get.id",0));
		$this->assign('uid',session("uid"));
		$this->assign('sessionid',session("sessionid"));
		ob_clean();
		$this->display('Index:plieMode');
	}
	//模拟器数据
	public function ModeData(){
		$json = array();
		if(!ajaxcheck(9)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！#1";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(I("get.pid",0)==0||I("get.uid",0)==0||I("get.sessionid","")==""){
			$json['status']['err']=2;
			$json['status']['msg']="提交参数不正确！#2";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$user=M('sys_admin')->where('id='.I("get.uid",0).' and working=1')->select();
		if(count($user)!=1){
			$json['status']['err']=5;
			$json['status']['msg']="用户验证失败！#3";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		if($user[0]["session"]!=I("get.sessionid","")){
			$json['status']['err']=6;
			$json['status']['msg']="登录超时验证失败！#4";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		$T1=M('pile')->where('id='.I("get.pid",0).' and isenable=1 and isdelete=0')->select();
		if(count($T1)!=1){
			$json['status']['err']=4;
			$json['status']['msg']="不存在的桩！#5";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$sitelist=M('sitelist')->where('id='.$T1[0]["parentid"].' and isdelete=0')->find();
		if(!$sitelist){
			$json['status']['err']=5;
			$json['status']['msg']="站点信息错误！#6";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			if($sitelist["bid"]!=session("uid")){
				$json['status']['err']=5;
				$json['status']['msg']="站点信息错误！#7";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
		}		
		$ver=0;
		$T2=M('down')->where("isdelete=0 and putout=1 and treeid=0")-> order('orderid desc')->find();
		if($T2){
			$ver=$T2["id"];	
		}
		$json['status']['err']=0;
		$json['status']['msg']="请求成功";
		$json["data"]["pileId"]=$T1[0]["id"];
		$json["data"]["cardName"]=$T1[0]["pilenum"];
		$json["data"]["siteId"]=$sitelist["id"];
		$json["data"]["sitePwd"]=$sitelist["linkpwd"];
		$json["data"]["cardIp"]="192.168.1.".($T1[0]["id"]%253+1);
		$json["data"]["cardMask"]="255.255.255.0";
		$json["data"]["cardGat"]="192.168.1.1";
		$maccode=strtoupper(dechex(date("YmdHis",strtotime($T1[0]["addtime"]))));
		if(strlen($maccode)<12){
			for($i=0;$i<(12-strlen($maccode));$i++){
				$maccode.="0";
			}	
		}
		$json["data"]["Mac"]=substr($maccode,0,2)."-".substr($maccode,2,2)."-".substr($maccode,4,2)."-".substr($maccode,6,2)."-".substr($maccode,8,2)."-".substr($maccode,10,2);
		$json["data"]["serIp"]="139.199.221.53";
		$json["data"]["serPort"]=8282;
		$json["data"]["serName"]="www.vmuui.com";
		$json["data"]["volUp"]=26000;
		$json["data"]["volDown"]=18000;
		$json["data"]["eleUp"]=5000;
		$json["data"]["vol"]=22000; //额定电压
		$json["data"]["ele"]=0;	 //最小电流
		$json["data"]["power"]=0; //当前电表读数
		$json["data"]["z"]=0;//充电状态
		$json["data"]["t"]=0;//桩类型
		$json["data"]["c"]=$ver;//版本code
		ob_clean();
		$this->ajaxReturn($json, 'json');
		exit;	
	}
	//生成二维码
	public function Qrcode(){
		Vendor('phpqrcode.phpqrcode');  
		$url = "https://budian.richcomm.com.cn/budian?id=".I("get.id",0,'intval'); 
        //容错级别  
        $errorCorrectionLevel = 'L';  
        //生成图片大小  
        $matrixPointSize =7;  
        //生成二维码图片  
        $object = new \QRcode();  
		ob_clean();
        //第二个参数false的意思是不生成图片文件，如果你写上‘picture.png’则会在根目录下生成一个png格式的图片文件  
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);		
	}
	//导出Excel
	public function Export(){
		if(!ajaxcheck(9)){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo '您已经退出或权限不够！';
			exit;
		}
		if(session("adminclass")!=99 && session("adminclass")!=1){ //商家
			$sitetab=M('sitelist')->where("isdelete=0 and bid=".session("uid"))->select();
			if(count($sitetab)>0){
				$str="";
				for($i=0;$i<count($sitetab);$i++){
					if($i==count($sitetab)-1){
						$str.=$sitetab[$i]["id"];
					}else{
						$str.=$sitetab[$i]["id"].",";	
					}	
				}
				$T=M('pile')->Field('id,pilenum,isenable,islink,isnone,smoney,stime,snum,sele')->where("isdelete=0 and parentid in(".$str.")")-> order('orderid desc')->select();
			}else{
				ob_clean();
				header("Content-Type:text/html;charset=utf-8");
				echo '设备为空！';
				exit;
			}
		}else{ //管理
			$T=M('pile')->Field('id,pilenum,isenable,islink,isnone,smoney,stime,snum,sele')->where("isdelete=0")-> order('orderid desc')->select();
		}

		$xlsCell = array(
			array('id','ID'),
			array('pilenum','设备名称'),
			array('isenable','是否启用'),
			array('islink','是否连线'),
			array('isnone','车位状态'),
			array('smoney','累计收入金额'),
			array('stime','累计充电时间'),
			array('snum','累计充电次数'),
			array('sele','累计充电电度')
        );
		foreach ($T as $k => $v){
			$t=ItoTime($T[$k]['stime']);
            $T[$k]['isenable']=$v['isenable']==1?'启用':'禁用';
			$T[$k]['islink']=$v['islink']==1?'连线':'断线';
			$T[$k]['smoney']=sprintf("%1.2f",(float)$v['smoney']/100)."元";
			$T[$k]['sele']=sprintf("%1.1f",(float)$v['sele']/10)."度";
			$T[$k]['isnone']=carTotxt($T[$k]['isnone']);
			$T[$k]['stime']=$t['h']."时".$t['m']."分".$t['s']."秒";
        }
		ob_clean();
		exportExcel('设备列表',$xlsCell,$T);
	}
}

//下拉菜单
function sitelist_menu($uid){
	if(session("adminclass")==0){
		$str=" and bid=".session("uid");
	}
	$T=M('sitelist')->where('isdelete=0'.$str)->order('orderid desc')->select();
	
	if($T){
		foreach($T as $t=>$v){
			if($v["id"]==$uid){
				$option.="<option value='".$v["id"]."' selected='selected'>√".$v["sitename"]."</option>";
			}else{
				$option.="<option value='".$v["id"]."'>".$v["sitename"]."</option>";
			}
		}
	}
	return $option;
	
}


//枪状态对应图片
function gstate($n){
	switch ($n){
		/*case 1:
			return C("__GRIDIMG__")."item_chkca_dis.png";
			break;
		case 2:
			return C("__GRIDIMG__")."item_chkzj_dis.png";
			break;*/
		case 1:
			return C("__GRIDIMG__")."item_chk1_dis.gif";
			break;
		case 0:
			return C("__GRIDIMG__")."item_chk0_dis.gif";
			break;
	}	
	
}

//车位状态对应图片
function carstate($n){
	switch ($n){	
		case 0:
			return C("__GRIDIMG__")."item_chk0_dis.gif";
			break;
		case 1:
			return C("__GRIDIMG__")."item_chk1_dis.gif";
			break;
		case 2:
			return C("__GRIDIMG__")."item_chkzj_dis.png";
			break;
		case 3:
			return C("__GRIDIMG__")."item_chkca_dis.png";
			break;	
	}	
	
}
//车位状态对应文字
function carTotxt($n){
	switch ($n){	
		case 0:
			return "空闲";
			break;
		case 1:
			return "占用";
			break;
		case 2:
			return "遮挡";
			break;
		case 3:
			return "未知";
			break;	
	}
}

//输出列表
function showitem($T){
	$data=array();
	foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		$data[$t]["data"][]=$v['pilenum'];
		$data[$t]["data"][]=gstate($v['ptype']);
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=$v['isenable'];
		$data[$t]["data"][]=gstate($v['islink']);
		$data[$t]["data"][]=carstate($v['isnone']);
		$data[$t]["data"][]="编辑^/System.php?s=/System/PileListAll/EditRead&id=".$v['id']."^_self";
		$data[$t]["data"][]="生成^javascript:parent.M.build(".$v['id'].")^_self";
		$data[$t]["data"][]=0;
	}
	return $data;
}
