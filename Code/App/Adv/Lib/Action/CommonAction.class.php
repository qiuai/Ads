<?php
/**
 * 广告联盟系统 公共方法
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午10:02:31
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午10:02:31      todo
 */
class CommonAction extends Action {
	/**
	 * 构造函数
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:07:29
	 */
	function _initialize(){
    	$this->checkUser();
    }
    /**
     * 检测登录
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-12 下午6:04:13
     */
    public function checkUser(){
    	$Public = A('Agent');
    	$Public->checkUser();
    }
}