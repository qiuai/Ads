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
class ReportAdvAction extends CommonAction {
    public function index(){ 
		$this->assign("title","广告综合报表");
		$this->display();
    }
	public function reportAll(){
		$this->assign("title","广告综合报表");
		$this->display(index);
	}
	public function reportCps(){
		$this->assign("title","CPS明细报表");
		$this->display();
	}
	public function reportCpa(){
		$this->assign("title","CPA明细报表");
		$this->display();
	}
	public function reportCpc(){
		$this->assign("title","CPC明细报表");
		$this->display();
	}
	public function reportCpm(){
		$this->assign("title","CPM明细报表");
		$this->display();
	}
}