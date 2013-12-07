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
	 * level
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午3:06:26
	 */
	public function roleAdd(){
		$nodeList = R('Node/getNode');
		
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
		
		$accesses = D("Access")->where("role_id=".$id)->field("node_id")->select();
		$node_ids = array();
		foreach($accesses as $access)
		{
			array_push($node_ids,$access['node_id']);
		}
		 
		//取出模块授权
		$Node	 = M('Node');
		$list	 = $Node->select();
		foreach($list as $value){
			// level = 2 导航管理
			// level = 3 模块管理
			// level = 4 方法管理
			$module[$value[level]][] = $value;
			$action[$value[nav_id]][] = $value;
		}
		foreach ($module[1] as $key=>$value){
			$module[1][$key]['action'] = $action[$value[id]];
		}
		$nav	= $module[0];
		$module = $module[1];
		 
		if(in_array(0,$node_ids)){
			$this->assign("nav_checked",1);
		}
		$checkall = true;
		foreach($nav as $nk=>$nd){
			if(in_array($nd['id'],$node_ids)){
				$nav[$nk]['checked'] = true;
			}else{
				$checkall = false;
				$nav[$nk]['checked'] = false;
			}
		}
		if($checkall){
			$this->assign("nav_checked_all",1);
		}
		 
		foreach($module as $mk=>$md)
		{
			if(in_array($md['id'],$node_ids))
				$module[$mk]['checked'] = true;
			else
				$module[$mk]['checked'] = false;
			 
			foreach($md['action'] as $ak=>$action)
			{
				$checkall = true;
				if(in_array($action['id'],$node_ids))
				{
					$module[$mk]['action'][$ak]['checked'] = true;
				}
				else
				{
					$checkall = false;
					$module[$mk]['action'][$ak]['checked'] = false;
				}
			}
			 
			if($checkall)
				$module[$mk]['checkall'] = true;
			else
				$module[$mk]['checkall'] = false;
		}
		
		$this->assign('modules',$module);
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