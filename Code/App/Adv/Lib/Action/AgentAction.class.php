<?php
/**
 * 广告联盟系统 权限系统 中介
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
class AgentAction extends Action {
	
	private $Authority;
	
	/**
	 * 构造函数
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-28 下午5:46:32
	 */
	function _initialize(){
		$this->Authority = A("Authority://Public");
	}
	/**
	 * 检测用户是否登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:10:26
	 */
	public function checkUser(){
		$this->Authority->checkUser();
	}
	/**
	 * 用户登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:10:51
	 */
	public function login(){
		$this->Authority->Login();
	}
	/**
	 * 用户登出
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:11:13
	 */
	public function logout(){
		$this->Authority->Logout();
	}
	/**
	 * 检测登录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-28 下午5:24:04
	 */
	public function checkLogin(){
		$this->Authority->checkLogin();
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
}