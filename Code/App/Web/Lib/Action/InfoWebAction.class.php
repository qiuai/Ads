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
class InfoWebAction extends CommonAction {
	function _initialize(){
		$this->assign("flag","info");
	}
    public function index(){ 
		$this->assign("title","修改个人信息");
		$this->display();
    }
	public function info_info(){
		$this->assign("title","修改个人信息");
		$this->display(index);
	}
	public function info_password(){
		$this->assign("title","修改密码");
		$this->display();
	}
	public function info_orderlist(){
		$this->assign("title","商品订单列表");
		$this->display();
	}
	public function info_address(){
		$this->assign("title","收货地址列表");
		$this->display();
	}
	public function info_goods(){
		$this->assign("title","兑换物品");
		$this->display();
	}
}