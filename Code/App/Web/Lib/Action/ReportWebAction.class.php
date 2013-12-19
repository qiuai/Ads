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
class ReportWebAction extends CommonAction {
    public function index(){ 
		$this->assign("title","综合报表");
		$this->display();
    }
	public function report_all(){
		$this->assign("title","综合报表");
		$this->display(index);
	}
	 public function report_cps(){ 
		$this->assign("title","CPS明细报表");
		$this->display();
    }
	 public function report_cpa(){ 
		$this->assign("title","CPA明细报表");
		$this->display();
    }
	 public function report_cpc(){ 
		$this->assign("title","CPC明细报表");
		$this->display();
    }
	 public function report_cpm(){ 
		$this->assign("title","CPM明细报表");
		$this->display();
    }
}