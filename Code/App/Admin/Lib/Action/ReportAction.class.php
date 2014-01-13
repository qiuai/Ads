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
		$yestoday = mktime(0,0,0,date("m") ,date('d')-1,date("Y"));
		$where = " z. visit_time > $yestoday ";
		$this->getPlanData($where);
		$this->display();
	}
	/**
	 * 独立IP明细
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-27 上午10:30:15
	 */
	public function uniqueDetailReport(){
		$order = " order by z.visit_ip desc";
		$this->getPlanData();
		$this->display('todayDetailReport');
	}
	/**
	 * 历史明细
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-27 上午10:30:15
	 */
	public function historyDetailReport(){
		$yestoday = mktime(0,0,0,date("m") ,date('d')-1,date("Y"));
		$where = " z. visit_time <= $yestoday ";
		$this->getPlanData();	
		$this->display('todayDetailReport');
	}
	/**
	 * 计划选择
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-27 下午3:12:00
	 */
	public function choosePlan(){
		if($_GET['search_type'] == 'plan_id'){
			$_GET['id'] = intval($_GET['keyword']);
				
		}elseif($_GET['search_type'] == 'plan_name'){
			$_GET['plan_name'] =  strip_tags($_GET['keyword']);
			$_GET['plan_name'] = array("like","%".$_GET['plan_name']."%");
		}
		$this->assign("action_names",$_GET['aa']);
		R('AdPlan/search', array(30));
	}
	/**
	 * 获取计划明细数据
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-27 下午3:16:17
	 */
	public function getPlanData($where='', $order=''){
		if(!$_GET['plan_id'] && !$_SESSION['pid']){
// 			$this->assign('msgTitle',"提示信息");
			$this->error("请选择要查看的计划！",C('SITE_URL') . "?m=Report&a=choosePlan&aa=" . ACTION_NAME);
			exit;
		}else{
			$_SESSION['pid'] = $_GET['plan_id'] ? $_GET['plan_id'] : $_SESSION['pid'];
			$_SESSION['pname'] = $_GET['plan_name'] ? $_GET['plan_name'] : $_SESSION['pname'];
		}
		
		// 查询条件
		$where = " where z.pid = " .$_SESSION['pid'] . " $where ";
		
		if(!$order){
			// 排序	结算时间降序
			$order = " order by z.visit_time desc ";
		}
		
		
		// 分页
		$p = $_GET['p'] ? $_GET['p'] : 1;
		$num = 10;
		$limit = " limit ". ($p-1)*$num .",".$num;
		
		$sql = "select * from " . C('DB_PREFIX') . "ad_plan p join " . C('DB_PREFIX') . "zone_visit z on p.id = z.pid $where $order $limit";
		$count = "select count(z.id) as num from " . C('DB_PREFIX') . "ad_plan p join " . C('DB_PREFIX') . "zone_visit z on p.id = z.pid $where limit 1";
		
		$this->pageList($sql, $count, $num);
		
		$this->assign('plan_name',$_SESSION['pname']);
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
			$where = " and p.uid = ". intval($uid);
		}
	
		// 查询中心 当日时间或者选择时间
		$today = ($_REQUEST['day'] <= date('d') && $_REQUEST['day'] > 0 ) ? $_REQUEST['day'] : date('d');
	
		// 获取数据
		$model = M();
		for($i=9; $i>=0; $i--){
			$day = mktime(0,0,0,date("m") ,$today-($i+1),date("Y"));
			$yestoday = mktime(0,0,0,date("m") ,$today-($i+0),date("Y"));
			$data = $model->query("select zv.click_ip_num as click, zv.view_pv_num as pv, p.pay_type, p.price, p.site_master_display_price, p.site_master_pay_price, zv.view_pv_num as ip from " . C('DB_PREFIX') . "zone_visit_count zv join " . C('DB_PREFIX') . "zone z on z.id = zv.zid join " . C('DB_PREFIX') . "ad_plan p on p.id = zv.pid where zv.day_start_time < $yestoday and zv.day_start_time >= $day $where");
			
			$report = array();
			$report['day'] = date('md', $day);
			
			if($data){
			    foreach($data as $k=>$v){
			        $report['income'] = ($data[$k]['price'] - $data[$k]['site_master_pay_price']) * $data[$k]['ip'] + $report['income'];	// 联盟收入
			        $report['click'] = $v['click'] + $report['click'];
			        $report['pv'] = $v['pv'] + $report['pv'];
			        $report['ip'] = $v['ip'] + $report['ip'];
			        if($v['pay_type'] == 1){	// CPM
			            $report['cpm'] = $v['pv'] + $report['cpm'];
			        }else if($v['pay_type'] == 2){	// CPC
			            $report['cpc'] = $v['pv'] + $report['cpc'];
			        }
			    }
			}else{
			    $report['income'] = 0;	// 预计收入
			    $report['click'] = 0;
			    $report['pv'] = 0;
			    $report['ip'] = 0;
			    $report['cpm'] = 0;
			    $report['cpc'] = 0;
			}
			$list[] = $report;
		}
	
		$json = json_encode($list);
	
		$this->assign("chartData", $json);
	
	}
}