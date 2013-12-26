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
class FinanceAdvAction extends CommonAction {
    public function index(){ 
		$this->assign("title","账户充值");
		$this->display();
    }
	public function financeCharge(){
		$this->assign("title","账户充值");
		$this->display(index);
	}
	public function financeList(){
		$this->assign("title","充值记录");
		$this->display();
	}
}