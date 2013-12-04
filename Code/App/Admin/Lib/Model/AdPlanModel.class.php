<?php
class	AdPlanModel extends Model{
		
	// 定义验证规则
	protected $_validate = array(
		array('uid','number','广告主uid必须为数字',1), // 	广告主id验证
		array('jump_url','url','目标网址格式不正确',1), // 验证目标网址
		array('plan_name','require','计划名称必须存在',1), // 验证目标网址
			
		array('plan_name','','计划名称不能重复',1,'unique',1),  // 计划名称必须唯一
		
	);
}