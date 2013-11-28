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
		$this->display();
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
}