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
			$re = R("Admin://Member/memberAdd");
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
        $map["status"]	=	array('gt',0);
        $model = M('Member');
        $authInfo = $model->where($map)->find();
        
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
        	$info['error'] = '帐号不存在或已禁用！';
        	return $info;
        }else {
            if($authInfo['password'] != $this->pwdHash($_POST['password'])) {
                $info['error'] = '密码错误！';
                return $info;
            }
            $_SESSION[C('MEMBER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
            $_SESSION['loginUserName']		=	$authInfo['username'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
            $_SESSION['login_count']	=	$authInfo['username'];
            if($authInfo['user_type']=='web') {
                $_SESSION[C('WEB_AUTH_KEY')]	=	$authInfo['id'];
                unset($_SESSION[C('ADV_AUTH_KEY')]);
            }else{
            	$_SESSION[C('ADV_AUTH_KEY')]	=	$authInfo['id'];
            	unset($_SESSION[C('WEB_AUTH_KEY')]);
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