<?php

/**
 * 
 * 广告尺寸管理对应控制器
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2013-12-13 上午11:14:22
 * @version 1.0
 */
class AdSizeAction extends CommonAction{
	
	private $actionName;
	
	function _initialize(){
	
		// 先调用父类的初始化方法
		parent::_initialize();
			
		// 初始化数据库句柄
		//$this->AdPlan =
		$this->actionName = $this->getActionName();
		//$this->table_pre = C('DB_PREFIX');
	}
	/**
	 * 显示尺寸列表
	 * (non-PHPdoc)
	 * @see CommonAction::index()
	 */
	public function index(){
		
		// 创建数据库对象
		$AdSize = M('AdSize');
				
		// 查询相关的数据
		$AdSizeInfo = $AdSize->select();
		
		$AdSizeInfo = $this->dealDataArr($AdSizeInfo);
		// 数据分配到前端模版
		$this->assign('AdSizeInfo',$AdSizeInfo);
		
		// 处理相关的数据
		
	dump($AdSizeInfo);
		$this->display();
	}
	
	/**
	 * 
	 * 处理查询出的数据（针对二维数组）
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-13 下午3:48:25
	 */
	private function dealDataArr($AdSizeInfo){
		
		// 遍历数据
		foreach($AdSizeInfo as $key=>$val){
			$AdSizeInfo[$key] = $this->dealData($AdSizeInfo[$key]); 
		}
		return $AdSizeInfo;
	}
	
	/**
	 * 
	 * 处理查询出的数据（针对一维数组）
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-13 下午3:51:26
	 * @param unknown_type $AdSizeInfo
	 */
	private function dealDataOne($AdSizeInfo){
		
		$AdSizeInfo = $this->dealData($AdSizeInfo);
	}
	
	/**
	 * 
	 * 具体处理数据对应的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-13 下午3:52:46
	 * @param unknown_type $AdSizeInfo
	 */
	private function  dealData($AdSizeInfo){
		
		// 获取广告位置分类信息 （先存储在配置文件中读取配置文件)
		$sizeType = C('AD_SIZE_TYPE');
		
		// 处理数值 把size_type的数值转化为名称
		$AdSizeInfo['size_type'] = $sizeType[$AdSizeInfo['size_type']];
		
		return $AdSizeInfo;
		
	}
	public function add(){
		
		// 获取广告位置分类信息 （先存储在配置文件中读取配置文件) 
		$sizeType = C('AD_SIZE_TYPE');
			
		//dump($sizeType);
		$sizeType = $this->dealAdSizeType($sizeType);
		//dump($sizeType);
		// 数据分配到前端模版
		$this->assign('sizeType',$sizeType);
		// 显示模版
		$this->display();
	}
	
	/**
	 * 
	 * 新增广告尺寸信息入库
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-13 下午2:43:00
	 */
	public function doAdd(){
		
		// 创建数据库对象
		$AdSize = D('AdSize');
		
		if(!$AdSize->create()){
			
			// 说明验证未通过，提示未通过的原因 跳转到添加页面
			$this->error($AdSize->getError(),C('SITE_URL')."?m=".$this->actionName.'&a=add&'); 
		}else{
			
			// 往数据库中添加数据
			if($AdSize->add()){
				
				// 说明数据添加成功
				$this->success('广告尺寸信息添加成功',C('SITE_URL')."?m=".$this->actionName.'&a=index&');
			}else{
				
				// 说明数据添加失败
				$this->error('数据添加失败',C('SITE_URL')."?m=".$this->actionName.'&a=add&');
			}
		}
		
	}
	/**
	 * 
	 * 把广告位置分类信息转化为二维数组包含
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-13 下午2:03:02
	 */
	private function dealAdSizeType($sizeType){
		
		// 定义数组保存相关的数据
		$sizeTypeInfo = array();
		
		// 定义变量保存数组的下标
		$i = 0;
		
		// 遍历数据		
		foreach ($sizeType as $key => $val){
			$sizeTypeInfo[$i]['key'] = $key;
			$sizeTypeInfo[$i]['val'] = $val;
			$i++; 	
		}
		return $sizeTypeInfo;
	}
	
	
	
}