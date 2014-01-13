<?php
/**
 * 广告联盟系统  公共操作
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午10:07:57
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午10:07:57      todo
 */
class PublicAction extends CommonAction {
	/**
	 * 用户注册
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-9 下午3:22:28
	 */
	public function register(){
		$model = M();
		$sql = "select u.* from " . C('DB_PREFIX') . "user u join " . C('DB_PREFIX') . "role_user r on u.id = r.user_id where r.role_id = 5";
		$list = $model->query($sql);
		$this->assign('list',$list);
		$this->display();
	}
	/**
	 * 会员注册
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-9 下午4:37:38
	 */
	public function userAdd(){
		if($this->isPost()){
			$re = R("Admin://Member/memberAdd",array(C('HOME_URL')));
		}
	}
	/**
	 * 用户登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-10 下午9:35:46
	 */
	public function login(){
		$this->redirect(C('SITE_URL'));
	}
	/**
	 * 用户登出
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-10 下午9:37:02
	 */
	public function logout(){
		if(isset($_SESSION[C('MEMBER_AUTH_KEY')])) {
			session_destroy();
            $this->redirect(__URL__.'/login/');
        }else {
            $this->error('已经登出！');
        }
	}
	/**
	 * 检查登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-10 下午9:35:28
	 */
	public function checkLogin(){
		if(empty($_POST['username'])) {
            $info['error'] = '帐号错误！';
            return $info;
        }elseif (empty($_POST['password'])){
            $info['error'] = '密码必须！';
            return $info;
        }
        //生成认证条件
        $map            =   array();
        // 支持使用绑定帐号登录
        $map['username']	= $_POST['username'];
        $map["status"]	=	array('eq',0);
        $model = M('Member');
        $authInfo = $model->where($map)->find();
        
        //使用用户名、密码和状态的方式进行认证
        if(false == $authInfo) {
        	$info['error'] = '帐号不存在或已禁用！';
        	return $info;
        }else {
            if($authInfo['password'] != $this->pwdHash($_POST['password'])) {
                $info['error'] = '密码错误！';
                return $info;
            }
            $_SESSION[C('MEMBER_AUTH_KEY')]	=	$authInfo['id'];
            if($authInfo['user_type']=='web') {
                $_SESSION[C('WEB_AUTH_KEY')]	=	$authInfo['id'];
                $_SESSION['loginWebName']		=	$authInfo['username'];
//                 unset($_SESSION[C('ADV_AUTH_KEY')]);
            }else{
            	$_SESSION[C('ADV_AUTH_KEY')]	=	$authInfo['id'];
            	$_SESSION['loginAdvName']		=	$authInfo['username'];
//             	unset($_SESSION[C('WEB_AUTH_KEY')]);
            }
            //保存登录信息
            $User	=	M('Member');
            $ip		=	get_client_ip();
            $time	=	time();
            $data = array();
            $data['id']	=	$authInfo['id'];
            $data['last_login_time']	=	$time;
            $data['login_count']	=	array('exp','login_count+1');
            $data['last_login_ip']	=	$ip;
            $User->save($data);
            
            $info['success'] = 1;
            return $info;
        }
	}
	/**
	 * 管理员 进入 网站主广告主后台 验证
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-21 下午2:15:56
	 * @return unknown
	 */
	public function adminLogin(){
		if( !$_REQUEST['username'] || !$_REQUEST['password']) {
			redirect(C('HOME_URL'));
		}
		//生成认证条件
		$map            =   array();
		// 支持使用绑定帐号登录
		$map['username']	= $_REQUEST['username'];
		$map['password']	= $_REQUEST['password'];
		$model = M('Member');
		$authInfo = $model->where($map)->find();
	
		//使用用户名、密码和状态的方式进行认证
		if(!empty($authInfo)) {
			
			$_SESSION[C('MEMBER_AUTH_KEY')]	=	$authInfo['id'];
			$_SESSION['email']				=	$authInfo['email'];
			$_SESSION['loginUserName']		=	$authInfo['username'];
			$_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
			$_SESSION['login_count']		=	$authInfo['username'];
			if($authInfo['user_type']=='web') {
				$_SESSION[C('WEB_AUTH_KEY')]=	$authInfo['id']; // 登入ID
				$_SESSION['loginWebName']	=	$authInfo['username']; // 登入账号
				unset($_SESSION[C('ADV_AUTH_KEY')]);
				if(empty($_GET["zone_id"])){ // 判断是否获取代码位ID
					redirect(C("WEB_URL"));	// 没得到跳转WEB首页
				}else{
					redirect(C("WEB_URL").'?m=ZoneWeb&a=zoneEdit&zone_id='.$_GET["zone_id"]); // 否则跳转代码位编辑页面
				}
				
			}else{
				$_SESSION[C('ADV_AUTH_KEY')]	=	$authInfo['id'];
				$_SESSION['loginAdvName']	=	$authInfo['username']; // 登入账号
				unset($_SESSION[C('WEB_AUTH_KEY')]);
				redirect(C("ADV_URL"));
			}
			
		}
	}
	/**
	 * 网站主 登录检测
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-11 下午1:22:21
	 */
	public function checkWeb(){
		$info = $this->checkLogin();
		if($info['success']){
			redirect(C("WEB_URL"));
		}else{
			$this->error($info['error']);
		}
	}
	/**
	 * 广告主 登录检测
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-11 下午1:22:21
	 */
	public function checkAdv(){
		$info = $this->checkLogin();
		if($info['success']){
			redirect(C("ADV_URL"));
		}else{
			$this->error($info['error']);
		}
	}
	/**
	 * 忘记密码，返回新密码
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-25 下午1:33:55
	 */
	public function forgetPwd(){
		if($this->isPost()){
			$username = $_POST['username'];
			$match = '/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i';
			if (preg_match($match,$username)){
				// 判断用户名是否存在
				$model = M("Member");
				$member = $model->field('id,real_name')->where("username = '$username'")->find();
				if(empty($member)){
					echo "用户名不存在！";
				}else{
					// 生成新密码
					$num = 8;	// 新密码长度
					$newPwd = $this->generatePwd($num);
					
					// 修改原密码
					$member['password'] = $this->pwdHash($newPwd);	
					if($model->save($member)){
						$Email = A('Admin://Email');
						// 发送邮件
						$title = "找回密码";
						$content = "您的新密码是：" . $newPwd . "【" . $num . "位】,登录后请修改新密码！";
						if($Email->thinkSendEmail($username, $member['real_name'], $title, $content)){
							$this->assign('jumpUrl',C('HOME_URL'));
							$this->success('密码已发送，请查收！');
						}else{
							$this->success('密码发送失败，请重新找回！');
						}
					}
				}
			}else{
				echo "邮箱格式不正确！";
			}
		}else{
			$this->display();
		}
	}
	/**
	 * 随机生成密码
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-25 下午2:04:32
	 * @param unknown_type $length
	 */
	public function generatePwd( $length = 8 ) {
		// 密码字符集，可任意添加你需要的字符
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	
		$password = '';
		for ( $i = 0; $i < $length; $i++ )
		{
			// 这里提供两种字符获取方式
			// 第一种是使用 substr 截取$chars中的任意一位字符；
			// 第二种是取字符数组 $chars 的任意元素
			// $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
			$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
		}
	
		return $password;
	}
	/**
	 * 验证码
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-28 下午6:19:02
	 */
	public function verify() {
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
		import("@.ORG.Util.Image");
		Image::buildImageVerify(4,1,$type);
	}
}