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
class SiteWebAction extends CommonAction {
	function _initialize(){
		$this->assign("flag","site");
	}
    public function index(){ 
		$this->assign("title","网站列表");
		$this->display();
    }
	public function site_list(){
		$this->assign("title","网站列表");
		$this->display(index);
	}
	public function site_add(){
		$this->assign("title","新增网站");
		$this->display();
	}
}