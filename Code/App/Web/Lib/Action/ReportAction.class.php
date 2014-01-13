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
    	$this->tenDaysBefore();
    	
		$this->assign("location","综合报表");
		$this->display();
    }
    /**
     * CPC报表
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-25 下午4:02:36
     */
	 public function cpcReport(){ 
	 	// 生成报表
	 	$this->tenDaysBefore(2);
	 	
		$this->assign("location","CPC明细报表");
		$this->display('index');
    }
    /**
     * CPM报表
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-25 下午4:02:46
     */
	 public function cpmReport(){ 
	 	// 生成报表
	 	$this->tenDaysBefore(1);
	 	
		$this->assign("location","CPM明细报表");
		$this->display('index');
    }
	/**
	 * 最近十天数据
	 * 
	 * cpm cpc
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-20 下午1:45:06
	 */
	public function tenDaysBefore($pay_type=0){
		// 会员报表
		if($_SESSION[C('WEB_AUTH_KEY')]){
			$where = " and z.uid = ". intval($_SESSION[C('WEB_AUTH_KEY')]);
		}else{
			return;
		}
		
		$model = M();
		
		// 计费方式
		if($pay_type){
		    $where .= " and p.pay_type = $pay_type ";
		}
		
		// 搜索代码位
		if(intval($_REQUEST['zone_id'])){
			$where .= " and zv.zid = " . intval($_REQUEST['zone_id']);
		}
		
		// 搜索日期
		if(intval($_REQUEST['start_date']) && intval($_REQUEST['end_date'])){
			$start_date = strtotime($_REQUEST['start_date']);
			$end_date = strtotime($_REQUEST['end_date']);
			
			$where .= " and zv.day_start_time >= $start_date";
			$where .= " and zv.day_start_time < $end_date";
			
			while($start_date <= $end_date && $start_date < strtotime(date('Ymd',time()))){
				$day = mktime(0,0,0,date("m",$start_date) ,date('d',$start_date)-1,date("Y",$start_date));
				$data = $model->query("select zv.click_ip_num as click, zv.view_pv_num as pv, p.site_master_pay_price as price, zv.view_pv_num as ip from " . C('DB_PREFIX') . "zone_visit_count zv join " . C('DB_PREFIX') . "zone z on z.id = zv.zid join " . C('DB_PREFIX') . "ad_plan p on p.id = zv.pid where zv.day_start_time < $start_date and zv.day_start_time >= $day $where");
				
				$report = array();
				$report['day'] = date('Ymd', $start_date);
				
				if($data){
				    foreach($data as $k=>$v){
				        $report['income'] = $data[$k]['price'] * $data[$k]['ip'] + $report['income'];	// 预计收入
				        $report['click'] = $v['click'] + $report['click'];
				        $report['pv'] = $v['pv'] + $report['pv'];
				        $report['ip'] = $v['ip'] + $report['ip'];
				    }
				}else{
				    $report['income'] = 0;	// 预计收入
				    $report['click'] = 0;
				    $report['pv'] = 0;
				    $report['ip'] = 0;
				}
				$list[] = $report;
				
				$start_date = mktime(0,0,0,date("m",$start_date) ,date('d',$start_date)+1,date("Y",$start_date));
			}
			
			// 总计
			$sum['day'] = '合计';
			
			$json = json_encode($list);
			
			$this->assign("start_date", $_REQUEST['start_date']);
			$this->assign("end_date", $_REQUEST['end_date']);
			$this->assign("chartData", $json);
			$this->assign("list", $list);
			$this->assign("sum", $sum);
		}else{
			// 查询中心 当日时间或者选择时间
			$today = time();
			
			// 获取数据
			for($i=5; $i>=0; $i--){
				$day = mktime(0,0,0,date("m") ,(date('d',$today)-($i+1)),date("Y"));
				$yestoday = mktime(0,0,0,date("m",$today) ,(date('d',$today)-$i),date("Y"));
				$data = $model->query("select zv.click_ip_num as click, zv.view_pv_num as pv, p.site_master_pay_price as price, zv.view_pv_num as ip from " . C('DB_PREFIX') . "zone_visit_count zv join " . C('DB_PREFIX') . "zone z on z.id = zv.zid join " . C('DB_PREFIX') . "ad_plan p on p.id = zv.pid where zv.day_start_time < $yestoday and zv.day_start_time >= $day $where");

				$report = array();
				$report['day'] = date('Ymd', $day);
				
				if($data){
				    foreach($data as $k=>$v){
				        $report['income'] = $data[$k]['price'] * $data[$k]['ip'] + $report['income'];	// 预计收入
				        $report['click'] = $v['click'] + $report['click'];
				        $report['pv'] = $v['pv'] + $report['pv'];
				        $report['ip'] = $v['ip'] + $report['ip'];
				    }
				}else{
				    $report['income'] = 0;	// 预计收入
				    $report['click'] = 0;
				    $report['pv'] = 0;
				    $report['ip'] = 0;
				}
				$list[] = $report;
			}
		}
		
		foreach ($list as $key=>$value){
		    $sum['income'] = $value['income'] + $sum['income'];
		    $sum['click'] = $value['click'] + $sum['click'];
		    $sum['pv'] = $value['pv'] + $sum['pv'];
		    $sum['ip'] = $value['ip'] + $sum['ip'];
		}
		 
		// 总计
		$sum['day'] = '合计';
		
		$json = json_encode($list);
	
		$this->assign("start_date", $_REQUEST['start_date']);
		$this->assign("end_date", $_REQUEST['end_date']);
		$this->assign("chartData", $json);
		$this->assign("list", $list);
		$this->assign("sum", $sum);
	}
}