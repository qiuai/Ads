<?php
/**
 * 广告联盟系统  财务管理
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
class FinanceAction extends CommonAction {
	// 提现申请列表
    public function index(){
		// 创建提现申请列表对象
		$fa			= M("finance_apply");
		$uid		= (int)($_POST["uid"]);
		$status		= (int)($_GET["status"]);
		if(empty($status)){	// 按uid查询
			if(empty($uid)){
				$where  = "1";
			}else{ 
				$where  = "id=".$uid;
			}
		}else{	// 按状态查询
			$where  = "status = ".$status;
		}
		$apply		= $fa->where($where)->select();
		foreach($apply as $key =>$value){
			$apply[$key]["apply_date"] = date("Y-m-d H:i:s",$value["apply_date"]);
			$apply[$key]["process_time"] = date("Y-m-d H:i:s",$value["process_time"]);
		}
		$this		->assign("apply",$apply);
    	$this		->display();
	}
	// 结算明细
	public function income(){
		$start_date = $_POST["start_date"]; // 开始日期
		$end_date = $_POST["end_date"]; // 结束日期
		$plan_id = $_POST["plan_id"]; // 计划ID
		$uid = $_POST["uid"]; // 网站主ID
		// 转化成时间戳
		if(empty($start_date)){
			$start  = time()-86400*4;
		}else{
			$start	= strtotime($start_date);
		}
		if(empty($end_date)){
			$end  	= time()+86400;
		}else{
			$end	= strtotime($end_date);
		}
		// 查询条件
		if(empty($_GET["status"])){ // 按时间，id查询
			if($start<=$end){
				$end +=86400; // 计算到一天结束为止
				$where = "start_date >=".$start." and end_date<=".$end;
			}else{
				$this->error("请正确选择日期！","SITE_URL/?m=Finance&a=income");
			}
			if(!empty($plan_id)){
				$where = $where." and pid =".$plan_id; 
			}
			if(!empty($uid)){
				$where = $where." and uid =".$uid; 
			}
		}else{ // 按业绩状态查询
			$where = "performance_status = ".$_GET["status"];
		}
		$in		= M("income");
		$income = $in->where($where)->select();
		// 数据处理
		foreach($income as $key =>$val){
			$income[$key]["settlement_time"] = date("Y-m-d",$val["settlement_time"]);
			switch($val["performance_status"]){
				case 1:
					$income[$key]["performance_status"] = "未确认";		
				break;
				case 2:
					$income[$key]["performance_status"] = "已确认";
				break;
				case 3:
					$income[$key]["performance_status"] = "无效";
				break;
				default:
				break;
			}
			switch($val["settlement_status"]){
				case 1:
					$income[$key]["settlement_status"] = "未支付";		
				break;
				case 2:
					$income[$key]["settlement_status"] = "已支付";
				break;
				case 3:
					$income[$key]["settlement_status"] = "异常";
				break;
				default:
				break;
			}
		}
		// 开始日期,默认前三天
		if(empty($start_date)){
			$this->assign("start_date",date("Y-m-d",strtotime("-3 day")));
		}else{
			$this->assign("start_date",$start_date);
		}
		// 结束日期,默认当天
		if(empty($end_date)){
			$this->assign("end_date",date("Y-m-d"));
		}else{
			$this->assign("end_date",$end_date);
		}
		$this->assign("income",$income);
		$this->display();
	}
	public function incomeDo(){
	
	}
}