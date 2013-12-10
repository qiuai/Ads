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
class AdAdsAction extends CommonAction {
	function _initialize(){
		$this->assign("flag","ad");
	}
    public function index(){
		$this->assign("title","广告列表");
		$this->display();
    }
	public function ad_list(){
		$this->assign("title","广告列表");
		$this->display(index);
	}
	public function ad_add(){
		$this->assign("title","新增广告");
		$this->display();
	}
}