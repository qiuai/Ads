<?php
/**
 * 广告联盟系统  财务管理
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
class FinanceWebAction extends CommonAction {
	// 提现明细
    public function index(){
		// 创建提现明细表对象
		$fi=M("finance");
		// 按状态检索
		$status=(int)($_POST["status"]);
		if(empty($status)){
			$finance=$fi->select();
		}else{
			$finance=$fi->where("payment_status =".$status)->select();
		}
		foreach($finance as $key =>$val){
			$finance[$key]["withdrawal_date"] = date("Y-m-d",$val["withdrawal_date"]);
			$finance[$key]["paid_time"] = date("Y-m-d",$val["paid_time"]);
			switch($val["payment_status"]){
				case 1:
					$finance[$key]["payment_status"] = "未支付";
				break;
				case 2:
					$finance[$key]["payment_status"] = "已支付";
				break;
				case 3:
					$finance[$key]["payment_status"] = "异常";
				break;
				default:
				break;
			}
		}
		$this->assign("title","提现明细");
		$this->assign("finance",$finance);
		$this->display();
    }
	public function financePay(){
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