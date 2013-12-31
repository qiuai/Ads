<?php
/**
 * 联盟系统  广告展现类
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-12-30 上午10:54:50
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-12-30 上午10:54:50      todo
 */
class AdShowAction extends Action{
	/**
	 * 广告展现
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-31 上午9:44:23
	 */
	public function index(){
		
		header("Content-type: text/html; charset=utf-8");
					
		// 获取提交过来的代码位信息
		$id = $_GET['zone'];
		
		$AdService = A('AdService');
		$code = $AdService->adShow($id);
	}
}