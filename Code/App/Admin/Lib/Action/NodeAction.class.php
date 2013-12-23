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
		if($this->isPost()){
			$model = M("Node");
			$level = $this->getLevel($_POST['pid']);
			$_POST['level'] = $level + 1;
			$this->insert($_REQUEST, C('SITE_URL')."?m=Node&a=nodeList");
		}else{
			$this->getGroup();
			$this->assign('location',"新增权限");
			$this->display();
		}
	}
	/**
	 * 节点列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 下午4:22:34
	 */
	public function nodeList(){
		$where = " where g.id = n.group_id and level = 2 order by n.group_id asc, n.sort desc, n.id asc ";
		$select =" n.*,g.title";
		$table = " ".C('DB_PREFIX').'node n, '.C('DB_PREFIX').'group g ';
		
		$p = $_GET['p'] ? $_GET['p'] : 1;
		$num = 2;
		$limit = " limit ". ($p-1)*$num .",".$num;
		
		$sql = "select $select from $table $where $limit";
		$count = "select count(n.id) as num from $table $where limit 1";
		
		// 菜单栏
		$list = $this->pageList($sql, $count, $num);
		
		// 菜单下栏目
		foreach($list as $key=>$value){
			// item 栏目标识
			$list[$key]['item'] = $this->getChildNode($value['id']);
			
			// 栏目下操作
			foreach($list[$key]['item'] as $k=>$v){
				$list[$key]['item'][$k]['item'] = $this->getChildNode($v['id']);
			}
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
		if($this->isPost()){
			if(!$_POST['group_id'] || !$_POST['pid']){
				unset($_POST['group_id']);
				unset($_POST['pid']);
			}
			$model = M('Node');
			if (false === $model->create()) {
				$this->error($model->getError());
			}
			// 更新数据
			$list = $model->save();
			if (false !== $list) {
				//成功提示
				$this->success('编辑成功!',C('SITE_URL').'?m=Node&a=nodeList');
			} else {
				//错误提示
				$this->error('编辑失败!');
			}
		}else{
			$this->getGroup();
			
			$model	=	M();
			$sql = "select * from ".C('DB_PREFIX')."node where id = ".$_GET['id'];
			$list = $model->query($sql);
				
			$this->assign('list',$list[0]);
			$this->assign('location',"修改权限");
			$this->display();
		}
	}
	/**
	 * 返回节点
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-5 上午10:51:17
	 */
	public function getCat(){
		$Node	 = M('Node');
		$where = " level = 2 or level = 3";
		$nodeList	 = $Node->where($where)->select();
		
		foreach ($nodeList as $value){
			$data[$value['group_id']][] = $value;
		}
	
		$this->assign("noleList",$data);
		
		echo json_encode($data);
	}
	/**
	 * 获得子节点
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-7 上午10:43:49
	 */
	public function getChildNode($pid){
		$model = M("Node");
		$where['pid'] = $pid;
		$childList = $model->where($where)->select();
		
		foreach($childList as $key=>$value){
			$group = $this->getMyGroup($value['group_id']);
			$childList[$key]['title'] = $group['title'];
		}
		
		return $childList;
	}
	/**
	 * 获得所有节点
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-7 上午10:14:26
	 */
	public function getNode(){
		$model	=	M("Node");
		$list = $model->select();
		return $list;
	}
	/**
	 * 获取所在分组
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-7 上午11:40:33
	 * @param unknown_type $id
	 * @return Ambigous <mixed, boolean, NULL, multitype:, unknown, string>
	 */
	public function getMyGroup($id){
		$Group = M("Group");
		$data = $Group->where("id = $id")->find();
		return $data;
	}
	/**
	 * 获得Level
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-6 上午10:48:29
	 */
	public function getLevel($id=0){
		$model	=	M("Node");
		$data  = $model->where("id=$id")->find();
	
		return $data['level'];
	}
}