<?php

/**
 * 
 * 
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2013-12-13 下午2:45:26
 * @version 1.0
 */
class AdSizeModel extends Model{
	
	// 定义验证规则
	protected $_validate = array(
		array('description','require','尺寸描述不能为空',1), // 验证目标网址 	
		array('width','number','宽度必须为数值',1), // 宽度必须为数字
		array('height','number','高度必须为数值',1), // 高度必须为数字
		array('sort','number','排序值必须为数字',1), // 排序值必须为数字
	);
	
	
}