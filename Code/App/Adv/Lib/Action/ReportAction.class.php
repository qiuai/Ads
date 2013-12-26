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
	 	$this->tenDaysBefore();
	 	
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
	 	$this->tenDaysBefore();
	 	
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
	public function tenDaysBefore(){
		// 获取计划列表
		$this->getPlan();
		
		// 会员报表
		if($_SESSION[C('WEB_AUTH_KEY')]){
			$where = " and i.uid = ". intval($_SESSION[C('WEB_AUTH_KEY')]);
		}
		
		$model = M('Income');
		
		// 搜索计划
		if($pid = intval($_REQUEST['plan_id'])){
			$where .= " and i.pid = $pid";
		}
		
		// 搜索日期
		if(intval($_REQUEST['start_date']) && intval($_REQUEST['end_date'])){
			$start_date = strtotime($_REQUEST['start_date']);
			$end_date = strtotime($_REQUEST['end_date']);
			
			$where .= " and i.start_date >= $start_date";
			$where .= " and i.end_date < $end_date";
			
			while($start_date <= $end_date && $start_date < strtotime(date('Ymd',time()))){
				$day = mktime(0,0,0,date("m",$start_date) ,date('d',$start_date)-1,date("Y",$start_date));
				$data = $model->query("select sum(click) as click, sum(pv) as pv, sum(cpm) as cpm, sum(cpc) as cpc, sum(real_income) as income, sum(ip) as ip from " . C('DB_PREFIX') . "income i where settlement_time < $start_date and settlement_time >= $day $where");
				if($data[0]['ip']){
					foreach($data[0] as $key=>$value){
						$data[0][$key] = $value ? $value : 0;
						$sum[$key] = intval($value) + intval($sum[$key]);
					}
					$data[0]['day'] = date('Ymd', $start_date);
						
					$list[] = $data[0];
				}
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
			for($i=6; $i>0; $i--){
				$day = mktime(0,0,0,date("m") ,(date('d',$today)-($i+1)),date("Y"));
				$yestoday = mktime(0,0,0,date("m",$today) ,(date('d',$today)-$i),date("Y"));
				$data = $model->query("select sum(click) as click, sum(pv) as pv, sum(cpm) as cpm, sum(cpc) as cpc, sum(real_income) as income, count(ip) as ip from " . C('DB_PREFIX') . "income i where settlement_time < $yestoday and settlement_time >= $day $where");
					
				foreach($data[0] as $key=>$value){
					$data[0][$key] = $value ? $value : 0;
					$sum[$key] = intval($value) + intval($sum[$key]);
				}
				$data[0]['day'] = date('Ymd', $yestoday);
					
				$list[] = $data[0];
			}
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
	/**
	 * 获取广告计划列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-26 下午3:46:49
	 */
	public function getPlan(){
		$model = M('ad_plan');
		$where['uid'] = $_SESSION[C('ADV_AUTH_KEY')];
		$data = $model->where($where)->select();
		
		$this->assign('planList',$data);
	}
}