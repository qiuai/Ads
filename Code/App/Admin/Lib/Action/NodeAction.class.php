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
	public function nodeSave(){
		$this->insert();
	}
	/**
	 * 节点列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 下午4:22:34
	 */
	public function nodeList(){
		$model	=	M("");
		
		$sql = 'select n.*,g.title as gtitle from '.C('DB_PREFIX').'node n, '.C('DB_PREFIX').'group g where g.id = n.group_id and n.status = 1';
		$sqln = 'select count(*) from '.C('DB_PREFIX').'node n, '.C('DB_PREFIX').'group g where g.id = n.group_id and n.status = 1';

		$this->assign('list',$list);
		$this->assign('location',"权限列表");
		$this->display();
	}
	
	
	
	
	
	
	
	
	
	
}