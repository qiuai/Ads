<?php
/**
 * 广告联盟系统 节点管理
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
class NodeAction extends CommonAction {
	/**
	 * 新增节点
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 下午4:03:38
	 */
	public function nodeAdd(){
		$this->assign('location',"新增权限");
		$this->display();
	}
	/**
	 * 节点列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 下午4:22:34
	 */
	public function nodeList(){
		$model	=	M("");
		
		$where = " where g.id = n.group_id and level = 2 order by id ";
		$select =" n.*,g.title as gtitle ";
		$table = " ".C('DB_PREFIX').'node n, '.C('DB_PREFIX').'group g ';
		
		$sql = "select $select from $table $where";
		
		$list = $model->query($sql);
		
		foreach($list as $key=>$value){
			$where = " where g.id = n.group_id and level = 3 and n.pid = ". $value[id] ." order by id ";
			$sql = "select $select from $table $where";
			
			$item = $model->query($sql);
			$list[$key]['item'] = $item;
		}
		
		$this->assign('list',$list);
		$this->assign('location',"权限列表");
		$this->display();
	}
	/**
	 * 编辑节点
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 上午10:23:04
	 */
	public function nodeEdit(){
		$model	=	M("");
		$sql = "select * from ".C('DB_PREFIX')."node where id = ".$_GET['id'];
		$list = $model->query($sql);
		
		$this->assign('list',$list[0]);
		$this->assign('location',"修改权限");
		$this->display();
	}
	
	
	
	
	
	
	
	
	
	
}