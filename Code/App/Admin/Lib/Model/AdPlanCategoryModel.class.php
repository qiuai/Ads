<?php
class AdPlanCategoryModel extends Model{
	
	// 定义验证规则
	protected $_validate = array(
			
		array('name','require','分类名称必须填写',1), // 分类名称的验证
		array('name','','分类名称不能重复',0,'unique',3),
		array('sort','number','排序号必须为数字',1,'',3), // 排序号的验证			
	);
	

}