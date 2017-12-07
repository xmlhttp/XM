<?php
namespace System\Controller;
use Think\Controller;
class DownController extends Controller {
    public function index(){
		loadcheck(19);
    	$this->display('Index:downall');
    }
	
	//查询
	public function paged(){
		$json = array();
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);

		$count=M('Down')->where("isdelete=0")->count();
		$T=M('Down')->where("isdelete=0")-> order('orderid desc')->limit($page*$size,$size)->select();
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
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(I("post.searchid",-1)!=-1){
			$str =" and treeid = '".I("post.searchid",-1)."'";
		}
		$count=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")->count();
		$T=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit($page*$size,$size)->select();	
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
		if(!ajaxcheck(19)){
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
				$field="newtitle";
				break;
			case 2:
				break;
			case 3:
				$field="putout";
				$v=$v=="true"?1:0;
				break;
		}
		$T=M('Down');
		if($T){
			$data[$field] = $v;
			$T->where('id='.I("post.rId",0).' and isdelete=0')->save($data);  	
			login_info("【下载】 信息ID为[".I("post.rId",0). "] 更新[".$field."]成功", "Down");
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
	
	//删除
	public function del(){
		$json = array();
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$data["isdelete"]=1;
		if(M('Down')->where('id in('.I("post.ids","-1").')')->save($data)){ //删除成功后刷新数据
			$page=I("post.page",0);
			$size=I("post.size",5);
			$count=M('Down')->where("isdelete=0")->count();
			$T=M('Down')->where("isdelete=0")-> order('orderid desc')->limit($page*$size,$size)->select();	
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
					$count=M('Down')->where("isdelete=0")->count();
					$T=M('Down')->where("isdelete=0")-> order('orderid desc')->limit($page*$size,$size)->select();
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
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}		
		$data["isdelete"]=1;
		if(M('Down')->where('id in('.I("post.ids","-1").')')->save($data)){ //删除成功后刷新数据
			$page=I("post.page",0);
			$size=I("post.size",5);
			if(I("post.searchid",-1)!=-1){
				$str =" and treeid = '".I("post.searchid",-1)."'";
			}			
			$count=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")->count();
			$T=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit($page*$size,$size)->select();	
			
			
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
					$count=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")->count();
					$T=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit($page*$size,$size)->select();	
					
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
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}

		$T=M('Down')->where("id=".I("post.cid",0))->find();
		$T1=M('Down')->where("id=".I("post.pid",0))->find();	
		if(!$T||!$T1){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		$S=M('Down');
		$data["orderid"]=$T1["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('Down')->where('id ='.$T["id"])->save($data) && M('Down')->where('id ='.$T1["id"])->save($data1)){
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
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$T=M('Down')->where("id=".I("post.cid",0))->find();
		if(!$T){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('Down')->where("isdelete=0")->count();
		$T1=M('Down')->where("isdelete=0")-> order('orderid desc')->limit($page*$size-1,1)->select();
		if($count==0){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$S=M('Down');
		$data["orderid"]=$T1[0]["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('Down')->where('id ='.$T["id"])->save($data) && M('Down')->where('id ='.$T1[0]["id"])->save($data1)){
			$S->commit();
			$T2=M('Down')->where("isdelete=0")-> order('orderid desc')->limit(($page-1)*$size,$size)->select();
			$json['pagecount']=ceil($count/$size);
			$json['pagecurrent']=$page-1;
			$json['data']['rows']=showitem($T2);
			$json['status']['err']=0;
			$json['status']['msg']="请求成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
				
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
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$S=M('Down');
		$T=M('Down')->where("id=".I("post.cid",0))->find();
		if(!$T){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(I("post.searchid",-1)!=-1){
			$str =" and treeid = '".I("post.searchid",-1)."'";
		}		
		$count=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")->count();
		$T1=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit($page*$size-1,1)->select();
		if($count==0){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$data["orderid"]=$T1[0]["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('Down')->where('id ='.$T["id"])->save($data) && M('Down')->where('id ='.$T1[0]["id"])->save($data1)){
			$S->commit();
			$count=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")->count();
			$T2=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit(($page-1)*$size,$size)->select();	
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
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$S=M('Down');
		$T=M('Down')->where("id=".I("post.cid",0))->find();
		$T1=M('Down')->where("id=".I("post.pid",0))->find();
		if(!$T||!$T1){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$data["orderid"]=$T1["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('Down')->where('id ='.$T["id"])->save($data) && M('Down')->where('id ='.$T1["id"])->save($data1)){
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
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$S=M('Down');
		$T=M('Down')->where("id=".I("post.cid",0))->find();
		if(!$T){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}		
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('Down')->where("isdelete=0")->count();
		$T1=M('Down')->where("isdelete=0")-> order('orderid desc')->limit(($page+1)*$size,1)->select();
		if($count==0){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$data["orderid"]=$T1[0]["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('Down')->where('id ='.$T["id"])->save($data) && M('Down')->where('id ='.$T1[0]["id"])->save($data1)){
			$S->commit();
			$T2=M('Down')->where("isdelete=0")-> order('orderid desc')->limit(($page+1)*$size,$size)->select();
			$json['pagecount']=ceil($count/$size);
			$json['pagecurrent']=$page+1;
			$json['data']['rows']=showitem($T2);
			$json['status']['err']=0;
			$json['status']['msg']="请求成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
				
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
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$S=M('Down');
		$T=M('Down')->where("id=".I("post.cid",0))->find();
		if(!$T){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		if(I("post.searchid",-1)!=-1){
			$str =" and treeid = '".I("post.searchid",-1)."'";
		}		
		$count=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")->count();
		if($count==0){
			$json['status']['err']=2;
			$json['status']['msg']="数据错误，上移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$T1=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit(($page+1)*$size,1)->select();	
		$data["orderid"]=$T1[0]["orderid"];
		$data1["orderid"]=$T["orderid"];
		$S->startTrans();
		if(M('Down')->where('id ='.$T["id"])->save($data) && M('Down')->where('id ='.$T1[0]["id"])->save($data1)){
			$S->commit();
			$count=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")->count();
			$T2=M('Down')->where("isdelete=0".$str." and newtitle like '%".I("post.searchtxt",'')."%'")-> order('orderid desc')->limit(($page+1)*$size,$size)->select();	
			$json['pagecount']=ceil($count/$size);
			$json['pagecurrent']=$page+1;
			$json['data']['rows']=showitem($T2);;
			$json['status']['err']=0;
			$json['status']['msg']="请求成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');	
		}else{
			$S->rollback();
			$json['status']['err']=2;
			$json['status']['msg']="下移失败.";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
	}

	//下载-显示
	 public function AddRead(){
		loadcheck(19);
    	$this->display('Index:downAdd');
    }

	//下载-添加
	public function AddSave(){
		header('Content-Type:text/html;charset=utf-8 ');
		$json = array();
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;
		}
		if(I('post.newtitle', '') == ""){
			$json['status']['err']=2;
			$json['status']['msg']="标题不能为空！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;	
		}
		$data["newtitle"]=I('post.newtitle', '');
		$data["newdesc"]=I('post.newdesc', '');
		$data["addtime"]=date('Y-m-d H:i:s');
		$data["putout"]=I('post.putout', '0');
		$data["treeid"]=I('post.list1', '0');
		if($lastInsId =M('Down')->add($data)){
			$data['orderid']=$lastInsId;
			if(M('Down')->where('id='.$lastInsId)->save($data)){
				login_info("【下载】 信息ID为[".$lastInsId."]的项添加成功", "Down");
				$json['status']['err']=0;
				$json['status']['msg']="添加成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				//echo json_encode($json);
				exit;
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="写入数据库失败！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				//echo json_encode($json);
				exit;	
			}
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="写入数据库失败！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;	
		}
	}

	//站点-读取
	public function EditRead(){
		loadcheck(19);
		$info=M('Down')->where('id='.I("get.id",0).' and isdelete=0')->find();
		if(!$info){
			header("Content-Type:text/html;charset=utf-8");
			echo "信息不存在!";
			exit;	
		}
		if($info["treeid"]==1){
			$option.="<option value='1'>※有线设备</option>";
		}else{
			$option.="<option value='1'>有线设备</option>";	
		}
		if($info["treeid"]==2){
			$option.="<option value='2'>※无线设备</option>";
		}else{
			$option.="<option value='2'>无线设备</option>";	
		}
		
		if($info['upfile']!=""){
			$arr=explode(".", $info['upfile']);
			$last=$arr[count($arr)-1];
			$info['upfile']=strToimg($last);
		}
		if($info['upfile2']!=""){
			$arr=explode(".", $info['upfile2']);
			$last=$arr[count($arr)-1];
			$info['upfile2']=strToimg($last);
		}

		$this->assign('option',$option);
		$this->assign('info',$info);	
		$this->display('Index:downUpdata');
	}
	
	//站点-修改
	public function EditSave(){
		header('Content-Type:text/html;charset=utf-8 ');
		$json = array();
		if(!ajaxcheck(19)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;
		}
		if(I('get.id',0)==0){
			$json['status']['err']=2;
			$json['status']['msg']="信息提交有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;	
		}
		if(I('post.newtitle', '')==""){
			$json['status']['err']=2;
			$json['status']['msg']="广告标题不能为空！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;	
		}
		$ret=M('Down')->where('id='.I('get.id',0).' and isdelete=0')->find();
		if(!$ret){
			$json['status']['err']=2;
			$json['status']['msg']="信息提交有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;	
		}

		
		$upfile = $_FILES['upfile']['name'];
		$upfile2 = $_FILES['upfile2']['name'];
		$upload = new \Think\Upload(); // 实例化上传类
       	$upload->maxSize = 3145728; // 设置附件上传大小
        $upload->rootPath = './'; // 设置附件上传根目录
		$upload->autoSub = true;
		$upload->subName = array('date','Y-m-d');
		$upload->exts = array('bin'); // 设置附件上传类
		
		if($upfile!=""){
		    $upload->savePath = '/Web/UploadFile/Down/user1/'; // 设置附件上传（子）目录
        	$info = $upload->uploadOne($_FILES['upfile']);
			if($info) {// 上传错误提示错误信息
        		$data['upfile']=$info['savepath'].$info['savename'];
				$src=$_SERVER["DOCUMENT_ROOT"]. $ret["upfile"];
				if (file_exists($src)){
					unlink($src);
				}
    		}
		}

		if($upfile2!=""){
		    $upload->savePath = '/Web/UploadFile/Down/user2/'; // 设置附件上传（子）目录
        	$info = $upload->uploadOne($_FILES['upfile2']);
			if($info) {// 上传错误提示错误信息
        		$data['upfile2']=$info['savepath'].$info['savename'];
				$src=$_SERVER["DOCUMENT_ROOT"]. $ret["upfile2"];
				if (file_exists($src)){
					unlink($src);
				}
    		}
		}


		$data["treeid"]=I('post.list1', '0');	
		$data["newtitle"]=I('post.newtitle', '');
		$data["newdesc"]=I('post.newdesc', '');
		$data["putout"]=I('post.putout', '0');
		$result=M('Down')->where('id='.I('get.id',0).' and isdelete=0')->save($data);
		if($result||$result===0){
			login_info("【下载】 信息ID为[".I('get.id',0)."]的项修改成功", "Down");
			$json['status']['err']=0;
			$json['status']['msg']="修改成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="修改数据失败！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
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
		$data[$t]["data"][]=$v['newtitle'];
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=$v['putout'];
		$data[$t]["data"][]="编辑^/System.php?s=/System/Down/EditRead&id=".$v['id']."^_self";
		$data[$t]["data"][]=0;
	}
	return $data;
}
/*上传控件图片*/
function strToimg($last){
	if($last=="gif"||$last=="jpg"||$last=="png"||$last=="bmp"||$last==""){
		return "";	
	}else if($last=="doc"||$last=="docx"){
		return "/Web/System/Public/images/fileico/doc.jpg";
	}else if($last=="exe"){
		return "/Web/System/Public/images/fileico/exe.jpg";
	}else if($last=="mp3"){
		return "/Web/System/Public/images/fileico/mp3.jpg";
	}else if($last=="mp4"){
		return "/Web/System/Public/images/fileico/mp4.jpg";
	}else if($last=="pdf"){
		return "/Web/System/Public/images/fileico/pdf.jpg";
	}else if($last=="ppt"||$last=="pptx"||$last=="pps"){
		return "/Web/System/Public/images/fileico/ppt.jpg";
	}else if($last=="rar"){
		return "/Web/System/Public/images/fileico/rar.jpg";
	}else if($last=="txt"){
		return "/Web/System/Public/images/fileico/txt.jpg";
	}else if($last=="xls"||$last=="xlsx"){
		return "/Web/System/Public/images/fileico/xls.jpg";
	}else if($last=="zip"){
		return "/Web/System/Public/images/fileico/zip.jpg";
	}else if($last=="bin"){
		return "/Web/System/Public/images/fileico/bin.jpg";
	}else if($last=="html"||$last=="htm"){
		return "/Web/System/Public/images/fileico/html.jpg";
	}else if($last=="css"){
		return "/Web/System/Public/images/fileico/css.jpg";
	}else{
		return "/Web/System/Public/images/fileico/no.jpg";
	}
}
