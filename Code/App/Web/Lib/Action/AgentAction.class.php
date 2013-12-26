<?php
/**
 * 广告联盟系统 代理类
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午10:02:31
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午10:02:31      todo
 */
class AgentAction extends Action {
	
	private $Report;	// 报表类
	
	/**
	 * 报表实例化
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-25 下午6:17:04
	 */
	public function report(){
		$this->Report = A("Admin://Report");
	}
}