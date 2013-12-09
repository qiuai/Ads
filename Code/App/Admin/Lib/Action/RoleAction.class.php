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
		$group = $this->getPower();
		
		$this->assign('roleList',$group);
		
		$this->display();
	}
	/**
	 * 角色编辑
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午3:06:37
	 */
	public function roleEdit(){
		
		$id = $_GET['id'];
		
		$this->getName();
		
		$accesses = D("Access")->where("role_id=".$id)->field("node_id")->select();
		$node_ids = array();
		foreach($accesses as $access)
		{
			array_push($node_ids,$access['node_id']);
		}
		 
		$group = $this->getPower();
		 
		// 应用信息
		foreach($group as $gk=>$gv)
		{
			// 栏目类别
			foreach($gv['item'] as $mk=>$mv)
			{
				if(in_array($mv['id'],$node_ids))
				{
					$mv['checked'] = 1;
				}
				else
				{
					$mv['checked'] = 0;
				}
				
				// 操作列表
				foreach($mv['item'] as $ak=>$av)
				{
					if(in_array($av['id'],$node_ids))
					{
						$av['checked'] = 1;
					}
					else
					{
						$av['checked'] = 0;
					}
					$mv['item'][$ak] = $av;
				}
				$gv['item'][$mk] = $mv;
			}
			$group[$gk] = $gv;
		}
		
		$this->assign('roleList',$group);
		$this->assign("location","修改管理员组");
		$this->display();
	}
	/**
	 * 角色保存
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-9 上午10:32:26
	 */
	public function roleSave(){
    	$model = M("Role");
    	if(false === $data = $model->create())
    	{
    		$this->error($model->getError());
    	}
    
    	//保存当前数据对象
    	$list=$model->add($data);
    	if ($list !== false)
    	{
    		$node_ids = $_REQUEST['id'];
    		foreach($node_ids as $node_id)
    		{
    			$access['role_id'] = $list;
    			$access['node_id'] = $node_id;
    			M("Access")->add($access);
    		}
    		
    		$this->success ("添加成功！");
    	}
    	else
    	{
    		$this->error (L('添加失败！'));
    	}
	}
	/**
	 * 获取权限数据
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-9 上午11:44:11
	 */
	public function getPower(){
		$model	=	M("");
		$Node = A("Node");
		
		$where = " where g.id = n.group_id and level = 2 order by id ";
		$select =" n.*,g.title";
		$table = " ".C('DB_PREFIX').'node n, '.C('DB_PREFIX').'group g ';
		
		$sql = "select $select from $table $where";
		
		// 菜单栏
		$list = $model->query($sql);
		
		// 菜单下栏目
		foreach($list as $key=>$value){
			// item 栏目标识
			$list[$key]['item'] = $Node->getChildNode($value['id']);
				
			// 栏目下操作
			foreach($list[$key]['item'] as $k=>$v){
				$list[$key]['item'][$k]['item'] = $Node->getChildNode($v['id']);
			}
			$group[$value['group_id']]['item'][] = $list[$key];
			$title = $Node->getMyGroup($value['group_id']);
			$group[$value['group_id']]['title'] = $title['title'];
		}
		
		return $group;
	}
	/**
	 * 数据更新
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-9 下午1:25:22
	 */
	public function roleUpdate()
	{
		$role_id = intval($_REQUEST['id']);
		$model = M ( "Role" );
		$model->create ();
		// 更新数据
		$list=$model->save();
		if (false !== $list)
		{
			D("Access")->where("role_id=".$role_id)->delete();
			$node_ids = $_REQUEST['node_id'];
			foreach($node_ids as $node_id)
			{
				$access['role_id'] = $role_id;
				$access['node_id'] = $node_id;
				M("Access")->add($access);
			}
			 
			$this->success ("更新成功！");
		}
		else
		{
			//错误提示
			$this->error ("木有更新！");
		}
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
		
		$this->assign("roleId",$roleList['id']);
		$this->assign("roleName",$roleList['name']);
		
		return $roleList['name'];
	}
}