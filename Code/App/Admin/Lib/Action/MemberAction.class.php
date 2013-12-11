<?php
/**
 * 广告联盟系统  会员管理
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午9:58:45
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午9:58:45      todo
 */
class MemberAction extends CommonAction {
    public function index(){
    	$this->memberList();
	}
	/**
	 * 会员注册
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-9 下午4:37:38
	 */
	public function userAdd(){
		if($this->isPost()){
			if(empty($_POST['username'])) {
				$this->error('帐号错误！');
			}elseif (empty($_POST['password'])){
				$this->error('密码必须！');
			}elseif (empty($_POST['verify'])){
				$this->error('验证码必须！');
			}elseif($_POST['password'] != $_POST['confirm_password']){
				$this->error("密码不一致！");
			}
			$Member = M("Member");
			$_POST['status'] = 1; // 状态
			$_POST['password'] = MD5($_POST['password']); // 密码加密
			$_POST['create_time'] = time(); // 创建时间
			$_POST['ip'] = $_SERVER['SERVER_ADDR']; // 创建时间
			if($Member->create()){
				if($Member->add()){
					$this->success("注册成功！");
				}else{
					$this->error("注册失败！");
				}
			}else{
				$this->error("注册失败！");
			}
		}
	}
	/**
	 * 会员列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-11 下午2:36:12
	 */
	public function memberList(){
		$model = M("Member");
		$member = $model->select();
		
		$this->assign("location","会员列表");
		$this->display("list");
// 		var_dump($member);
	}
}