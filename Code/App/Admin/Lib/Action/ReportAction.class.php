<?php
/**
 * 广告联盟系统  会员管理
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
class ReportAction extends CommonAction {
	/**
	 * 网站主报表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-21 上午11:10:00
	 */
	public function webReport(){
		// 网站主UID
		$uid = $_REQUEST['uid'] ? $_REQUEST['uid'] : '';
		
		// 搜索UID
		if($uid){
			$where['uid'] = intval($uid);
		}
		
		// 搜索日期
		if(intval($_REQUEST['start_date']) && intval($_REQUEST['end_date'])){
			$where['start_date'] = array('gt', strtotime(intval($_REQUEST['start_date'])));
			$where['end_date'] = array('lt', strtotime(intval($_REQUEST['end_date'])));
		}
		
		// 列表数据
		$model = M('Income');
		
		// 结算时间降序
		$income = $this->memberPage($model, $where, 10, 'settlement_time desc');
// 		$income = $model->where($where)->find();
// 		var_dump($income);
		$this->assign('uid', $uid);
		$this->assign('start_date', $_REQUEST['start_date']);
		$this->assign('end_date', $_REQUEST['end_date']);
		$this->display();
	}
	/**
	 * 计划报表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-23 下午9:31:09
	 */
	public function planReport(){
		// 计划PID
		$pid = $_REQUEST['pid'] ? $_REQUEST['pid'] : '';
	
		// 搜索PID
		if($pid){
			$where['pid'] = intval($pid);
		}
	
		// 搜索日期
		if(intval($_REQUEST['start_date']) && intval($_REQUEST['end_date'])){
			$where['start_date'] = array('gt', strtotime(intval($_REQUEST['start_date'])));
			$where['end_date'] = array('lt', strtotime(intval($_REQUEST['end_date'])));
		}
	
		// 列表数据
		$model = M('Income');
	
		// 结算时间降序
		$income = $this->memberPage($model, $where, 10, 'settlement_time desc');
// 		echo $model->getLastSql();
// 				var_dump($income);
		$this->assign('pid', $pid);
		$this->assign('start_date', $_REQUEST['start_date']);
		$this->assign('end_date', $_REQUEST['end_date']);
		$this->display();
	}
}