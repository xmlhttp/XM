<?php
namespace System\Controller;
use Think\Controller;
class ManagerPageController extends Controller {

    public function index(){
		If(!session("?admin")){
			 redirect('/System.php');
		}     
		if(I('get.ver', '')!=''){
			session('ver',I('get.ver'));
		}
		$T=M('sys_site')->where('ver=0')->find();
		$this->assign('web',$T["siteWeb"]);
		$this->assign('tree',show(0));
		$this->assign('adminclass',session("adminclass"));
		ob_clean();
    	$this->display('Index:ManagerPage');
    }
	//退出
	public function loginout(){
		$ie=get_client_browser('');
		$os=getOS();
		$ip=get_client_ip();
		if(session('?admin')){
			if(session('admin')=='super admin'){
				$username = '--';
			}else{
				$username =	session('admin');
			}
		}else{
			$username = '-';	
		}
		$Note=M("sys_note");
		$data['login_name'] = $username;
   		$data['login_ip'] = $ip;
		$data['login_os'] = $os;
		$data['login_ie'] = $ie;
		$data['act'] = "登出系统";
		$data['login_tab'] = '';
   		$Note->add($data);
		session(null);
		redirect('/System.php');
	}
	
	//首页，版权
	public function BaseInfo(){
		$this->assign('name',gethostbyname($_SERVER['SERVER_NAME']));
		$ctime=ini_get('max_execution_time').'秒';
		$this->assign('ctime',$ctime);
		$this->assign('os',getOS());
		ob_clean();
		$this->display('ManagerPage:BaseInfo');
	}
	
	
	//用户协议
	public function UserAg(){
		ob_clean();
		$this->display('ManagerPage:UserAg');
	}
	
	
	//网站设置
	public function sitesetup(){
		if(session("adminclass")==0){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo "你无权访问本页!";
			exit;		
		}
		loadcheck(14); 
		$Site=M('sys_site')->where('ver="'.session("ver").'"')->find();
		$this->assign('Site',$Site);
		ob_clean();	
		$this->display('ManagerPage:sitesetup');
	}
	
	//网站设设置——信息修改
	public function sitesetup_updata(){
		$json = array();
		if(session("adminclass")==0){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		if(!ajaxcheck(14)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}

		if(I('post.sitename1', '')!=""){		
			$data['sitename']=I('post.sitename1', '');
			$data['siteWeb']=I('post.siteweb1', '');
			$data['lock_ip']=I('post.lock_ip', '');
			$data['tel']=I('post.tel', '');
			$result=M('sys_site')->where('ver=0')->save($data);
			if($result||$result===0){
				$json['status']['err']=0;
				$json['status']['msg']="更新成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="修改数据失败或数据没有修改！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}			
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="站点名称不能为空！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
			
		}
	}
	//邮箱设置
	public function MailSet(){
		loadcheck(26); 
		$Site=M('sys_site')->where('ver="'.session("ver").'"')->find();
		$this->assign('Site',$Site);
		ob_clean();
		$this->display('ManagerPage:MailSet');
	}
	//更新邮箱设置
	public function MailSet_updata(){
		$json = array();		
		if(!ajaxcheck(26)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}

		if(I('post.smtp', '')!=""||I('post.mail', '')!=""||I('post.mailpwd', '')!=""){		
			$data['smtp']=I('post.smtp', '');
			$data['mail']=I('post.mail', '');
			$data['mailpwd']=I('post.mailpwd', '');
			if(M('sys_site')->where('ver=0')->save($data)){
				login_info("【邮箱设置】 邮箱设置修改成功", "sys_site");
				$json['status']['err']=0;
				$json['status']['msg']="更新成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="修改数据失败或数据没有修改！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}			
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="信息填写有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
			
		}
	}
	//检测邮箱设置
	public function MailSet_chk(){
		$json = array();
		if(!ajaxcheck(26)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		
		if(I('post.smtp', '')!=""||I('post.mail', '')!=""||I('post.mailpwd', '')!=""||I('post.sadd', '')!=""){		
			
			Vendor('PHPMailer.PHPMailerAutoload');  
        	$mail = new \PHPMailer(); //实例化
        	$mail->IsSMTP(); // 启用SMTP
        	$mail->Host=I('post.smtp', ''); //smtp服务器的名称（这里以QQ邮箱为例）
        	$mail->SMTPAuth =TRUE; //启用smtp认证
        	$mail->Username = I('post.mail', ''); //你的邮箱名
        	$mail->Password = I('post.mailpwd', ''); //邮箱密码
        	$mail->From = I('post.mail', ''); //发件人地址（也就是你的邮箱地址）
        	$mail->FromName = '测试'; //发件人姓名
        	$mail->AddAddress(I('post.sadd', ''),"SMTP检测接收地址");
        	$mail->WordWrap = 50; //设置每行字符长度
        	$mail->IsHTML(TRUE); // 是否HTML格式邮件
       		$mail->CharSet='utf-8'; //设置邮件编码
        	$mail->Subject ="这是一封测试邮件！"; //邮件主题
        	$mail->Body = "这是一封测试邮件，能看到该邮件表示SMTP设置正确。"; //邮件内容
        	$mail->AltBody = "这是一封测试邮件，能看到该邮件表示SMTP设置正确。"; //邮件正文不支持HTML的备用显示
       		if($mail->Send()){
				$json['status']['err']=0;
				$json['status']['msg']="发送成功！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="发送失败！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}
		
		}else{
			$json['status']['err']=2;
			$json['status']['msg']="信息填写有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
			
		}
	}
	
	//修改密码
	public function ChangePwd(){
		loadcheck(17); 
		ob_clean();
		$this->display('ManagerPage:ChangePwd');
	}
	
	//保存密码
	public function ChangPwdSave(){
		$json = array();
		if(!ajaxcheck(17)){
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;
		}
		if(I('post.oldpwd', '')=="" || I('post.pwd', '')=="" || I('post.repwd', '')==""){
			
			$json['status']['err']=2;
			$json['status']['msg']="数据填写有误！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
		
		$T=M('sys_admin')->where('id='.session("uid").' and working=1')->select();
		if(count($T)==1){
			if($T[0]["passwords"]==md5(I('post.oldpwd'))){
				$data['passwords']=md5(I('post.pwd'));
				if(M('sys_admin')->where('id='.session("uid").' and working=1')->save($data)){
					$json['status']['err']=0;
					$json['status']['msg']="修改成功！";
					login_info('修改密码成功','sys_admin');
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;
				}else{
					$json['status']['err']=2;
					$json['status']['msg']="修改数据失败或数据没有修改！";
					ob_clean();
					$this->ajaxReturn($json, 'json');
					exit;
				}
				
				
			}else{
				$json['status']['err']=2;
				$json['status']['msg']="旧密码不正确！";
				ob_clean();
				$this->ajaxReturn($json, 'json');
				exit;	
			}
		}else{
			$json['status']['err']=1;
			$json['status']['msg']="您已经退出或权限不够！";
			ob_clean();
			$this->ajaxReturn($json, 'json');
			exit;	
		}
	}
	//统计信息
	 public function Count(){
		loadcheck(29);
		$T=M('sys_site')->where('ver=0')->find();
		if(!$T){
			ob_clean();
			header("Content-Type:text/html;charset=utf-8");
			echo '请求信息有误！';
		}
		$time=ItoTime($T['stime']);
		$this->assign('time',$time);
		$T['sele']=sprintf("%1.1f",(float)$T['sele']/10);
		$T['smoney']=sprintf("%1.2f",(float)$T['smoney']/100);
		$T['tmoney']=sprintf("%1.2f",(float)$T['tmoney']/100);
		$T['money']=sprintf("%1.2f",(float)$T['money']/100);
		$this->assign('site',$T);
		ob_clean();
    	$this->display('ManagerPage:Count');		
    }
	
}



