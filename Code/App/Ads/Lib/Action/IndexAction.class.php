<?php
/**
 * 广告联盟系统  首页
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
class IndexAction extends CommonAction {
	//首页
    public function index(){ 
		$this->display();
    }
	//计划管理
	public function plan(){
		$this->display();
	}
	//广告管理
	public function ad(){
		$this->display();
	}
	//查看报表
	public function report(){
		$this->display();
	}
	//投放申请
	public function apply(){
		$this->display();
	}
	//财务管理
	public function money(){
		$this->display();
	}
	//个人信息
	public function info(){
		$this->display();
	}
}