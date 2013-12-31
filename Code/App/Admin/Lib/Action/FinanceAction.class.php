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
		$fa						= M("finance_apply");
		$uid					= $_POST["uid"];
		$status					= (int)($_GET["status"]);
		if(empty($status)){	// 按uid查询
			if(empty($uid)){
				$where			= "1";
			}else{ 
				$where			= "uid=".$uid;
			}
		}else{	// 按状态查询
			$where				= "status = ".$status;
		}
		$apply					= $this->memberPage($fa, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$wb						= M("web_balance");
		foreach($apply as $key =>$value){
			$apply[$key]["apply_date"] = date("Y-m-d H:i:s",$value["apply_date"]); // 申请时间
			if($value["process_time"]==0){	
				$apply[$key]["process_time"] = "-"; // 处理时间
			}else{
				$apply[$key]["process_time"] = date("Y-m-d H:i:s",$value["process_time"]); // 处理时间
			}
			if($value["paid_time"]==0){
				$apply[$key]["paid_time"] = "-"; // 支付时间
			}else{
				$apply[$key]["paid_time"] = date("Y-m-d H:i:s",$value["paid_time"]); // 支付时间
			}
			$web_blance			= $wb->where("uid =".$value["uid"])->select();
			foreach($web_blance as $keys=>$val){
				$apply[$key]["total_balance"] = $val["total_balance"]; // 税前总收入
			}
		}
		$this					->assign("apply",$apply);
		$this					->assign("uid",$uid);
		$this					->assign("status",$status);
    	$this					->display();
	}
	// 处理申请提现
	public function withdraw(){
		$bank_short				= C("BANK_SHORT");
		$uid					= (int)($_GET["uid"]); // 会员编号
		$id						= (int)($_GET["id"]); // 提现编号
		$member_detail			= M("member_detail");
		$member					= $member_detail->where("uid=".$uid)->select();
		$this					->assign("member",$member);
		$this					->assign("bank_type",$bank_short);
		$this					->assign("id",$id);
		$this					->assign("uid",$uid);
		$this					->display();
	}
	// 处理申请提现
	public function withdrawDo(){
		$uid					= (int)($_POST["uid"]); // 会员编号
		$id						= (int)($_POST["id"]); // 提现编号
		$data["status"]			= (int)($_POST["status"]); // 支付状态:2成功->已支付，3失败->异常
		$data["pay_channel"]	= $_POST["pay_channel"]; // 支付银行
		$data["pay_record"]		= $_POST["pay_record"]; // 交易单号
		$data["pay_remark"]		= $_POST["pay_remark"]; // 交易备注
		$data["process_time"]	= time(); // 处理时间
		$data["paid_time"]		= time(); // 支付时间
		$finance_apply 			= M("finance_apply");
		$web_balance 			= M("web_balance");
		if(empty($data["pay_channel"])){
			$this				->error("请选择支付银行！","SITE_URL?m=Finance&a=withdraw&id=".$id."&uid=".$uid);	
		}elseif(empty($data["pay_record"])){
			$this				->error("交易单号不能为空！","SITE_URL?m=Finance&a=withdraw&id=".$id."&uid=".$uid);
		}elseif(empty($data["pay_remark"])){
			$this				->error("交易备注不能为空！","SITE_URL?m=Finance&a=withdraw&id=".$id."&uid=".$uid);
		}else{
			$finance			= $finance_apply->where("id =".$id)->select();
			$data["amount_payable"]	= $finance[0]["withdraw_balance"]-$finance[0]["deductible_amount"]-$finance[0]["fee"];
			$finance_apply		->where("id=".$id)->data($data)->save();
			$web_balance		->where("uid=".$uid)->setInc("settlement_balance",$finance[0]["withdraw_balance"]);
			$web_balance		->where("uid=".$uid)->setDec("total_balance",$finance[0]["withdraw_balance"]);
			$this				->success("提现处理成功！","SITE_URL?m=Finance&a=index");
		}
	}
	// 申请提现状态报表
	public function withdrawListRule(){
		$with					= M("withdraw_list_rule");
		$withdraw				= $with->where("id=1")->select(); // 该表只有一条数据，只用来修改
		$this					->assign("withdraw",$withdraw);
		$this					->display();
	}
	// 订制申请提现状态报表
	public function withdrawListRuleDo(){
		$data["uid"]			= (int)($_POST["uid"]);	// 网站主ID（显隐）
		$data["apply_date"]		= (int)($_POST["timestmp"]); // 申请时间（显隐）
		$data["pre_tax"]		= (int)($_POST["income_zong"]); // 税前收入（显隐）
		$data["after_tax"]		= (int)($_POST["income_shuihou"]); // 税后收入（显隐）
		$data["fee"]			= (int)($_POST["income_shouxufei"]); // 手续费（显隐）
		$data["amount_payable"]	= (int)($_POST["income_yingfu"]); // 应付金额（显隐）
		$data["process_time"]	= (int)($_POST["pay_time"]); // 处理时间（显隐）
		$data["status"]			= (int)($_POST["status"]); // 支付状态（显隐）
		$with					= M("withdraw_list_rule");
		$withdraw				= $with->where("id=1")->data($data)->save(); // 修改各字段的状态
		$this					->success("订制申请提现报表成功","SITE_URL?m=Finance&a=index");
	}
	// 导出申请提现报表
	public function withdrawExport(){
		$filename		= "提现申请列表_".date("Y-m-d");
		$finance_apply	= M("finance_apply");
		$uid			= (int)($_GET["uid"]); // 网站主id
		$status			= (int)($_GET["status"]); // 支付状态
		if(empty($status)){	// 按uid查询
			if(empty($uid)){
				$where	= "1";
			}else{ 
				$where	= "uid=".$uid;
			}
		}else{	// 按状态查询
			$where		= "status = ".$status;
		}
		$finance 	  	= $finance_apply->where($where)->select();
		// 查询申请提现状态报表
		$with			= M("withdraw_list_rule");
		$withdraw		= $with->where("id=1")->select();
		$pay_status		= C("PAY_STATUS"); // 获取支付状态
		$ReportArr	  	= array();
		// 将关系数组转换成索引数组
		foreach($finance as $key =>$val){
			if($withdraw[0]["uid"]==0){
				$ReportArr[$key][]=$val["uid"]; // 会员ID
			}
			if($withdraw[0]["apply_date"]==0){
				$ReportArr[$key][]=date("Y-m-d H:i:s",$val["apply_date"]); // 申请提现时间
			}
			if($withdraw[0]["pre_tax"]==0){
				$ReportArr[$key][]=$val["pre_tax"]?0:0; // 税前总收入
			}
			if($withdraw[0]["after_tax"]==0){
				$ReportArr[$key][]=$val["after_tax"]?0:0; // 税后总收入
			}
			if($withdraw[0]["fee"]==0){
				$ReportArr[$key][]=$val["fee"]?0:0; // 手续费
			}
			if($withdraw[0]["amount_payable"]==0){
				$ReportArr[$key][]=$val["amount_payable"]?0:0; // 应付金额
			}
			if($withdraw[0]["process_time"]==0){
				$ReportArr[$key][]=date("Y-m-d H:i:s",$val["process_time"])?0:0; // 操作时间
			}
			if($withdraw[0]["status"]==0){
				$ReportArr[$key][]=$pay_status[$val["status"]]; // 支付状态
			}
		}
		// 需要查询出来的字段信息
		$HeaderArr		= array(); // 组建Excel表头数组
		if($withdraw[0]["uid"]==0){ // 判断是否要导出会员ID信息
			$HeaderArr[]="会员ID";
		}
		if($withdraw[0]["apply_date"]==0){ // 判断是否要导出申请时间信息
			$HeaderArr[]="申请时间";
		}
		if($withdraw[0]["pre_tax"]==0){ // 判断是否要导出税前总收入信息
			$HeaderArr[]="税前总收入(￥)";
		}
		if($withdraw[0]["after_tax"]==0){ // 判断是否要导出税后总收入信息
			$HeaderArr[]="税后总收入(￥)";
		}
		if($withdraw[0]["fee"]==0){ // 判断是否要导出手续费信息
			$HeaderArr[]="手续费(￥)";
		}
		if($withdraw[0]["amount_payable"]==0){ // 判断是否要导出应付金额信息
			$HeaderArr[]="应付金额(￥)";
		}
		if($withdraw[0]["process_time"]==0){ // 判断是否要导出处理时间信息
			$HeaderArr[]="处理时间";
		}
		if($withdraw[0]["status"]==0){ // 判断是否要导出支付状态信息
			$HeaderArr[]="支付状态";
		}
		// 下载Excel报表，输出即提示下载
		$this->downloadExcel($filename,$ReportArr,$HeaderArr);
	}
	// 结算明细
	public function income(){
		$start_date		= $_POST["start_date"]; // 开始日期
		$end_date		= $_POST["end_date"]; // 结束日期
		$plan_id		= $_POST["plan_id"]; // 计划ID
		$uid			= $_POST["uid"]; // 网站主ID
		// 转化成时间戳
		$start			= strtotime($start_date);
		$end			= strtotime($end_date);
		// 查询条件
		if(empty($_GET["status"])){ // 按时间，id查询
			if(empty($start)&&empty($end)){
				$where	= 1; // 默认
			}else if(empty($start)&&!empty($end)){
				$where	= "settlement_time <= ".$end; // 早于结束时间
			}else if(!empty($start)&&empty($end)){
				$where	= "settlement_time >= ".$start; // 晚于开始时间
			}else{
				$where	= "settlement_time >= ".$start." and settlement_time <= ".$end; // 介于开始结束时间之间
			}
			if(!empty($plan_id)){
				$where  = $where." and pid =".$plan_id; 
			}
			if(!empty($uid)){
				$where	= $where." and uid =".$uid; 
			}
		}else{ // 按业绩状态查询
			if($_GET["status"]=="all"){
				$where	= "1";
			}else{
				$where	= "performance_status = ".$_GET["status"];
			}	
		}
		$in				= M("income");
		$income			= $this->memberPage($in, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$performance	= C("PERFORMANCE_STATUS"); // 获取业绩状态
		$pay_status		= C("PAY_STATUS"); // 获取支付状态
		foreach($income as $key =>$val){
			$income[$key]["settlement_time"]	= date("Y-m-d",$val["settlement_time"]); // 结算时间
			$income[$key]["performance_status"]	= $performance[$val["performance_status"]]; // 业绩状态
			$income[$key]["settlement_status"]	= $pay_status[$val["settlement_status"]]; // 支付状态
		}
		// 开始日期,默认前三天
		if(empty($start_date)){
			$this		->assign("start_date",date("Y-m-d",strtotime("-3 day")));
		}else{
			$this		->assign("start_date",$start_date);
		}
		// 结束日期,默认当天
		if(empty($end_date)){
			$this		->assign("end_date",date("Y-m-d"));
		}else{
			$this		->assign("end_date",$end_date);
		}
		$this			->assign("income",$income);
		$this			->assign("plan_id",$plan_id);
		$this			->assign("uid",$uid);
		$this			->assign("status",$_GET["status"]);
		$this			->display();
	}
	// 结算明细表操作_冻结
	public function incomeDo(){
		$id				= (int)($_GET["id"]);
		$data["frozen_status"]=0;
		$in 			= M("income");
		$in				->where("id=".$id)->data($data)->save(); // 冻结成功，改成冻结状态
		$this			->success("冻结成功！","SITE_URL?m=Finance&a=income");
	}
	// 订制结算明细报表
	public function reportIncomeListRule(){
		$report_income			= M("report_income_list_rule");
		$report					= $report_income->where("id=1")->select(); // 该标只有一条记录，只能用于编辑（所有状态0显示1隐藏）
		$this					->assign("report",$report);
		$this					->display();
	}
	// 处理订制结算明细报表
	public function reportIncomeListRuleDo(){
		$data["pid"]			= (int)($_POST["plan_id"]); // 计划ID（显隐）
		$data["uid"]			= (int)($_POST["uid"]); // 网站主ID（显隐）
		$data["settlement_time"]= (int)($_POST["times"]); // 结算时间（显隐）
		$data["real_income"]	= (int)($_POST["income_real"]); // 真实收入（显隐）
		$data["settlement_income"]= (int)($_POST["income"]); // 结算收入（显隐）
		$data["ip"]				= (int)($_POST["count_ip"]); // IP访问量（显隐）
		$data["pv"]				= (int)($_POST["count_pv"]); // PV量（显隐）
		$data["click"]			= (int)($_POST["count_click"]); // CLICK点击量（显隐）
		$data["cps"]			= (int)($_POST["count_cps"]); // CPS（显隐）
		$data["cpa"]			= (int)($_POST["count_cpa"]); // CPA（显隐）
		$data["performance_status"]= (int)($_POST["status"]); // 业绩状态（显隐）
		$data["settlement_status"]= (int)($_POST["pay_status"]); // 支付状态（显隐）
		$report_income			= M("report_income_list_rule");
		$report					= $report_income->where("id=1")->data($data)->save(); // 修改各字段的状态
		$this					->success("订制结算明细报表成功","SITE_URL?m=Finance&a=income");
	}
	// 导出结算明细报表
	public function incomeExport(){
		$filename				= "结算明细列表_".date("Y-m-d");
		$start_date				= $_GET["start_date"]; // 开始日期
		$end_date				= $_GET["end_date"]; // 结束日期
		$plan_id				= $_GET["plan_id"]; // 计划ID
		$uid					= $_GET["uid"]; // 网站主ID
		// 转化成时间戳
		$start					= strtotime($start_date);
		$end					= strtotime($end_date);
		$in						= M("income");
		// 查询条件
		if(empty($_GET["status"])){ // 按时间，id查询
			if(empty($start)&&empty($end)){
				$where			= 1; // 默认
			}else if(empty($start)&&!empty($end)){
				$where			= "settlement_time <= ".$end; // 早于结束时间
			}else if(!empty($start)&&empty($end)){
				$where			= "settlement_time >= ".$start; // 晚于开始时间
			}else{
				$where			= "settlement_time >= ".$start." and settlement_time <= ".$end; // 介于开始结束时间之间
			}
			if(!empty($plan_id)){
				$where			= $where." and pid =".$plan_id; // 计划ID
			}
			if(!empty($uid)){
				$where			= $where." and uid =".$uid; // 网站主ID
			}
		}else{ // 按业绩状态查询
			if($_GET["status"]=="all"){
				$where			= "1";
			}else{
				$where			= "performance_status = ".$_GET["status"];
			}	
		}
		$income 	  			= $in->where($where)->select();
		// 查询结算明细状态报表
		$report_income			= M("report_income_list_rule");
		$report					= $report_income->where("id=1")->select();
		$ReportArr	  			= array();
		$performance			= C("PERFORMANCE_STATUS"); // 获取业绩状态
		$pay_status				= C("PAY_STATUS"); // 获取支付状态
		// 将关系数组转换成索引数组
		$ReportArr				= array();
		foreach($income as $key =>$val){
			if($report[0]["pid"]==0){
				$ReportArr[$key][]	= $val["pid"]; // 计划ID
			}
			if($report[0]["uid"]==0){
				$ReportArr[$key][]	= $val["uid"]; // 网站主ID
			}
			if($report[0]["settlement_time"]==0){
				$ReportArr[$key][]	= date("Y-m-d H:i:s",$val["settlement_time"]); // 结算日期
			}
			if($report[0]["real_income"]==0){
				$ReportArr[$key][]	= $val["real_income"]?0:0; // 真实收入
			}
			if($report[0]["settlement_income"]==0){
				$ReportArr[$key][]	= $val["settlement_income"]?0:0; // 结算收入
			}
			if($report[0]["ip"]==0){
				$ReportArr[$key][]	= $val["ip"]?0:0; // IP
			}
			if($report[0]["pv"]==0){
				$ReportArr[$key][]	= $val["pv"]?0:0; // PV
			}
			if($report[0]["click"]==0){
				$ReportArr[$key][]	= $val["click"]?0:0; // CLICK
			}
			if($report[0]["cps"]==0){
				$ReportArr[$key][]	= $val["cps"]?0:0; // CPS
			}
			if($report[0]["cpa"]==0){
				$ReportArr[$key][]	= $val["cpa"]?0:0; // CPA
			}
			if($report[0]["performance_status"]==0){
				$ReportArr[$key][]	= $performance[$val["performance_status"]]; // 业绩状态
			}
			if($report[0]["settlement_status"]==0){
				$ReportArr[$key][]	= $pay_status[$val["settlement_status"]]; // 结算状态
			}
		}
		// 需要查询出来的字段信息
		$HeaderArr					= array();
		if($report[0]["pid"]==0){ // 判断是否要导出计划ID信息
			$HeaderArr[]			= "计划ID";
		}
		if($report[0]["uid"]==0){ // 判断是否要导出网站主ID信息
			$HeaderArr[]			= "网站主ID";
		}
		if($report[0]["settlement_time"]==0){ // 判断是否要导出结算时间信息
			$HeaderArr[]			= "结算时间";
		}
		if($report[0]["real_income"]==0){ // 判断是否要导出真实收入信息
			$HeaderArr[]			= "真实收入";
		}
		if($report[0]["settlement_income"]==0){ // 判断是否要导出结算收入信息
			$HeaderArr[]			= "结算收入";
		}
		if($report[0]["ip"]==0){ // 判断是否要导出IP信息
			$HeaderArr[]			= "IP";
		}
		if($report[0]["pv"]==0){ // 判断是否要导出PV信息
			$HeaderArr[]			= "PV";
		}
		if($report[0]["click"]==0){ // 判断是否要导出CLICK信息
			$HeaderArr[]			= "CLICK";
		}
		if($report[0]["cps"]==0){ // 判断是否要导出CPS信息
			$HeaderArr[]			= "CPS";
		}
		if($report[0]["cpa"]==0){ // 判断是否要导出CPA信息
			$HeaderArr[]			= "CPA";
		}
		if($report[0]["performance_status"]==0){ // 判断是否要导出业绩状态信息
			$HeaderArr[]			= "业绩状态";
		}
		if($report[0]["settlement_status"]==0){ // 判断是否要导出支付状态信息
			$HeaderArr[]			= "支付状态";
		}
		// 下载Excel报表，输出即提示下载
		$this->downloadExcel($filename,$ReportArr,$HeaderArr);
	}
}