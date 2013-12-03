<?php
/**
 * 广告联盟系统  角色管理
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-12-3 下午3:04:12
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-12-3 下午3:04:12      todo
 */
class RoleAction extends CommonAction {
	/**
	 * 角色列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午3:05:45
	 */
	public function roleList(){
		
		$this->getRole();
		
		$this->display();
	}
	/**
	 * 角色添加
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午3:06:26
	 */
	public function roleAdd(){
		$this->display();
	}
	/**
	 * 角色编辑
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午3:06:37
	 */
	public function roleEdit(){
		
		$this->getName();
		
		$this->getRole();
		
		$this->assign("location","修改管理员组");
		$this->display();
	}
	/**
	 * 返回角色
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午2:24:03
	 */
	public function getRole(){
		$Role = M("Role");
		$roleList = $Role->select();
	
		$this->assign("roleList",$roleList);
	}
	/**
	 * 返回组名
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午3:28:22
	 */
	public function getName(){
		$Role = M("Role");
		$roleList = $Role->find($_GET['id']);
		
		$this->assign("roleName",$roleList['name']);
		
		return $roleList['name'];
	}
}