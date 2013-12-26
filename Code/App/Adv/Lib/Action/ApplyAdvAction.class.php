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
class ApplyAdvAction extends CommonAction {
    public function index(){ 
		$this->assign("title","投放申请列表");
		$this->display();
    }
	//投放申请
	public function applyList(){
		$this->assign("title","投放申请列表");
		$this->display(index);
	}
}