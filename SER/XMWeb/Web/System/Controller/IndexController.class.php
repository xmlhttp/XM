<?php
namespace System\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
		check_ip();
		$this->display();
    }	
	/*
	验证码
	*/
	public function verify(){
		$Verify = new \Think\Verify();  
   	 	$Verify->length = 4;
		ob_clean();
     	$Verify->entry();
	}
	
	/*
	验证登录
	*/
	public function Login(){
		check_ip();
		$uname=I('post.uname', ''); 
		$upwd=I('post.upwd', ''); 
		$ucode=I('post.ucode', ''); 
		$json = array();
		if(!$uname||!$upwd||!$ucode){
			login_info("信息填写不完整", "sys_admin");
			$json['status']['err']=1;
			$json['status']['msg']="信息填写不完整！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
            exit;	
		}
		if(check_code($ucode) === false){
			login_info("验证码错误", "sys_admin");
			$json['status']['err']=1;
			$json['status']['msg']="验证码错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
            exit; 
		}
	 	$User=M('sys_admin')->where('username="'.$uname.'" and working=1 and isact=1')->select();
		if (!$User) {
			login_info("不存在的用户", "sys_admin");
			$json['status']['err']=1;
			$json['status']['msg']="用户名或密码错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
            exit; 
		}
		if(count($User)!=1){
			login_info("用户信息异常", "sys_admin");
			$json['status']['err']=1;
			$json['status']['msg']="用户信息异常！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
            exit;
		}
		
		$cpwd=md5($upwd);
		if($cpwd==$User[0]['passwords']){
			$data["session"]=md5(time(). mt_rand(0,1000));
			session("uid", $User[0]["id"]);
			session("admin", $User[0]["username"]);
            session("adminclass",$User[0]["adminClass"]);
            session("parts", ','.$User[0]["parts"].',');
            session("ver", 0);
			session("sessionid",$data["session"]);
			M('sys_admin')->where('id='.$User[0]["id"])->save($data);
			login_info("登入成功", "sys_admin");
			$json['status']['err']=0;
			$json['status']['msg']="登录成功！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
            exit;
		}else{
			login_info("密码错误", "sys_admin");
			$json['status']['err']=1;
			$json['status']['msg']="用户名或密码错误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
            exit;
				
		}
		
		
	}	
}
