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
class PublicAction extends Action {
	/**
	 * 检查用户是否登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-19 上午9:50:57
	 */
	public function checkUser() {
		if(!isset($_SESSION[C('WEB_AUTH_KEY')])) {
			$this->redirect('/?m=Public&a=login');
		}
	}
	/**
	 * 用户登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:10:51
	 */
	public function login() {
        if(isset($_SESSION[C('WEB_AUTH_KEY')])) {
            redirect(C('WEB_URL'));
        }else{
            redirect(C('HOME_URL'));
        }
    }
	/**
	 * 用户登出
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:11:13
	 */
	public function logout(){
		if(isset($_SESSION[C('WEB_AUTH_KEY')])) {
            unset($_SESSION[C('WEB_AUTH_KEY')]);
            $this->redirect('/?m=Public&a=login');
        }else {
            $this->error('已经登出！');
        }
	}
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
			$re = R("Admin://Member/userAdd");
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