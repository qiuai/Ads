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
class PlanWebAction extends CommonAction {	
    public function index(){ 
		$this->assign("title","广告活动列表");
		$this->display();
    }
	public function plan_list(){
		$this->assign("title","广告活动列表");
		$this->display(index);
	}
	public function plan_mine(){
		$this->assign("title","我的申请活动");
		$this->display();
	}
	public function plan_coupon_my(){
		$this->assign("title","我的优惠券");
		$this->display();
	}
	public function plan_coupon_list(){
		$this->assign("title","申请优惠券");
		$this->display();
	}
}