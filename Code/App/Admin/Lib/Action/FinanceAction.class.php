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
	//提现申请列表
    public function index(){
		$fa			= M("finance_apply");
		$uid		= (int)($_POST["uid"]);
		$status		= (int)($_GET["status"]);
		if(empty($status)){	// 按uid查询
			if(empty($uid)){
				$where  = "1";
			}else{ 
				$where  = "uid=".$uid;
			}
		}else{	// 按状态查询
			$where  = "status = ".$status;
		}
		$apply		= $fa->where($where)->select();
		foreach($apply as $key =>$value){
			$apply[$key]["apply_date"] = date("Y-m-d H:i:s",$value["apply_date"]);
			$apply[$key]["process_time"] = date("Y-m-d H:i:s",$value["process_time"])?0:0;
			$apply[$key]["pre_tax"] = $value["pre_tax"]?0:0;
			$apply[$key]["after_tax"] = $value["after_tax"]?0:0;
			$apply[$key]["fee"] = $value["fee"]?0:0;
			$apply[$key]["amount_payable"] = $value["amount_payable"]?0:0;
		}
		$this		->assign("apply",$apply);
    	$this		->display();
	}
	public function withdrawDo(){
		$id=(int)($_GET["id"]);
		
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
			if($_GET["status"]=="all"){
				$where = "1";
			}else{
				$where = "performance_status = ".$_GET["status"];
			}	
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
	//结算明细表操作_冻结
	public function incomeDo(){
		$id		= (int)($_GET["id"]);
		$data["frozen_status"]=0;
		$in 	= M("income");
		$in		->where("id=".$id)->data($data)->save(); //冻结成功，改成冻结状态
		$this	->success("冻结成功！","SITE_URL/?m=Finance&a=income");
	}
	//申请提现状态报表
	public function withdrawListRule(){
		$with					= M("withdraw_list_rule");
		$withdraw				= $with->where("id=1")->select();
		$this					->assign("withdraw",$withdraw);
		$this					->display();
	}
	//订制申请提现状态报表
	public function withdrawListRuleDo(){
		$data["uid"]			= (int)($_POST["uid"]);
		$data["apply_date"]		= (int)($_POST["timestmp"]);
		$data["pre_tax"]		= (int)($_POST["income_zong"]);
		$data["after_tax"]		= (int)($_POST["income_shuihou"]);
		$data["fee"]			= (int)($_POST["income_shouxufei"]);
		$data["amount_payable"]	= (int)($_POST["income_yingfu"]);
		$data["process_time"]	= (int)($_POST["pay_time"]);
		$data["status"]			= (int)($_POST["status"]);
		$with					= M("withdraw_list_rule");
		$withdraw				= $with->where("id=1")->data($data)->save(); //修改各字段的状态
		$this->success("订制申请提现报表成功","SITE_URL/?m=Finance&a=index");
	}
	// 导出申请提现报表
	public function withdrawExport(){
		//输出的文件类型为excel
		header("Content-type:application/vnd.ms-excel");
		//提示下载
		header("Content-Disposition:attachement;filename=提现申请列表_".date("Y-m-d").".xls");
		//查询提现申请表
		$finance_apply	= M("finance_apply");
		$finance 	  	= $finance_apply->select();
		//查询申请提现状态报表
		$with			= M("withdraw_list_rule");
		$withdraw		= $with->where("id=1")->select();
		$ReportArr	  	= array();
		//将关系数组转换成索引数组
		foreach($finance as $key =>$val){
			if($withdraw[0]["uid"]==0){
				$ReportArr[$key][]=$val["uid"]; //会员ID
			}
			if($withdraw[0]["apply_date"]==0){
				$ReportArr[$key][]=date("Y-m-d H:i:s",$val["apply_date"]); //申请提现时间
			}
			if($withdraw[0]["pre_tax"]==0){
				$ReportArr[$key][]=$val["pre_tax"]?0:0; //税前总收入
			}
			if($withdraw[0]["after_tax"]==0){
				$ReportArr[$key][]=$val["after_tax"]?0:0; //税后总收入
			}
			if($withdraw[0]["fee"]==0){
				$ReportArr[$key][]=$val["fee"]?0:0; //手续费
			}
			if($withdraw[0]["amount_payable"]==0){
				$ReportArr[$key][]=$val["amount_payable"]?0:0; //应付金额
			}
			if($withdraw[0]["process_time"]==0){
				$ReportArr[$key][]=date("Y-m-d H:i:s",$val["process_time"])?0:0; //操作时间
			}
			if($withdraw[0]["status"]==0){
				if($val["status"]==1){ // 支付状态
					$ReportArr[$key][]="未支付";
				}else if($val["status"]==2){
					$ReportArr[$key][]="已支付";
				}else{
					$ReportArr[$key][]="异常";
				}
			}
		}
		//报表数据
		$ReportContent = '';
		$num1 = count($ReportArr);
		for($i=0;$i<$num1;$i++){
			$num2 = count($ReportArr[$i]);
			for($j=0;$j<$num2;$j++){
				//ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
				$ReportContent .= '"'.$ReportArr[$i][$j].'"'."\t";
			}
			//最后连接\n 表示换行
			$ReportContent .= "\n";
		}
		//用的utf-8 最后转换一个编码为gb
		//$ReportContent = mb_convert_encoding($ReportContent,"gb2312","utf-8");
		//需要查询出来的字段信息
		if($withdraw[0]["uid"]==0){ //判断是否要导出会员ID信息
			$t[]="会员ID";
		}
		if($withdraw[0]["apply_date"]==0){ //判断是否要导出申请时间信息
			$t[]="申请时间";
		}
		if($withdraw[0]["pre_tax"]==0){ //判断是否要导出税前总收入信息
			$t[]="税前总收入(￥)";
		}
		if($withdraw[0]["after_tax"]==0){ //判断是否要导出税后总收入信息
			$t[]="税后总收入(￥)";
		}
		if($withdraw[0]["fee"]==0){ //判断是否要导出手续费信息
			$t[]="手续费(￥)";
		}
		if($withdraw[0]["amount_payable"]==0){ //判断是否要导出应付金额信息
			$t[]="应付金额(￥)";
		}
		if($withdraw[0]["process_time"]==0){ //判断是否要导出处理时间信息
			$t[]="处理时间";
		}
		if($withdraw[0]["status"]==0){ //判断是否要导出支付状态信息
			$t[]="支付状态";
		}
		for($k=0;$k<count($t);$k++){
			//ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
			$ReportTitle .= '"'.$t[$k].'"'."\t";
		}
		//输出即提示下载
		echo $ReportTitle."\n".$ReportContent;
	}
	// 订制结算明细报表
	public function reportIncomeListRule(){
		$report_income			= M("report_income_list_rule");
		$report					= $report_income->where("id=1")->select();
		$this					->assign("report",$report);
		$this					->display();
	}
	// 订制结算明细报表
	public function reportIncomeListRuleDo(){
		$data["pid"]			= (int)($_POST["plan_id"]);
		$data["uid"]			= (int)($_POST["uid"]);
		$data["settlement_time"]= (int)($_POST["times"]);
		$data["real_income"]	= (int)($_POST["income_real"]);
		$data["settlement_income"]= (int)($_POST["income"]);
		$data["ip"]				= (int)($_POST["count_ip"]);
		$data["pv"]				= (int)($_POST["count_pv"]);
		$data["cps"]			= (int)($_POST["count_cps"]);
		$data["cpa"]			= (int)($_POST["count_cpa"]);
		$data["performance_status"]= (int)($_POST["status"]);
		$data["settlement_status"]= (int)($_POST["pay_status"]);
		$data["status"]			= (int)($_POST["status"]);
		$report_income			= M("report_income_list_rule");
		$report					= $report_income->where("id=1")->data($data)->save(); //修改各字段的状态
		$this->success("订制结算明细报表成功","SITE_URL/?m=Finance&a=income");
	}
	// 导出结算明细报表
	public function incomeExport(){
		//输出的文件类型为excel
		header("Content-type:application/vnd.ms-excel");
		//提示下载
		header("Content-Disposition:attachement;filename=结算明细列表_".date("Y-m-d").".xls");
		//查询结算明细表
		$in				= M("income");
		$income 	  	= $in->select();
		//查询结算明细状态报表
		$report_income	= M("report_income_list_rule");
		$report			= $report_income->where("id=1")->select();
		$ReportArr	  	= array();
		//将关系数组转换成索引数组
		foreach($income as $key =>$val){
			if($report[0]["pid"]==0){
				$ReportArr[$key][]=$val["pid"]; //计划ID
			}
			if($report[0]["uid"]==0){
				$ReportArr[$key][]=$val["uid"]; //网站主ID
			}
			if($report[0]["settlement_time"]==0){
				$ReportArr[$key][]=date("Y-m-d H:i:s",$val["settlement_time"]); //结算日期
			}
			if($report[0]["real_income"]==0){
				$ReportArr[$key][]=$val["real_income"]?0:0; //真实收入
			}
			if($report[0]["settlement_income"]==0){
				$ReportArr[$key][]=$val["settlement_income"]?0:0; //结算收入
			}
			if($report[0]["ip"]==0){
				$ReportArr[$key][]=$val["ip"]?0:0; //IP
			}
			if($report[0]["pv"]==0){
				$ReportArr[$key][]=$val["pv"]?0:0; //PV
			}
			if($report[0]["click"]==0){
				$ReportArr[$key][]=$val["click"]?0:0; //CLICK
			}
			if($report[0]["cps"]==0){
				$ReportArr[$key][]=$val["cps"]?0:0; //CPS
			}
			if($report[0]["cpa"]==0){
				$ReportArr[$key][]=$val["cpa"]?0:0; //CPA
			}
			if($report[0]["performance_status"]==0){
				if($val["performance_status"]==1){ //业绩状态
					$ReportArr[$key][]="未确认";
				}else if($val["performance_status"]==2){
					$ReportArr[$key][]="已确认";
				}else{
					$ReportArr[$key][]="无效";
				}
			}
			if($report[0]["settlement_status"]==0){
				if($val["settlement_status"]==1){ //结算状态
					$ReportArr[$key][]="未支付";
				}else if($val["settlement_status"]==2){
					$ReportArr[$key][]="已支付";
				}else{
					$ReportArr[$key][]="异常";
				}
			}
		}
		//报表数据
		$ReportContent = '';
		$num1 = count($ReportArr);
		for($i=0;$i<$num1;$i++){
			$num2 = count($ReportArr[$i]);
			for($j=0;$j<$num2;$j++){
				//ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
				$ReportContent .= '"'.$ReportArr[$i][$j].'"'."\t";
			}
			//最后连接\n 表示换行
			$ReportContent .= "\n";
		}
		//用的utf-8 最后转换一个编码为gb
		//$ReportContent = mb_convert_encoding($ReportContent,"gb2312","utf-8");
		//需要查询出来的字段信息
		if($report[0]["pid"]==0){ //判断是否要导出计划ID信息
			$t[]="计划ID";
		}
		if($report[0]["uid"]==0){ //判断是否要导出网站主ID信息
			$t[]="网站主ID";
		}
		if($report[0]["settlement_time"]==0){ //判断是否要导出结算时间信息
			$t[]="结算时间";
		}
		if($report[0]["real_income"]==0){ //判断是否要导出真实收入信息
			$t[]="真实收入";
		}
		if($report[0]["settlement_income"]==0){ //判断是否要导出结算收入信息
			$t[]="结算收入";
		}
		if($report[0]["ip"]==0){ //判断是否要导出IP信息
			$t[]="IP";
		}
		if($report[0]["pv"]==0){ //判断是否要导出PV信息
			$t[]="PV";
		}
		if($report[0]["click"]==0){ //判断是否要导出CLICK信息
			$t[]="CLICK";
		}
		if($report[0]["cps"]==0){ //判断是否要导出CPS信息
			$t[]="CPS";
		}
		if($report[0]["cpa"]==0){ //判断是否要导出CPA信息
			$t[]="CPA";
		}
		if($report[0]["performance_status"]==0){ //判断是否要导出业绩状态信息
			$t[]="业绩状态";
		}
		if($report[0]["settlement_status"]==0){ //判断是否要导出支付状态信息
			$t[]="支付状态";
		}
		for($k=0;$k<count($t);$k++){
			//ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
			$ReportTitle .= '"'.$t[$k].'"'."\t";
		}
		//输出即提示下载
		echo $ReportTitle."\n".$ReportContent;
	}
}