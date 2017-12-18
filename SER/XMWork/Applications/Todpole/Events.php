<?php
/**
 * 主逻辑
 * 主要是处理 onMessage onClose 三个方法
 * pid枪id，cid订单id,gid站点id
 *2017.04.10添加充电结束时修改对应多商家Money信息
 */

use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;
use \Workerman\Lib\Timer;
class Events{
    /**
     * 当客户端连上时触发
     * @param int $client_id
     */
    public static function onConnect($client_id){
	//check user pwd
	Timer::add(5,function($client_id){
	    $gid=Gateway::getSession($client_id);
        if($gid==null){
			Gateway::sendToClient($client_id, "\n");
			Gateway::closeClient($client_id);
	    }else if($gid['pid']==null){
			Gateway::sendToClient($client_id, "\n");
			Gateway::closeClient($client_id);
	    }else if($gid['pid']==''){
			Gateway::sendToClient($client_id, "\n");
			Gateway::closeClient($client_id);
	    }
	},array($client_id),false);

    }
    
   /**
    * 有消息时
    * @param int $client_id
    * @param string $message
    */
   public static function onMessage($client_id, $message){
        // 获取客户端请求
		//echo $message."\n";
        $message_data = json_decode($message, true);
        if(!$message_data){
			Gateway::sendToClient($client_id, "\n");
			Gateway::closeCurrentClient();
			echo date('Y-m-d H:i:s',time()).">>异常数据---断开\n".$message."\r\n";
            return ;
        }

        switch($message_data['type']){
			//登录
            case 'login':
				echo "login >>".date('Y-m-d H:i:s',time())."*****************************\n".$message."\r\n";
				//检测上传数据
				if(!isset($message_data["pname"])||!isset($message_data["sid"])||!isset($message_data["w"])||!isset($message_data["v"])||!isset($message_data["a"])||!isset($message_data["Ispower"])||!isset($message_data["t"])||!isset($message_data["c"])||!isset($message_data["Orderid"])){
					Gateway::closeCurrentClient();
					echo date('Y-m-d H:i:s',time()).">>login-参数不正确---断开\r\n";
					break;	
				}
				//验证是否登录×
				$gid=Gateway::getSession($client_id);
        		if($gid!=null){
	    			if($gid['pid']!=null){
	    			if($gid['pid']!=''){
						echo date('Y-m-d H:i:s',time()).">>login-处于登录状态，后来提交的登录请求直接忽略\r\n";
						break;
					}
					}
	    		}
				//未充电状态下检测更新程序
				if($message_data["Ispower"]==0){
					$sql="select * from db_down where treeid=".$message_data["t"]." and isdelete=0 and putout=1 order by orderid desc";
					$verret= Db::instance('db1')->query($sql);
					if($verret){
						if($verret[0]["id"]!=$message_data["c"]){ //版本不一致更新*/
							echo date('Y-m-d H:i:s',time()).">>版本不匹配，服务端版本为：".$verret[0]["id"]."\n";
							Gateway::sendToClient($client_id,'{"type":"UpdataVer"}');
							break;
						}	
					}
				}
				//站点ID不为数字
				if(!is_numeric($message_data["sid"])||!is_numeric($message_data["Orderid"])){
					Gateway::closeCurrentClient();
					echo date('Y-m-d H:i:s',time()).">>login-站点ID/订单ID不为数字---断开\n";
					break;
				}
				//查询提交站点信息
				$ret = Db::instance('db1')->select('*')->from('db_sitelist')->where('id='.$message_data["sid"].' and isdelete=0 and isenable=1')->query();
				//站点不存在
				if(count($ret)!=1){
					Gateway::closeCurrentClient();
					echo date('Y-m-d H:i:s',time()).">>login-不存在的站点，提交sid:".$message_data["sid"]."---断开\r\n";
					break;
				}
				//密码不正确
				if(md5($ret[0]["linkpwd"])!=md5($message_data["pwd"])){
					Gateway::closeCurrentClient();
					echo date('Y-m-d H:i:s',time()).">>login-密码不正确，提交cid:".$message_data["cid"]."---断开\r\n";
					break;
				}
				//查询提交桩信息
			   	$ret1 = Db::instance('db1')->select('*')->from('db_pile')->where("parentid=".$message_data["sid"]." and pilenum = '".$message_data["pname"]."' and isdelete=0 and isenable=1")->query();	
				//桩个数数据有误，0个或多个一样的桩名
			    if(count($ret1)!=1){ 
					Gateway::closeCurrentClient();
					echo date('Y-m-d H:i:s',time()).">>login-桩信息有误，多个桩或者桩不存在，提交cid:".$message_data["cid"]."---断开\r\n";
					break;
			    } 
				//数据库中桩的连接还处于连接状态,关闭之前连接，由于这个连接在30秒之内会断掉，导致sesion失效
				//如果设备名称相同会处于抢线状态都无法使用
				if($ret1[0]["islink"]==1){
					if(Gateway::isOnline($ret1[0]["client_id"])&&$ret1[0]["client_id"]!=$client_id){
						$gid2=Gateway::getSession($ret1[0]["client_id"]);
						if($gid2['pid']==$ret1[0]["id"]){
							Gateway::closeClient($ret1[0]["client_id"]);
							echo date('Y-m-d H:i:s',time()).">>login-桩信息再次提交，原来桩下线，桩ID:".$ret1[0]["id"]."---断开\r\n";
						}
					}
				}
				
				if($message_data["Orderid"]!=0){
					$reto = Db::instance('db1')->select('*')->from('db_temp')->where("id=".$message_data["Orderid"]."")->query();	
					//桩结算，服务端有订单未结算
					if($message_data["Ispower"]==0&&$message_data["Isend"]==0&&$reto[0]['isclose']==0&&$reto[0]['isenable']==1){
						$sql="update db_temp set eleend =".$message_data["w"].",cpower=".$message_data["Cpower"].",lasttime='".date('Y-m-d H:i:s',time())."',endcode=25,endtxt='".stoptxt(25)."' where id=".$reto[0]["id"];
					
						Db::instance('db1')->query($sql);
						EndOrder($reto[0]["id"]);
						echo "触发结算\n";
					}
					//两边订单不同步
					if($message_data["Ispower"]==1&&$reto[0]['isclose']==1){
						Gateway::sendToClient($client_id, '{"type":"StopChage","code":30}');
						$message_data["Ispower"]=0;
					}
					//都在充电更新订单
					if($message_data["Ispower"]==1&&$message_data["Isend"]==0&&$reto[0]['isclose']==0&&$reto[0]['isenable']==1){
						$sql="update db_temp set eleend =".$message_data["w"].",cpower=".$message_data["Cpower"].",lasttime='".date('Y-m-d H:i:s',time())."' where id=".$reto[0]["id"];
						Db::instance('db1')->query($sql);
					}
				}

				//更新桩的连接状态，设置session
				$sql="update db_pile set islink =1,ptype=".$message_data["Ispower"].",client_id='".$client_id."' where id=".$ret1[0]["id"];
				Db::instance('db1')->query($sql);
				Gateway::setSession($client_id, array('pid'=>$ret1[0]["id"],'oid'=>$message_data["Orderid"],'sid'=>$message_data["sid"]));
				echo date('Y-m-d H:i:s',time()).">>login-更新桩状态，云Session成功，提交sid:".$message_data["sid"]."，桩号：".$ret1[0]["id"]."\n";
				//记录电表改变和操作日志#1
				if($message_data["Orderid"]!=0){
					$sql="insert into db_pnote(uid,uname,bid,bname,pid,pname,addtime,w,v,a,mark) values(".$reto[0]["uid"].",'".$reto[0]["uname"]."',".$reto[0]["bid"].",'".$reto[0]["bname"]."',".$reto[0]["pid"].",'".$reto[0]["pname"]."','".date('y-m-d h:i:s',time())."',".$message_data["w"].",".$message_data["v"].",".$message_data["a"].",'登录-记录电表数据')";
				}else{
					$retb = Db::instance('db1')->select('*')->from('db_sys_admin')->where("id=".$ret[0]['bid'])->query();
					$sql="insert into db_pnote(uid,uname,bid,bname,pid,pname,addtime,w,v,a,mark) values(0,'-',".$retb[0]['id'].",'".$retb[0]['username']."',".$ret1[0]["id"].",'".$ret1[0]["pilenum"]."','".date('y-m-d h:i:s',time())."',".$message_data["w"].",".$message_data["v"].",".$message_data["a"].",'登录-记录电表数据')";
				}
				Db::instance('db1')->query($sql);
				echo date('Y-m-d H:i:s',time()).">>login-连接日志入库，提交sid:".$message_data["sid"]."，桩号：".$ret1[0]["id"]."\n";
				//核对数据结束
		break;
       	//数据变动提交
	    case 'postdata':
			echo "postdata >>".date('Y-m-d H:i:s',time())."*****************************\n".$message."\n";
			//检测提交参数
			if(!isset($message_data["w"])||!isset($message_data["v"])||!isset($message_data["a"])||!isset($message_data["Ispower"])||!isset($message_data["Orderid"])){
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>postdata-参数不正确---断开\n";
				break;	
			}
			if($message_data["Orderid"]==0){
				//设备
				$retp = Db::instance('db1')->select('*')->from('db_pile')->where("client_id='".$client_id."' and isdelete=0 and isenable=1")->query();
				if(count($retp)!=1){
					Gateway::closeCurrentClient();
					echo date('Y-m-d H:i:s',time()).">>postdata-桩不存在---断开\n";
					break;
				}
				//站点
				$rets = Db::instance('db1')->select('*')->from('db_sitelist')->where('id='.$retp[0]["parentid"].' and isdelete=0 and isenable=1')->query();
				if(count($rets)!=1){
					Gateway::closeCurrentClient();
					echo date('Y-m-d H:i:s',time()).">>postdata-站点信息有误，桩id:".$retp[0]["id"]."---断开\n";
					break;
				}	
				//商家
				$retb = Db::instance('db1')->select('*')->from('db_sys_admin')->where('id='.$rets[0]["bid"])->query();
				if(count($retb)!=1){
					Gateway::closeCurrentClient();
					echo date('Y-m-d H:i:s',time()).">>postdata-商家信息有误，桩id:".$retb[0]["id"]."---断开\n";
					break;
				}	
				//记录日志
				$sql="insert into db_pnote(uid,uname,bid,bname,pid,pname,addtime,w,v,a,mark) values(0,'-',".$retb[0]["id"].",'".$retb[0]["username"]."',".$retp[0]["id"].",'".$retp[0]["pilenum"]."','".date('y-m-d h:i:s',time())."',".$message_data["w"].",".$message_data["v"].",".$message_data["a"].",'电表修改-记录电表数据')";
			Db::instance('db1')->query($sql);
			}else{
				$reto = Db::instance('db1')->select('*')->from('db_temp')->where("id=".$message_data["Orderid"]."")->query();
				$sql="insert into db_pnote(uid,uname,bid,bname,pid,pname,addtime,w,v,a,mark) values(".$reto[0]["uid"].",'".$reto[0]["uname"]."',".$reto[0]["bid"].",'".$reto[0]["bname"]."',".$reto[0]["pid"].",'".$reto[0]["pname"]."','".date('y-m-d h:i:s',time())."',".$message_data["w"].",".$message_data["v"].",".$message_data["a"].",'充电中-记录电表数据')";
				echo $sql."\n";
				Db::instance('db1')->query($sql);
				$sql="update db_temp set eleend =".$message_data["w"].",cpower=".$message_data["Cpower"].",lasttime='".date('y-m-d h:i:s',time())."' where id=".$reto[0]["id"];
				Db::instance('db1')->query($sql);	
			}
			break;
		//开始信息
	    case 'startdata':
			echo "startdata >>".date('Y-m-d H:i:s',time())."*****************************\n".$message."\n";
			//检测提交参数
			if(!isset($message_data["w"])||!isset($message_data["v"])||!isset($message_data["a"])||!isset($message_data["Orderid"])){
				echo date('Y-m-d H:i:s',time()).">>startdata-参数不正确---断开\n";
				Gateway::closeCurrentClient();
				break;	
			}
			echo "#0\r\n";
			//核对订单
			$reto = Db::instance('db1')->select('*')->from('db_temp')->where("id=".$message_data["Orderid"])->query();
			if(count($reto)!=1){
				//数据错误清除板卡信息
				//Gateway::sendToClient($client_id, "ClearData");
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>postdata-订单信息有误，订单id:".$message_data["Orderid"]."---断开\n";
				break;
			}
			echo "#1\r\n";
			//核对设备
			$retp = Db::instance('db1')->select('*')->from('db_pile')->where("id=".$reto[0]['pid'])->query();
			if(count($retp)!=1){
				//数据错误清除板卡信息
				//Gateway::sendToClient($client_id, "ClearData");
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>postdata-设备信息有误，订单id:".$message_data["Orderid"]."---断开\n";
				break;
			}
			echo date('Y-m-d H:i:s',time()).">>postdata-订单id:".$message_data["Orderid"]."---修改状态\n";
			//更新信息
			$sql="update db_temp set elecount =".$message_data["w"].",eleend=".$message_data["w"].",cpower=0,isenable=1,lasttime='".date('Y-m-d H:i:s',time())."' where id=".$reto[0]["id"];
			Db::instance('db1')->query($sql);
			$sql="update db_pile set ptype=1 where id=".$retp[0]['id'];
			Db::instance('db1')->query($sql);
			
			echo date('Y-m-d H:i:s',time()).">>postdata-订单信息修改完成，订单id:".$message_data["Orderid"]."---开启充电成功\n";
			break;
		//停止信息
		case 'stopdata':
			echo "stopdata >>".date('Y-m-d H:i:s',time())."*****************************\n".$message."\r\n";
			//检测提交参数
			if(!isset($message_data["w"])||!isset($message_data["v"])||!isset($message_data["a"])||!isset($message_data["c"])||!isset($message_data["Orderid"])){
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>stopdata-参数不正确---断开\r\n";
				break;
			}
			echo "#1\r\n";
			//核对订单
			$reto = Db::instance('db1')->select('*')->from('db_temp')->where("id=".$message_data["Orderid"]."")->query();
			if(count($reto)!=1){
				//数据错误清除板卡信息
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>stopdata-订单信息有误，订单id:".$message_data["Orderid"]."---断开\r\n";
				break;
			}
			if($reto[0]['isclose']==1){
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>stopdata-订单关闭，订单id:".$message_data["Orderid"]."---订单关闭\r\n";
				break;	
			}
			echo "#2\r\n";
			//更新信息
			$sql="update db_temp set eleend =".$message_data["w"].",cpower=".$message_data["Cpower"].",endcode=".$message_data["c"].",lasttime='".date('Y-m-d H:i:s',time())."',endcode=".$message_data["c"].",endtxt='".stoptxt($message_data["c"])."',endfacode=".$message_data["z"].",endfatxt='".stopfa($message_data["z"])."' where id=".$reto[0]["id"];
			Db::instance('db1')->query($sql);
			echo date('Y-m-d H:i:s',time()).">>stopdata-订单信息已更新，订单id:".$message_data["Orderid"]."---停止成功\r\n";
			$sql="update db_pile set ptype=0 where id=".$reto[0]['pid'];
			Db::instance('db1')->query($sql);
			/********结算*********/
			EndOrder($reto[0]["id"]);
			echo "触发结算\r\n";		
			break;
		//充电执行错误
		case 'starterr':
			echo "starterr >>".date('Y-m-d H:i:s',time())."*****************************\n".$message."\n";
			//检测提交参数
			if(!isset($message_data["code"])||!isset($message_data["Orderid"])){
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>starterr-参数不正确---断开\n";
				break;	
			}
			//订单检测
			$reto = Db::instance('db1')->select('*')->from('db_temp')->where("id=".$message_data["Orderid"]."")->query();
			if(count($reto)!=1){
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>postdata-订单信息有误，订单id:".$message_data["Orderid"]."---断开\n";
				break;
			}
			if($reto[0]['isclose']==1){
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>postdata-订单信息有误，订单id:".$message_data["Orderid"]."---订单关闭\n";
				break;	
			}
			//更新信息
			$sql="update db_temp set cpower=0,isenable=1,startcode=".$message_data["code"].",starterrtxt='".starttxt($message_data["code"])."',lasttime='".$reto[0]["addtime"]."' where id=".$reto[0]["id"];
			Db::instance('db1')->query($sql);
			echo date('Y-m-d H:i:s',time()).">>postdata-订单状态修改，订单id:".$message_data["Orderid"]."---启动失败\n";		
			/********结算*********/
			EndOrder($reto[0]["id"]);
			//echo "触发结算\n";

			break;
		case 'poststatus':
			echo "poststatus >>".date('Y-m-d H:i:s',time())."*****************************\n".$message."\n";
			//参数检测
			if(!isset($message_data["z"])||!isset($message_data["Orderid"])){
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>poststatus-参数不正确---断开\n";
				break;	
			}
			//订单检测
			$ret1=Db::instance('db1')->select('*')->from('db_temp')->where("id=".$message_data["Orderid"]." and isclose=0")->query();	
			if(count($ret1)!=1){
				Gateway::closeCurrentClient();
				echo date('Y-m-d H:i:s',time()).">>poststatus-不存在的充电---断开\n";
				break;
			}
			//过压修改
			if($message_data["z"]==1){
				$sql="update db_temp set isstatus=1 where id=".$message_data["Orderid"];
				Db::instance('db1')->query($sql);	
				echo date('Y-m-d H:i:s',time()).">>poststatus-修改为过压保护，充电数据id:".$ret1[0]["id"]."\n";
			}else{
				$sql="update db_temp set isstatus=0 where id=".$message_data["Orderid"];
				Db::instance('db1')->query($sql);
				echo date('Y-m-d H:i:s',time()).">>poststatus-修改为正常充电，充电数据id:".$ret1[0]["id"]."\n";		
			}	
			break;
		//ping信息	
		case 'ping':
			echo "ping >>".date('Y-m-d H:i:s',time())."\n";
			break;
		case 'disdata':	//距离数据
			echo "disdata >>".date('Y-m-d H:i:s',time())."*****************************\n".$message."\n";
			$sql="update db_pile set isnone=".$message_data["s"]." where client_id='".$client_id."'";
			Db::instance('db1')->query($sql);
			echo date('Y-m-d H:i:s',time()).">>disdata-探头状态修改，修改值为:".$message_data["s"]."\n";
			break;
		default:
			echo "其他 >>".date('Y-m-d H:i:s',time())."*****************************\n".$message."\n";
			break;
        }
   }
   
   /**
    * 当用户断开连接时
    * @param integer $client_id 用户id
    */
   public static function onClose($client_id){
	   $sql="update db_pile set islink =0,client_id='',ptype=0,isnone=3 where client_id='".$client_id."'";	
		Db::instance('db1')->query($sql);
		echo date('Y-m-d H:i:s',time()).">>onClose-设备状态已经恢复\n";
   }
}


//请求网络文件
function get_file($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_exec($ch);
	curl_close($ch);
}

//启动失败原因
function starttxt($code){
	switch($code){
		case 20:
			return "CC线未连接";
			break;
		case 21:
			return "检测到设备正在充电";
			break;
		case 22:
			return "充电反馈器异常";
			break;
		case 23:
			return "急停按钮被按下";
			break;
		case 24:
			return "您的枪未插好";
			break;
		case 25:
			return "车辆信号无法识别，请联系管理员";
			break;
		case 26:
			return "充电反馈信号超时";
			break;
		case 27:
			return "单次电压检测异常";
			break;
		case 28:
			return "该设备未连接电表"; 
			break;
		default:
			return "未知原因"; 
	}
}
//停止原因
function stoptxt($code){
	switch($code){
		case 0:
			return "平台停止成功";
			break;
		case 1:
			return "网页停止成功";
			break;
		case 2:
			return "CC线不正常";
			break;
		case 3:
			return "CCADC异常";
			break;
		case 4:
			return "充电接触器异常";
			break;
		case 5:
			return "急停按钮停止";
			break;
		case 6:
			return "设备过流";
			break;
		case 7:
			return "取消过压保护启动失败";
			break;
		case 8:
			return "电表掉线";
			break;
		case 9:
			return "输出跳枪";
			break;
		case 10:
			return "过压异常";
			break;
		case 11:
			return "过压拔枪";
			break;
		case 12:
			return "余额不足";
			break;
		case 13:
			return "未知";
			break;
		case 23:
			return "电表异常";
			break;
		case 24:
			return "余额不足";
			break;
		case 25:
			return "登录结算";
			break;
		default:
			return "未知原因";
			
	}
}
//触发失败文字
function stopfa($code){
	switch($code){
		case 0:
			return "正常关闭";
			break;
		case 14:
			return "充电接触器反馈超时";
			break;
		default:
			return "未知原因";
	}
}

//结算账单
function EndOrder($id){
	//订单检测
	$reto = Db::instance('db1')->select('*')->from('db_temp')->where("id=".$id)->query();
	if($reto[0]['isclose']==1){
		echo "订单已经结算\n";
		return;
	}
	$money=intval(ceil($reto[0]['uint']*(float)$reto[0]['cpower']/10));
	//退款
	if($money<$reto[0]['smoney']){
		//退款
		$order_info = array();
		$order_info['out_trade_no'] = $reto[0]['No'];
		$order_info['refund_trade_no'] =$reto[0]['No'];
    	$order_info['total_fee'] =$reto[0]['smoney'];
    	$order_info['refund_fee'] = ($reto[0]['smoney']-$money);
		require_once dirname(__FILE__).'/../../../XMWeb/API/wxpay/pay.php';
		$pay = new \Pay($order_info);
   		$res=$pay->refund();
		if($res){
			//写退款数据
			$sql="insert into db_wxcode(uid,tit,code,type,outid,addtime)values(".$reto[0]['uid'].",'".$reto[0]['No']."（结算）','".json_encode($res)."',4,".$reto[0]['id'].",'".date('Y-m-d H:i:s',time())."')";
			Db::instance('db1')->query($sql);
			setInitOrder($reto);
		}else{
			$sql="insert into db_err(tit,tdesc,type,addtime)values('".$reto[0]['No']."-退款失败','结算时执行退款操作失败',1,'".date('Y-m-d H:i:s',time())."')";
			Db::instance('db1')->query($sql);
		}		
	}else if($money==$reto[0]['smoney']){
		setInitOrder($reto);
	}else if($money>$reto[0]['smoney']){
		$sql="insert into db_err(tit,tdesc,type,addtime)values('".$reto[0]['No']."-金额验证有异常','结算时充电金额大于充值金额',2,'".date('Y-m-d H:i:s',time())."')";
		Db::instance('db1')->query($sql);
		setInitOrder($reto);
	}
}

//设置结算数据
function setInitOrder($reto){
	$id=$reto[0]['id'];
	$ele=$reto[0]['cpower'];
	$time=strtotime($reto[0]['lasttime'])-strtotime($reto[0]['addtime']);
	$money=intval(ceil($reto[0]['uint']*(float)$reto[0]['cpower']/10));
	$money=($money>$reto[0]['smoney'])?$reto[0]['smoney']:$money;
	//echo $ele.",".$time.",".$money.",".$id.",".(strtotime($reto[0]['lasttime'])-strtotime($reto[0]['addtime'])).",".$reto[0]['addtime'].",".$reto[0]['lasttime']."\n";
	//桩
	$retp = Db::instance('db1')->select('*')->from('db_pile')->where("id=".$reto[0]['pid'])->query();
	//平台
	$rets = Db::instance('db1')->select('*')->from('db_sys_site')->where("ver=0")->query();
	//商家
	$retb = Db::instance('db1')->select('*')->from('db_sys_admin')->where("id=".$reto[0]['bid'])->query();
	//用户
	$retu = Db::instance('db1')->select('*')->from('db_sys_userinfo')->where("id=".$reto[0]['uid'])->query();
	//修改订单
	$sql="update db_temp set tmoney=".($reto[0]['smoney']-$money).",money=".$money.",isclose=1,lasttime='".date('Y-m-d H:i:s',time())."' where id=".$id;
	Db::instance('db1')->query($sql);
	//修改桩信息
	$sql="update db_pile set smoney=".($retp[0]['smoney']+$money).",stime=".($retp[0]['stime']+$time).",snum=snum+1,sele=".($retp[0]['sele']+$ele)." where id=".$retp[0]['id'];
	Db::instance('db1')->query($sql);
	//修改平台
	$sql="update db_sys_site set smoney=".($rets[0]['smoney']+$money).",stime=".($rets[0]['stime']+$time).",snum=snum+1,sele=".($rets[0]['sele']+$ele).",money=".($rets[0]['money']+$money)." where id=".$rets[0]['id'];
	Db::instance('db1')->query($sql);
	//修改商家
	$sql="update db_sys_admin set smoney=".($retb[0]['smoney']+$money).",stime=".($retb[0]['stime']+$time).",snum=snum+1,sele=".($retb[0]['sele']+$ele).",money=".($retb[0]['money']+$money)." where id=".$retb[0]['id'];
	Db::instance('db1')->query($sql);
	//修改用户
	$sql="update db_sys_userinfo set smoney=".($retu[0]['smoney']+$money).",stime=".($retu[0]['stime']+$time).",snum=snum+1,sele=".($retu[0]['sele']+$ele)." where id=".$retu[0]['id'];
	Db::instance('db1')->query($sql);
	//增加商家和平台日志
	$sql="insert into db_sys_aslog(tid,type,bid,bname,cuint,cmoney,ctime,cele,addtime,bsmoney,bstime,bsnum,bsele,bmoney,btmoney,btnum,ssmoney,sstime,ssnum,ssele,smoney,stmoney,stnum) values(".$id.",1,".$reto[0]['bid'].",'".$reto[0]['bname']."',".$reto[0]['uint'].",".$money.",".$time.",".$ele.",'".date('Y-m-d H:i:s',time())."',".($retb[0]['smoney']+$money).",".($retb[0]['stime']+$time).",".($retb[0]['snum']+1).",".($retb[0]['sele']+$ele).",".($retb[0]['money']+$money).",".$retb[0]['tmoney'].",".$retb[0]['tnum'].",".($rets[0]['smoney']+$money).",".($rets[0]['stime']+$time).",".($rets[0]['snum']+1).",".($rets[0]['sele']+$ele).",".($rets[0]['money']+$money).",".$rets[0]['tmoney'].",".$rets[0]['tnum'].")";
	Db::instance('db1')->query($sql);
	//增加电桩和用户日志
	$sql="insert into db_pulog(tid,bid,bname,pid,pname,sid,sname,uid,uname,cuint,cmoney,ctime,cele,addtime,psmoney,pstime,psnum,psele,usmoney,ustime,usnum,usele) values(".$id.",".$reto[0]['bid'].",'".$reto[0]['bname']."',".$reto[0]['pid'].",'".$reto[0]['pname']."',".$reto[0]['sid'].",'".$reto[0]['sname']."',".$reto[0]['uid'].",'".$reto[0]['uname']."',".$reto[0]['uint'].",".$money.",".$time.",".$ele.",'".date('Y-m-d H:i:s',time())."',".($retp[0]['smoney']+$money).",".($retp[0]['stime']+$time).",".($retp[0]['snum']+1).",".($retp[0]['sele']+$ele).",".($retu[0]['smoney']+$money).",".($retu[0]['stime']+$time).",".($retu[0]['snum']+1).",".($retu[0]['sele']+$ele).")";
	Db::instance('db1')->query($sql);
}
