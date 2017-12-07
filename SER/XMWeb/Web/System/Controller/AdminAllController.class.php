<?php
namespace System\Controller;
use Think\Controller;
class AdminAllController extends Controller {

    public function index(){
		
		if(session("adminclass")==0){
			header("Content-Type:text/html;charset=utf-8");
			echo '您的权限不够.';	
			exit();
		}else{
			loadcheck(7); 
    		$this->display('Index:adminall');
		}
    }
	
	//查询
	public function paged(){
		$json = array();
		if(session("adminclass")==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		if(!ajaxcheck(7)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sys_admin')->where('adminclass<>99')->count();
		$T=M('sys_admin')->where('adminclass<>99')->order('id desc')->limit($page*$size,$size)->select();
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
		if(session("adminclass")==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(!ajaxcheck(7)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$page=I("post.page",0);
		$size=I("post.size",5);
		$count=M('sys_admin')->where("adminclass<>99 and(username like '%".I("post.searchtxt",'')."%' or name like '%".I("post.searchtxt",'')."%')")->count();
		$T=M('sys_admin')->where("adminclass<>99 and(username like '%".I("post.searchtxt",'')."%' or name like '%".I("post.searchtxt",'')."%')")-> order('id desc')->limit($page*$size,$size)->select();
		$json['pagecount']=ceil($count/$size);
		$json['pagecurrent']=$page;
		$json['data']['rows']=showitem($T);
		$json['status']['err']=0;
		$json['status']['msg']="请求成功！";
		ob_clean();
		$this->ajaxReturn($json, 'json');
	}
	
	//编辑
	public function edit(){
		$json = array();
		if(session("adminclass")==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(!ajaxcheck(7)){
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
				$field="username";
				break;
			case 2:
				$field="name";
				break;
			case 3:
				break;
			case 4:
				break;
			case 5:
				$field="working";
				$v=$v=="true"?1:0;
				break;
		}
		$T=M('sys_admin');
		if($T){
			$data[$field] = $v;
			$T->where('id='.I("post.rId",0))->save($data);  	
			login_info("【更新】 信息ID为[".I("post.rId",0). "] 更新成功", "sys_admin");
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
		if(session("adminclass")==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(!ajaxcheck(7)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$T=M('sitelist')->where('userid in('.I("post.ids","-1").') and isdelete=0')->select();

		if($T){
			$json['status']['err']=2;
			$json['status']['msg']="请先将该用户下的所有站点删除！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		if(M('sys_admin')->where('id in('.I("post.ids","-1").')')->delete()){ //删除成功后刷新数据
			$page=I("post.page",0);
			$size=I("post.size",5);
			$count=M('sys_admin')->where('adminclass<>99')->count();
			$T=M('sys_admin')->where('adminclass<>99')->order('id desc')->limit($page*$size,$size)->select();
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
					$count=M('sys_admin')->where('adminclass<>99')->count();
					$T=M('sys_admin')->where('adminclass<>99')->order('id desc')->limit($page*$size,$size)->select();
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
		if(session("adminclass")==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(!ajaxcheck(7)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		$T=M('sitelist')->where('userid in('.I("post.ids","-1").') and isdelete=0')->select();
		if($T){
			$json['status']['err']=2;
			$json['status']['msg']="请先将该用户下的所有站点删除！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		if(M('sys_admin')->where('id in('.I("post.ids","-1").')')->delete()){ //删除成功后刷新数据
			$page=I("post.page",0);
			$size=I("post.size",5);
			$count=M('sys_admin')->where("adminclass<>99 and(username like '%".I("post.searchtxt",'')."%' or name like '%".I("post.searchtxt",'')."%')")->count();
			$T=M('sys_admin')->where("adminclass<>99 and(username like '%".I("post.searchtxt",'')."%' or name like '%".I("post.searchtxt",'')."%')")-> order('id desc')->limit($page*$size,$size)->select();
			
			
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
					$count=M('sys_admin')->where('adminclass<>99 and(username like "%'.I("post.searchtxt",'').'%" or name like "%'.I("post.searchtxt",'').'%")"')->count();
					$T=M('sys_admin')->where('adminclass<>99 and(username like "%'.I("post.searchtxt",'').'%" or name like "%'.I("post.searchtxt",'').'%")"')->order('id desc')->limit($page*$size,$size)->select();
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
	
	
	//添加管理员-显示
	 public function AddRead(){
		 if(session("adminclass")==0){
			header("Content-Type:text/html;charset=utf-8");
			echo '您的权限不够.';	
			exit();
		}else{
			loadcheck(7); 
    		$this->display('Index:adminAdd');
		}
    }
	
	//显示权限列表
	public function adminMenu0(){
		if(session("adminclass")==0){
			header("Content-Type:text/html;charset=utf-8");
			echo '您的权限不够.';	
			exit();
		}else{
			loadcheck(7);
			header("Content-type:text/xml");
			echo "<?xml version='1.0' encoding='utf-8'?><tree id='0'>" .show_xml(0)."</tree>";
		}
	}
	//显示权限列表1
	public function adminMenu1(){
		if(session("adminclass")==0){
			exit();
		}
		loadcheck(7);
		header("Content-type:text/xml");
		echo "<?xml version='1.0' encoding='utf-8'?><tree id='0'>" .show_xml1(0,",".I("get.ids").",")."</tree>";
	}
	
	//添加管理员-添加
	public function AddSave(){
		$json = array();
		if(session("adminclass")==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(!ajaxcheck(7)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		if(I('post.username', '')!="" && I('post.name', '')!="" && I('post.pwd', '')!=""){
			$User=M('sys_admin')->where('username="'.I('post.username', '').'"')->select();
			if(count($User)==0){
						
				/*$upfile = $_FILES['upfile']['name'];
				$upfile2 = $_FILES['upfile2']['name'];
				$upload = new \Think\Upload(); // 实例化上传类
				$upload->maxSize = 3145728; // 设置附件上传大小
				$upload->rootPath = './'; // 设置附件上传根目录
				$upload->autoSub = true;
				$upload->subName = array('date','Y-m-d');
				$upload->exts = array('pem'); // 设置附件上传类
		
				if($upfile!=""){
					$upload->savePath = '/Web/UploadFile/Admin/'; // 设置附件上传（子）目录
					$info = $upload->uploadOne($_FILES['upfile']);
					if($info) {// 上传错误提示错误信息
						$data['certpath']=$info['savepath'].$info['savename'];
					}
				}

				if($upfile2!=""){
					$upload->savePath = '/Web/UploadFile/Admin/'; // 设置附件上传（子）目录
					$info = $upload->uploadOne($_FILES['upfile2']);
					if($info) {// 上传错误提示错误信息
						$data['keypath']=$info['savepath'].$info['savename'];
					}
				}
*/
				$data['username']=I('post.username', '');
				$data['name']=I('post.name', '');
				$data['passwords']=md5(I('post.pwd', ''));
				$data['adminClass']=I('post.adminclass',0);
				$data['working']=I('post.working',0);
				$data['addtime']=date('y-m-d h:i:s',time());
				$data['isact']=1;
				//$data['mchid']=I('post.mchid', '');
				//$data['mchkey']=I('post.mchkey', '');
				$data['mark']=I('post.mark',0);
				if(I('post.adminclass',0)==0){
					$data['parts']=I('post.hiden',0);
				}else{
					$data['parts']='';
				}
				if($lastInsId =M('sys_admin')->add($data)){
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
				$json['status']['msg']="该用户已经存在！";
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
		if(session("adminclass")==0){
			header("Content-Type:text/html;charset=utf-8");
			echo '您的权限不够.';	
			exit();
		}
		loadcheck(7);
		$sysadmin=M('sys_admin')->where('id='.I("get.id"),0)->find();
		if($sysadmin['certpath']!=""){
			$arr=explode(".", $sysadmin['certpath']);
			$last=$arr[count($arr)-1];
			$sysadmin['upfile']=strToimg($last);
		}
		if($sysadmin['keypath']!=""){
			$arr=explode(".", $sysadmin['keypath']);
			$last=$arr[count($arr)-1];
			$sysadmin['upfile2']=strToimg($last);
		}
		
		$this->assign('sysadmin',$sysadmin);
		
		
		$this->display('Index:adminUpdata');
	}
	
	//修改管理员信息-修改
	public function EditSave(){
		$json = array();
		if(session("adminclass")==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(!ajaxcheck(7)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		$ret=M('sys_admin')->where('id='.I('get.id',0))->find();
		if(!$ret){
			$json['status']['err']=2;
			$json['status']['msg']="信息提交有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			//echo json_encode($json);
			exit;	
		}
		
		
		if(I('post.name', '')!=""){
			
			/*$upfile = $_FILES['upfile']['name'];
			$upfile2 = $_FILES['upfile2']['name'];
			$upload = new \Think\Upload(); // 实例化上传类
       		$upload->maxSize = 3145728; // 设置附件上传大小
        	$upload->rootPath = './'; // 设置附件上传根目录
			$upload->autoSub = true;
			$upload->subName = array('date','Y-m-d');
			$upload->exts = array('pem'); // 设置附件上传类
		
			if($upfile!=""){
		    	$upload->savePath = '/Web/UploadFile/Admin/'; // 设置附件上传（子）目录
        		$info = $upload->uploadOne($_FILES['upfile']);
				if($info) {// 上传错误提示错误信息
        			$data['certpath']=$info['savepath'].$info['savename'];
					$src=$_SERVER["DOCUMENT_ROOT"]. $ret["certpath"];
					if (file_exists($src)){
						unlink($src);
					}
    			}
			}

			if($upfile2!=""){
				$upload->savePath = '/Web/UploadFile/Admin/'; // 设置附件上传（子）目录
        		$info = $upload->uploadOne($_FILES['upfile2']);
				if($info) {// 上传错误提示错误信息
        			$data['keypath']=$info['savepath'].$info['savename'];
					$src=$_SERVER["DOCUMENT_ROOT"].$ret["keypath"];
					if (file_exists($src)){
						unlink($src);
					}
    			}
			}
			*/
			$data['name']=I('post.name', '');
			$data['adminClass']=I('post.adminclass',0);
			$data['working']=I('post.working',0);
			//$data['parts']=I('post.hiden',''); 
			$data['mark']=I('post.mark',0);
			//$data['mchid']=I('post.mchid', '');
			//$data['mchkey']=I('post.mchkey', '');
			
			if($ret['adminClass']==0){
				$data['parts']=I('post.hiden',0);
			}else{
				$data['parts']='';
			}
			if(I('post.pwd', '')!=""){
				$data['passwords']=md5(I('post.pwd', ''));
			}

			if(M('sys_admin')->where('id='.I('get.id',0))->save($data)){
				$json['status']['err']=0;
				$json['status']['msg']="修改成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="修改数据失败！";
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
	
}

//输出列表
function showitem($T){
	$data=array();
	foreach($T as $t=>$v){
		$data[$t]["id"]=$v['id'];
		$data[$t]["data"][]=$v['id'];
		$data[$t]["data"][]=$v['username'];
		$data[$t]["data"][]=$v['name'];
		$data[$t]["data"][]=$v['adminClass']=='0'?'商家':'管理员';
		$data[$t]["data"][]=$v['addtime'];
		$data[$t]["data"][]=$v['working'];
		$data[$t]["data"][]="编辑^/System.php?s=/System/AdminAll/EditRead&id=".$v['id']."^_self";
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
	}else if($last=="pem"){
		return "/Web/System/Public/images/fileico/pem.png";
	}else{
		return "/Web/System/Public/images/fileico/no.jpg";
	}
}

