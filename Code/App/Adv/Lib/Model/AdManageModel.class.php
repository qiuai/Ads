<?php

/**
 * 
 * 
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2013-12-14 下午3:07:05
 * @version 1.0
 */
class AdManageModel extends Model{
	
	// 定义验证规则
	protected $_validate = array(
			
		array('pid','number','广告所属计划的id值必须为数值',1,'',1), // pid 所属计划的id值必须验证 必须是数值
		array('show_type','number','广告展现形式值必须为数值',1,'',1), // 广告展现形式值必须验证 必须是数值
		array('title','require','广告标题必须存在',1), // 广告标题必须存在 必须验证
		array('title','','广告标题不能重复',0,'unique',3), // 广告标题不能重复
		array('jump_url','url','目标网址格式不正确',1), // 目标网址必须存在必须验证
		//array('content','require','广告内容或图片必须存在',1), // 广告标题必须存在 必须验证
	);
	
	// 自动完成规则
	/*protected $_auto = array(
		array("sid",'getSid',3,'function'),//array("sid",$_SESSION['authId']),		// 添加数据时把广告主id
		array('size','getSize',3,'function'),	// 自动添加得到图片的尺寸添加到数据库
	);
	*/
	/**
	 * 
	 * 定义方法获取size值
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-16 上午11:33:50
	 */
	function getSize(){
		
		// 得到show_type值
		$showType = $_REQUEST['show_type'];
		
		// 查询adSize的表得到尺寸的相关信息
		$adSize = M('AdSize');
		
		// 查询相关的尺寸信息
		$adSizeInfo = $adSize->where("show_type = ".$showType)->find();
		
		// 返回相关的值
		return $adSizeInfo['width']."*".$adSizeInfo['height'];
		
	}
	
	function getSid(){
		
		return $_SESSION['authId'];
	}
	
}