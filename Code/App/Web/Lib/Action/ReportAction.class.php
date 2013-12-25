<?php
/**
 * 广告联盟系统  报表管理
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午10:07:57
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午10:07:57      todo
 */
class ReportAction extends CommonAction {
	/**
	 * 综合报表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-25 下午4:02:28
	 */
    public function index(){ 
    	// 生成报表
    	$this->tenDaysBefore($_SESSION[C('WEB_AUTH_KEY')]);
    	
		$this->assign("title","综合报表");
		$this->display();
    }
    /**
     * CPC报表
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-25 下午4:02:36
     */
	 public function cpcReport(){ 
		$this->assign("title","CPC明细报表");
		$this->display();
    }
    /**
     * CPM报表
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-25 下午4:02:46
     */
	 public function cpmReport(){ 
		$this->assign("title","CPM明细报表");
		$this->display();
    }
/**
	 * 最近十天数据
	 * 
	 * cpm cpc
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-20 下午1:45:06
	 */
	public function tenDaysBefore($uid){
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