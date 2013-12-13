<?php

/**
 * 
 * 广告计划分类相关
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2013-12-12 下午6:04:26
 * @version 1.0
 */
class AdPlanCategoryAction extends CommonAction{
	
	private $actionName;
	
	/**
	 * 
	 * 初始化方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午9:58:48
	 */
	function _initialize(){
	
		// 先调用父类的初始化方法
		parent::_initialize();
			
		// 初始化数据库句柄
		//$this->AdPlan =
		$this->actionName = $this->getActionName();
		$this->table_pre = C('DB_PREFIX');
	}
	
	/**
	 * 
	 * 广告计划分类显示
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午6:05:16
	 */
	public function index(){
		
		// 查询相关的数据
		$AdPlanCategory = M('AdPlanCategory');
		$AdPlanCategoryInfo = $AdPlanCategory->select();
		$AdPlanCategoryInfo = $this->dealDataArr($AdPlanCategoryInfo);
		
		// 把数据分配到前端显示
		$this->assign('AdPlanCategoryInfo',$AdPlanCategoryInfo);
		
		//dump($AdPlanCategoryInfo);
		
		$this->display();
	}
	
	/**
	 * 
	 * 广告计划分类的编辑
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午9:10:49
	 */
	public function edit(){
		
		// 查询相关的数据
		$AdPlanCategory = M('AdPlanCategory');
		
		// 处理提交过来的数据
		$this->dealSubmitData();
		
		// 查询相关的数据
		$AdPlanCategoryInfo = $AdPlanCategory->where('id = '.$_GET['id'])->find();
		
		
		// 处理相关的数据
		//$AdPlanCategoryInfo = $this->dealDataOne($AdPlanCategoryInfo);
		
		// 数据分配到前端模版
		$this->assign('AdPlanCategoryInfo',$AdPlanCategoryInfo);
		
		$this->display();		
	}
	
	/**
	 * 
	 * 处理提交过来的数据
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-13 上午9:50:36
	 */
	private function dealSubmitData(){
	
		// 如果有提交过来id值则把id值转化为整型
		if($_POST['id']){
			$_POST['id'] = intval($_POST['id']);
		}
		
		if($_GET['id']){
			$_GET['id'] = intval($_GET['id']);
		}
	
	}
	
	/**
	 * 
	 * 广告计划分类的数据编辑的确认
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-13 上午9:23:08
	 */
	public function doEdit(){
		
		// 创建数据库资源对象
		$AdPlanCategory = D('AdPlanCategory');
		
		if(!$AdPlanCategory->create()){
			
			// 说明未通过验证
			$this->error($AdPlanCategory->getError(),C('SITE_URL')."?m=".$this->actionName.'&a=edit&id='.$_POST['id']);
			
		}else{
			
			if($AdPlanCategory->save()){
				$this->success("数据修改成功",C('SITE_URL')."?m=".$this->actionName.'&a=index');
			}else{
				$this->error("数据修改失败",C('SITE_URL')."?m=".$this->actionName.'&a=edit&id='.$_POST['id']);
			}
		}
		
	}
	/**
	 * 
	 * 广告计划分类的添加
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午6:06:06
	 */
	public function add(){
		
		
		$this->display();
	}
	
	/**
	 * 
	 * 计划分类数据添加到数据库
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午9:50:35
	 */
	public function doAdd(){
		
		// 创建数据库资源对象
		$AdPlanCategory = D('AdPlanCategory');
		
		if(!$AdPlanCategory->create()){ // 表单提交的数据不符合规则 跳转到数据添加的页面重新添加数据
			
			$this->error($AdPlanCategory->getError(),C('SITE_URL')."?m=".$this->actionName.'&a=add');
			
		}else{
			
			// 往数据库中添加数据
			$AdPlanCategory->add();
			$this->success('数据添加成功',C('SITE_URL')."?m=".$this->actionName.'&a=index');
		}
	}
	
	/**
	 * 
	 * 处理数据的方法(对于二维数组)
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午8:15:27
	 */
	private function dealDataArr($AdPlanCategoryInfo){
		
		// 遍历数据
		foreach($AdPlanCategoryInfo as $key=>$val){
			$AdPlanCategoryInfo[$key] = $this->dealData($AdPlanCategoryInfo[$key]);
		}
		
		return $AdPlanCategoryInfo;
	}
	
	/**
	 * 
	 * 处理数据的方法(对于一维数组)
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午8:50:59
	 * @param unknown_type $AdPlanCategoryInfo
	 */
	private function dealDataOne($AdPlanCategoryInfo){
		
		$AdPlanCategoryInfo = $this->dealData($AdPlanCategoryInfo);
		return $AdPlanCategoryInfo;
	}
	
	/**
	 * 
	 * 具体处理数据的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午8:24:15
	 * @param unknown_type $AdPlanCategoryInfo
	 */
	private function dealData($AdPlanCategoryInfo){
		
		// 处理显示的状态信息 如果为0则表示隐藏 1表示显示
		switch ($AdPlanCategoryInfo['display']){
			case 0:
				$AdPlanCategoryInfo['display'] = '隐藏';
				break;
			case 1:
				$AdPlanCategoryInfo['display'] = '显示';
				break;				
		}
		return  $AdPlanCategoryInfo;
	}
}