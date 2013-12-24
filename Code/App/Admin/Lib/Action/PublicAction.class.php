<?php
/**
 * 广告联盟系统 公共操作
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午9:57:14
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午9:57:14      todo
 */
class PublicAction extends Action {
	/**
	 * 用户登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:10:51
	 */
	public function login(){
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->display();
        }else{
            $this->redirect(C('SITE_URL'));
        }
	}
	/**
	 * 用户登出
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-24 上午10:16:18
	 */
	public function logout(){
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			session_destroy();
			$this->redirect('/?m=Public&a=login');
		}else {
			$this->error('已经登出！');
		}
	}
	/**
	 * 检测用户是否登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:10:26
	 */
	public function checkUser(){
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->redirect('/?m=Public&a=login');
        }
	}
	/**
	 * 登录验证
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-19 下午2:41:40
	 */
	public function checkLogin() {
		if(empty($_POST['account'])) {
			$this->error('帐号错误！');
		}elseif (empty($_POST['password'])){
			$this->error('密码必须！');
		}elseif (empty($_POST['verify'])){
			$this->error('验证码必须！');
		}
		//生成认证条件
		$map            =   array();
		// 支持使用绑定帐号登录
		$map['username']	= $_POST['account'];
		$map["status"]	=	array('gt',0);
		if(session('verify') != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}
		import ( '@.ORG.Util.RBAC' );
		$authInfo = RBAC::authenticate($map);
	
		//使用用户名、密码和状态的方式进行认证
		if(false === $authInfo) {
			$this->error('帐号不存在或已禁用！');
		}else {
			if($authInfo['password'] != md5($_POST['password'])) {
				$this->error('密码错误！');
			}
			$_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
			$_SESSION['email']	=	$authInfo['email'];
			$_SESSION['loginUserName']		=	$authInfo['username'];
			$_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
			$_SESSION['login_count']	=	$authInfo['username'];
			if($authInfo['username']=='admin') {
				$_SESSION['administrator']		=	true;
			}
			//保存登录信息
			$User	=	M('User');
			$ip		=	get_client_ip();
			$time	=	time();
			$data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_login_time']	=	$time;
			$data['login_count']	=	array('exp','login_count+1');
			$data['last_login_ip']	=	$ip;
			$User->save($data);
	
			// 缓存访问权限
			RBAC::saveAccessList();
			$this->redirect('/?m=Index&a=index');
	
		}
	}
	/**
	 * 左侧菜单
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-27 下午7:39:00
	 */
	public function menu(){
		$this->display();
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
	/**
	 * 清除缓存
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-23 下午6:09:49
	 * @param unknown_type $path
	 */
	public function clearCache()
	{
		header("Content-type: text/html; charset=utf-8");
		//清文件缓存
		$dirs = array(ROOT_PATH . '/Data/');
		@mkdir($dirs,0777,true);
		//清理缓存
		foreach($dirs as $value) {
		  $this->rmdirr($value);
		}
		$this->assign('jumpUrl',C('SITE_URL'));
		$this->success('系统缓存清除成功！');
	}
	/**
	 * 删除文件夹及文件
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-23 下午7:25:26
	 * @param unknown_type $dirname
	 * @return boolean
	 */
	public function rmdirr($dirname) {
		if (!file_exists($dirname)) {
			return false;
		}
		if (is_file($dirname) || is_link($dirname)) {
			return unlink($dirname);
		}
		$dir = dir($dirname);
		if($dir){
			while (false !== $entry = $dir->read()) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				//递归
				$this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
			}
		}
		$dir->close();
		return rmdir($dirname);
	}
}