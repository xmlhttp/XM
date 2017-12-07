<?php
namespace System\Controller;
use Think\Controller;
class SiteListAllController extends Controller {
    public function index(){
		loadcheck(8);
    	$this->display('Index:sitelistall');
    }
	
	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$count=M('sitelist')->where("isdelete=0".$str)->count();
		$T=M('sitelist')->where("isdelete=0".$str)-> order('orderid desc')->limit($page*$size,$size)->select();
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
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$count=M('sitelist')->where("isdelete=0".$str." and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')")->count();
		$T=M('sitelist')->where("isdelete=0".$str." and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')")-> order('orderid desc')->limit($page*$size,$size)->select();	
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
		if(!ajaxcheck(8)){
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
			case 1:
				$field="sitename";
				break;
			case 2:
				$field="siteadd";
				break;
			case 3:
				break;
			case 4:
				$field="isenable";
				$v=$v=="true"?1:0;
				break;
		}
		$T=M('sitelist');
		if($T){
			$data[$field] = $v;
			if(session("adminclass")==0){
				$str=" and bid=".session("uid");
			}
			if($T->where('id='.I("post.rId",0).$str.' and isdelete=0')->save($data)){  	
				login_info("【更新】 信息ID为[".I("post.rId",0). "] 更新成功", "sitelist");
				$json['status']['err']=0;
				$json['status']['msg']="<span class='msgright'>ID为<font style='padding-left:2px; padding-right:2px; font-size:13px'>".I("post.rId",0)."</font>的第<font  style='padding-left:2px; padding-right:2px; font-size:13px'>".(I("post.cInd",0)+1)."</font>列的数据已经更新为:".I("post.nValue","")."</span>";		
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="数据修改失败！";
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
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$pcount=M('pile')->where('isdelete=0 and parentid in('.I("post.ids","-1").')')->count();
		if($pcount>0){
			$json['status']['err']=2;
			$json['status']['msg']="您选定的站点下还有设备，请清空后在删除！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$data["isdelete"]=1;
		if(M('sitelist')->where('id in('.I("post.ids","-1").')'.$str)->save($data)){ //删除成功后刷新数据
			$page=I("post.page",0);
			$size=I("post.size",5);
			
			$count=M('sitelist')->where("isdelete=0".$str)->count();
			$T=M('sitelist')->where("isdelete=0".$str)-> order('orderid desc')->limit($page*$size,$size)->select();	
			if($T){ //数据表有数据时
				$json['pagecount']=ceil($count/$size);
				$json['pagecurrent']=$page;
				$json['data']['rows']=showitem($T);;
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
					$count=M('sitelist')->where("isdelete=0".$str)->count();
					$T=M('sitelist')->where("isdelete=0".$str)-> order('orderid desc')->limit($page*$size,$size)->select();
					$json['pagecount']=ceil($count/$size);
					$json['pagecurrent']=$page;
					$json['data']['rows']=showitem($T);;
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
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$pcount=M('pile')->where('isdelete=0 and parentid in('.I("post.ids","-1").')')->count();
		if($pcount>0){
			$json['status']['err']=2;
			$json['status']['msg']="您选定的站点下还有设备，请清空后在删除！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}	
		
		$data["isdelete"]=1;
		if(M('sitelist')->where('id in('.I("post.ids","-1").')'.$str)->save($data)){ //删除成功后刷新数据

			$page=I("post.page",0);
			$size=I("post.size",5);
			$count=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)->count();
			$T=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)-> order('orderid desc')->limit($page*$size,$size)->select();	
			
			
			if($T){ //数据表有数据时
				$json['pagecount']=ceil($count/$size);
				$json['pagecurrent']=$page;
				$json['data']['rows']=showitem($T);;
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
					$count=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)->count();
					$T=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)-> order('orderid desc')->limit($page*$size,$size)->select();	
					
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
	
	//上移
	public function up(){
		$json = array();
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$T=M('sitelist')->where("id=".I("post.cid",0).$str)->find();
		$T1=M('sitelist')->where("id=".I("post.pid",0).$str)->find();		
		$S=M('sitelist');
		$data["orderid"]=$T1["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('sitelist')->where('id ='.$T["id"].$str)->save($data) && M('sitelist')->where('id ='.$T1["id"].$str)->save($data1)){
			$S->commit();
			$json['status']['err']=0;
			$json['status']['msg']="上移成功";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
				
		}else{
			$S->rollback();
			$json['status']['err']=2;
			$json['status']['msg']="执行错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
	
	}

	//普通上移上翻页
	public function pageup(){
		$json = array();
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$T=M('sitelist')->where("id=".I("post.cid",0).$str)->find();
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sitelist')->where("isdelete=0".$str)->count();
		$T1=M('sitelist')->where("isdelete=0".$str)-> order('orderid desc')->limit($page*$size-1,1)->select();
		$S=M('sitelist');
		$data["orderid"]=$T1[0]["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('sitelist')->where('id ='.$T["id"].$str)->save($data) && M('sitelist')->where('id ='.$T1[0]["id"].$str)->save($data1)){
			$S->commit();
			
			$T2=M('sitelist')->where("isdelete=0".$str)-> order('orderid desc')->limit(($page-1)*$size,$size)->select();
			$json['pagecount']=ceil($count/$size);
			$json['pagecurrent']=$page-1;
			$json['data']['rows']=showitem($T2);
			$json['status']['err']=0;
			$json['status']['msg']="请求成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}else{
			$S->rollback();
			$json['status']['err']=2;
			$json['status']['msg']="执行错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
	}
	
	//带查询的上移上翻
	public function searchup(){
		$json = array();
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		
		$S=M('sitelist');
		$T=M('sitelist')->where("id=".I("post.cid",0).$str)->find();
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)->count();
		$T1=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)-> order('orderid desc')->limit($page*$size-1,1)->select();	
		
		$data["orderid"]=$T1[0]["orderid"];
		$data1["orderid"]=$T["orderid"];
		
		$S->startTrans();
		if(M('sitelist')->where('id ='.$T["id"].$str)->save($data) && M('sitelist')->where('id ='.$T1[0]["id"].$str)->save($data1)){
			$S->commit();
			$count=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)->count();
			$T2=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)-> order('orderid desc')->limit(($page-1)*$size,$size)->select();	
			
			$json['pagecount']=ceil($count/$size);
			$json['pagecurrent']=$page-1;
			$json['data']['rows']=showitem($T2);;
			$json['status']['err']=0;
			$json['status']['msg']="请求成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');	
			exit;
		}else{
			$S->rollback();
			$json['status']['err']=2;
			$json['status']['msg']="上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
	}
	
	
	//下移
	public function down(){
		$json = array();
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$S=M('sitelist');
		$T=M('sitelist')->where("id=".I("post.cid",0).$str)->find();
		$T1=M('sitelist')->where("id=".I("post.pid",0).$str)->find();
		$data["orderid"]=$T1["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('sitelist')->where('id ='.$T["id"].$str)->save($data) && M('sitelist')->where('id ='.$T1["id"].$str)->save($data1)){
			$S->commit();
			$json['status']['err']=0;
			$json['status']['msg']="下移成功";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
				
		}else{
			$S->rollback();
			$json['status']['err']=2;
			$json['status']['msg']="执行错误，下移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
	
	}
	
	
	//普通下移下翻页
	public function pagedown(){
		$json = array();
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$S=M('sitelist');
		$T=M('sitelist')->where("id=".I("post.cid",0).$str)->find();
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sitelist')->where("isdelete=0".$str)->count();
		$T1=M('sitelist')->where("isdelete=0".$str)-> order('orderid desc')->limit(($page+1)*$size,1)->select();
		$data["orderid"]=$T1[0]["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('sitelist')->where('id ='.$T["id"].$str)->save($data) && M('sitelist')->where('id ='.$T1[0]["id"].$str)->save($data1)){
			$S->commit();
			$T2=M('sitelist')->where("isdelete=0".$str)-> order('orderid desc')->limit(($page+1)*$size,$size)->select();
			$json['pagecount']=ceil($count/$size);
			$json['pagecurrent']=$page+1;
			$json['data']['rows']=showitem($T2);
			$json['status']['err']=0;
			$json['status']['msg']="请求成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}else{
			$S->rollback();
			$json['status']['err']=2;
			$json['status']['msg']="执行错误，下移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
	}
	
	//带查询的下移下翻
	public function searchdown(){
		$json = array();
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$S=M('sitelist');
		$T=M('sitelist')->where("id=".I("post.cid",0).$str)->find();
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)->count();
		$T1=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)-> order('orderid desc')->limit(($page+1)*$size,1)->select();	
		$data["orderid"]=$T1[0]["orderid"];
		$data1["orderid"]=$T["orderid"];
		
		$S->startTrans();
		if(M('sitelist')->where('id ='.$T["id"].$str)->save($data) && M('sitelist')->where('id ='.$T1[0]["id"].$str)->save($data1)){
			$S->commit();
			$count=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)->count();
			$T2=M('sitelist')->where("isdelete=0 and(sitename like '%".I("post.searchtxt",'')."%' or siteadd like '%".I("post.searchtxt",'')."%')".$str)-> order('orderid desc')->limit(($page+1)*$size,$size)->select();	
			$json['pagecount']=ceil($count/$size);
			$json['pagecurrent']=$page+1;
			$json['data']['rows']=showitem($T2);;
			$json['status']['err']=0;
			$json['status']['msg']="请求成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}else{
			$S->rollback();
			$json['status']['err']=2;
			$json['status']['msg']="下移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
	}
	
	
	
	//站点-显示
	 public function AddRead(){
		loadcheck(8);
		if(session("adminclass")==1||session("adminclass")==99){
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!";
			exit;
		}
    	$this->display('Index:sitelistAdd');
    }

	//站点-添加
	public function AddSave(){
		$json = array();
		if(!ajaxcheck(8)){
			//filedel($_SERVER["DOCUMENT_ROOT"]."/Web/UploadFile/Site/".I('post.siteimg', ''));
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==1||session("adminclass")==99){
			$json['status']['err']=20;
			$json['status']['msg']="管理员不能添加站点！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}

		$picname = $_FILES['img']['name'];
		$picname1 = $_FILES['bigimg']['name'];
		if($picname == ""){
			$json['status']['err']=1;
			$json['status']['msg']="站点图片不能为空！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		$upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = './'; // 设置附件上传根目录
		$upload->autoSub = true;
		$upload->subName = array('date','Y-m-d');
        $upload->savePath = '/Web/UploadFile/Site/img/'; // 设置附件上传（子）目录
        $info = $upload->uploadOne($_FILES['img']);
		if(!$info) {// 上传错误提示错误信息
        	$json['status']['err']=1;
			$json['status']['msg']="上传图片失败！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
    	}else{// 上传成功 获取上传文件信息
        	$pics=$info['savepath'].$info['savename'];
    	}
		//图片2不为空
		if($picname1!= ""){
			$upload->savePath = '/Web/UploadFile/Site/map/'; // 设置附件上传（子）目录
			$info = $upload->uploadOne($_FILES['bigimg']);
			if($info) {// 上传错误提示错误信息
        		$pics1=$info['savepath'].$info['savename'];
    		}
		}
		if(I('post.sitename', '')!="" && I('post.siteadd', '')!=""&& I('post.linkpwd', '')!=""&& I('post.uint',0,'float')>0&&isset($pics)){
			$User=M('sitelist')->where('sitename="'.I('post.sitename', '').'"')->select();
			if(count($User)==0){
				$data['sitename']=I('post.sitename', '');
				$data['siteadd']=I('post.siteadd', '');
				//$data['siteinfoadd']=I('post.siteinfoadd', '');
				$data['sitetel']=I('post.sitetel', '');
				$data['siteimg']=$pics;
				if(isset($pics1)){
					$data['sitemap']=$pics1;
				}
				$data['siteimgs']=I('post.pls','|');
				//$data['sitex']=I('post.sitx',0);
				//$data['sitey']=I('post.sity',0);
				//$data['bsitex']=I('post.bsitx',0);
				//$data['bsitey']=I('post.bsity',0);
				$data['tsitex']=I('post.tsitx',0);
				$data['tsitey']=I('post.tsity',0);
				$data['linkpwd']=I('post.linkpwd','');
				$data['mark']=I('post.mark','');
				$data['isenable']=I('post.isenable',0);
				$data['uint']=(int)(I('post.uint',0,'float')*100);
				$data['addtime']=date('Y-m-d H:i:s');
				$data['bid']=session("uid");
				if($lastInsId =M('sitelist')->add($data)){
					$data['orderid']=$lastInsId;
					if(M('sitelist')->where('id='.$lastInsId)->save($data)){
						$json['status']['err']=0;
						$json['status']['msg']="添加成功！";
						ob_clean();
						$this->ajaxReturn($json, 'json');
						exit;	
					}else{
						unlink($_SERVER["DOCUMENT_ROOT"]."/Web/UploadFile/Site/".I('post.siteimg', ''));
						$json['status']['err']=2;
						$json['status']['msg']="写入数据库失败！";
						ob_clean();
						$this->ajaxReturn($json, 'json');
						exit;	
					}					
				}else{
					unlink($_SERVER["DOCUMENT_ROOT"]."/Web/UploadFile/Site/".I('post.siteimg', ''));
					$json['status']['err']=2;
					$json['status']['msg']="写入数据库失败！";
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;
				}
			}else{
				unlink($_SERVER["DOCUMENT_ROOT"]."/Web/UploadFile/Site/".I('post.siteimg', ''));
				$json['status']['err']=2;
				$json['status']['msg']="站点名称已被使用！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}
		}else{
			
			unlink($_SERVER["DOCUMENT_ROOT"]."/Web/UploadFile/Site/".I('post.siteimg', ''));
			$json['status']['err']=2;
			$json['status']['msg']="内容填写有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
	}

	//站点-读取
	public function EditRead(){
		loadcheck(8);
		$sitelist=M('sitelist')->where('id='.I("get.id",0).' and isdelete=0')->find();
		if(session("adminclass")==0&&$sitelist["bid"]!=session("uid")){
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!";
			exit;
		}
		$user=M('sys_admin')->where('id='.$sitelist["bid"])->find();
		$sql="select ifnull(SUM(money),0) as money,ifnull(SUM(sele),0) as sele from db_pile where parentid=".$sitelist["id"];
		$count=M()-> query($sql);
		$ctepm['money']=sprintf("%1.2f",(float)$count[0]['money']/100);
		$ctepm['sele']=sprintf("%1.1f",(float)$count[0]['sele']/10);
		$this->assign('user',$user);
		$this->assign('count',$ctepm);
		$sitelist['uint']=sprintf("%1.2f",(float)$sitelist['uint']/100);
		$this->assign('sitelist',$sitelist);	
		$this->display('Index:sitelistUpdata');
	}
	
	//站点-修改
	public function EditSave(){
		$json = array();
		
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$sitelist=M('sitelist')->where('id='.I('get.id',0).' and isdelete=0')->find();
		if(!$sitelist){
			$json['status']['err']=2;
			$json['status']['msg']="信息提交有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if(session("adminclass")==0&&$sitelist["bid"]!=session("uid")){
			$json['status']['err']=5;
			$json['status']['msg']="你的操作有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(session("adminclass")==0){
			$str=" and bid=".session("uid");
		}
		$picname = $_FILES['img']['name'];
		$picname1 = $_FILES['bigimg']['name'];
		$upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = './'; // 设置附件上传根目录
		$upload->autoSub = true;
		$upload->subName = array('date','Y-m-d');
		if($picname!=""){
       		$upload->savePath = '/Web/UploadFile/Site/img/'; // 设置附件上传（子）目录
        	$info = $upload->uploadOne($_FILES['img']);
			if($info) {// 上传错误提示错误信息
        		$pics=$info['savepath'].$info['savename'];
				$src=$_SERVER["DOCUMENT_ROOT"]. $sitelist["siteimg"];
				if (file_exists($src)){
					unlink($src);
				}
    		}
		}
		//图片2不为空
		if($picname1!= ""){
			$upload->savePath = '/Web/UploadFile/Site/map/'; // 设置附件上传（子）目录
			$info = $upload->uploadOne($_FILES['bigimg']);
			if($info) {// 上传错误提示错误信息
        		$pics1=$info['savepath'].$info['savename'];
				$src=$_SERVER["DOCUMENT_ROOT"]. $sitelist["sitemap"];
				if (file_exists($src)){
					unlink($src);
				}
    		}
		}

		if(I('post.sitename', '')!="" && I('post.siteadd', '')!=""&& I('post.linkpwd', '')!=""&& I('post.uint', 0,'float')>=0.01){
			
			$sitelist1=M('sitelist')->where('sitename="'.I('post.sitename', '').'" and isdelete=0 and id<>'.I('get.id',0))->select();
			if(count($sitelist1)){
				$json['status']['err']=2;
				$json['status']['msg']="站点名有重复！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}
			
			$data['sitename']=I('post.sitename', '');
			$data['siteadd']=I('post.siteadd', '');
			//$data['siteinfoadd']=I('post.siteinfoadd', '');
			$data['sitetel']=I('post.sitetel', '');
			//$data['sitex']=I('post.sitx',0);
			//$data['sitey']=I('post.sity',0);
			//$data['bsitex']=I('post.bsitx',0);
			//$data['bsitey']=I('post.bsity',0);
			$data['tsitex']=I('post.tsitx',0);
			$data['tsitey']=I('post.tsity',0);
			$data['linkpwd']=I('post.linkpwd','');
			if(isset($pics)){
				$data['siteimg']=$pics;
			}
			if(isset($pics1)){
				$data['sitemap']=$pics1;
			}
			$data['siteimgs']=I('post.pls','|');
			$data['mark']=I('post.mark','');
			$data['isenable']=I('post.isenable',0);
			$data['uint']=(int)(I('post.uint',0,'float')*100);
			$result=M('sitelist')->where('id='.I('get.id',0).' and isdelete=0'.$str)->save($data);
			if($result||$result===0){
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
			
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="内容填写有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;		
		}
		
		
		
	}
	//批量上传
	public function Swfupload(){
		If($_POST["cid"]!=8){
			print_r("/Web/System/Public/images/swfupload/error.gif");
			exit;
		}
		$upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = './'; // 设置附件上传根目录
		$upload->autoSub = true;
		$upload->subName = array('date','Y-m-d');
        $upload->savePath = '/Web/UploadFile/Site/pl/'; // 设置附件上传（子）目录
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            foreach ($info as $file) {
                print_r($file['savepath'] . $file['savename']);
            }
        }
	}
	//删除
	public function SwfDel(){
		if(!ajaxcheck(8)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(I('post.src', '')!=""){
			$src=$_SERVER["DOCUMENT_ROOT"]. $_POST['src'];
			if (file_exists($src)){
				unlink($src);
			}
			$json['status']['err']=0;
			$json['status']['msg']="删除成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}else{
			$json['status']['err']=1;
			$json['status']['msg']="参数有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
	}
	
}

//输出列表
function showitem($T){
	$data=array();
	foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		$data[$t]["data"][]=$v['sitename'];
		$data[$t]["data"][]=$v['siteadd'];
		$data[$t]["data"][]=$v['addtime'];
		//$data[$t]["data"][]=$v['islink']?C('__GRIDIMG__').'item_chk1_dis.gif':C('__GRIDIMG__').'item_chk0_dis.gif';
		$data[$t]["data"][]=$v['isenable'];
		$data[$t]["data"][]="编辑^/System.php?s=/System/SiteListAll/EditRead&id=".$v['id']."^_self";
		$data[$t]["data"][]=0;
	}
	return $data;
}
