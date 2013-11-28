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
class MoneyWebAction extends CommonAction {
	function _initialize(){
		$this->assign("flag","money");
	}
    public function index(){
		$this->assign("title","提现明细");
		$this->display();
    }
	public function money_list(){
		$this->assign("title","提现明细");
		$this->display(index);
    }
	public function money_pay(){
		$this->assign("title","申请提现");
		$this->display();
    }
	public function money_detail(){
		$this->assign("title","结算明细");
		$this->display();
    }
	public function money_bank(){
		$this->assign("title","银行信息");
		$this->display();
    }
}