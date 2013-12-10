<?php
class	AdPlanModel extends RelationModel{

	// 定义变量保存表前缀
	private  $table_pre;
	
	// 定义验证规则
	protected $_validate = array(
		array('uid','number','广告主uid必须为数字',1), // 	广告主id验证
		array('jump_url','url','目标网址格式不正确',1), // 验证目标网址
		array('plan_name','require','计划名称必须存在',1), // 验证目标网址
			
		array('plan_name','','计划名称不能重复',1,'unique',1),  // 计划名称必须唯一
		
	);
	
	// 定义关联的相关规则
	/*protected $_link = array(
			'AdPlanIndustry'=>	array(
				'mapping_type' => HAS_ONE,
				'class_name' => 	'Industry',
				//'foreign_key' => 'categoryid',
				'condition' => 'AdPlan.category_id = industry.id',
				'as_fields' => 'name'
					
			)
	);*/
	
	// 初始化值
	/*function __construct(){
		parent::__construct();
		$this->table_pre = C('DB_PREFIX');
	}*/
	
	// 定义获取广告计划列表信息的方法
	/*public function getAdPlanInfo(){
		
		// 连表查询相关的数据
		$this->
	}*/
}