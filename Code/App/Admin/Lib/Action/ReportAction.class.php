<?php
/**
 * 广告联盟系统 报表管理
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
			$where = " where i.uid = " . intval($uid);
		}else{
			$where = " where i.uid != " . intval($uid);
		}
		
		// 搜索日期
		if(intval($_REQUEST['start_date']) && intval($_REQUEST['end_date'])){
			$where .= " and i.start_date >= " . strtotime(intval($_REQUEST['start_date']));
			$where .= " and i.end_date < " . strtotime(intval($_REQUEST['end_date']));
		}
		
		// 列表数据
		$this->getReportData($where);
		
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
			$where = " where pid = " . intval($pid);
		}else{
			$where = " where pid != " . intval($pid);
		}
	
		// 搜索日期
		if(intval($_REQUEST['start_date']) && intval($_REQUEST['end_date'])){
			$where .= " and i.start_date >= " . strtotime(intval($_REQUEST['start_date']));
			$where .= " and i.end_date < " . strtotime(intval($_REQUEST['end_date']));
		}
	
		// 列表数据
		$this->getReportData($where);
		
		$this->assign('pid', $pid);
		$this->assign('start_date', $_REQUEST['start_date']);
		$this->assign('end_date', $_REQUEST['end_date']);
		$this->display();
	}
	/**
	 * CPM今日订单
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-25 上午10:49:43
	 */
	public function cpmTodayOrder(){
		$this->display();
	}
	/**
	 * 今日明细
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-27 上午10:30:15
	 */
	public function todayDetailReport(){
		$this->display();
	}
	/**
	 * 独立IP明细
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-27 上午10:30:15
	 */
	public function uniqueDetailReport(){
		$this->display();
	}
	/**
	 * 历史明细
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-27 上午10:30:15
	 */
	public function historyDetailReport(){
		$this->display();
	}
	/**
	 * 获取报表数据
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-25 上午10:24:03
	 */
	public function getReportData($where, $num=10, $order=''){
		// 排序	结算时间降序
		$order = " order by i.settlement_time desc ";
		
		// 分页
		$p = $_GET['p'] ? $_GET['p'] : 1;
		$limit = " limit ". ($p-1)*$num .",".$num;
		
		$sql = "select * from " . C('DB_PREFIX') . "ad_plan p join " . C('DB_PREFIX') . "income i on p.id = i.pid join " . C('DB_PREFIX') . "ad_plan_category c on p.category_id = c.id $where $order $limit";
		$count = "select count(i.id) as num from " . C('DB_PREFIX') . "ad_plan p join " . C('DB_PREFIX') . "income i on p.id = i.pid $where limit 1";
		
		$this->pageList($sql, $count, $num);
	}
	/**
	 * 最近十天数据
	 *
	 * cpm cpc
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-20 下午1:45:06
	 */
	public function tenDaysBefore($uid=''){
		// 会员报表
		if($uid){
			$where = " and uid = ". intval($uid);
		}
	
		// 查询中心 当日时间或者选择时间
		$today = ($_REQUEST['day'] <= date('d') && $_REQUEST['day'] > 0 ) ? $_REQUEST['day'] : date('d');
	
		// 获取数据
		$model = M('Income');
		for($i=9; $i>=0; $i--){
			$day = mktime(0,0,0,date("m") ,$today-($i+1),date("Y"));
			$yestoday = mktime(0,0,0,date("m") ,$today-$i,date("Y"));
			$data = $model->query("select sum(click) as click, sum(pv) as pv, sum(cpm) as cpm, sum(cpc) as cpc, sum(real_income) as income, count(ip) as ip from " . C('DB_PREFIX') . "income where settlement_time < $yestoday and settlement_time >= $day $where");
			foreach($data[0] as $key=>$value){
				$data[0][$key] = $value ? $value : 0;
			}
			$data[0]['day'] = date('md', $yestoday);
			$list[] = $data[0];
		}
	
		$json = json_encode($list);
	
		$this->assign("chartData", $json);
	
	}
}