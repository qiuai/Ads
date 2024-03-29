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
	 * 检测用户是否登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:10:26
	 */
	public function checkUser(){
		if(!isset($_SESSION[C('ADV_AUTH_KEY')])) {
			$this->redirect('?m=Public&a=login');
		}
	}
	/**
	 * 用户登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:10:51
	 */
	public function login() {
        if(isset($_SESSION[C('ADV_AUTH_KEY')])) {
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
		if(isset($_SESSION[C('ADV_AUTH_KEY')])) {
			session_destroy();
			$this->redirect(C('HOME_URL'));
		}else {
			$this->error('已经登出！');
		}
	}
}