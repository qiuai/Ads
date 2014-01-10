<?php
/**
 * 广告联盟系统  财务管理
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
class FinanceWebAction extends CommonAction {
	// 提现明细列表
    public function index(){
		$fi				= M("finance_apply");
		// 按状态检索
		$status			= (int)($_POST["status"]);
		if(empty($status)){
			$where		= "1"; // 默认
		}else{
			$where		= "status =".$status; // 支付状态
		}
		$finance		= $this->memberPage($fi, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$pay_status		= C("PAY_STATUS"); // 获取支付状态
		foreach($finance as $key =>$val){
			if(empty($val["withdraw_date"])){
				$finance[$key]["withdraw_date"] = "-";
			}else{
				$finance[$key]["withdraw_date"]	= date("Y-m-d",$val["withdraw_date"]); // 提现时间
			}
			if(empty($val["paid_time"])){
				$finance[$key]["paid_time"] = "-";
			}else{
				$finance[$key]["paid_time"]	= date("Y-m-d",$val["paid_time"]); // 支付时间
			}
			$finance[$key]["status"]		= $pay_status[$val["status"]]; // 支付状态
		}
		$this			->assign("title","提现明细");
		$this			->assign("status",$status);
		$this			->assign("finance",$finance);
		$this			->display();
    }
	// 申请提现
	public function financePay(){
		$this			->assign("title","申请提现");
		$uid 			= $_SESSION[C('WEB_AUTH_KEY')]; // 网站主ID
		$web  			= M("web_balance"); // 财务表
		$balance		= $web->where("uid=".$uid)->select(); // 查处开户银行信息
		$me  			= M("member_detail"); // 会员详情表
		$member			= $me->where("uid=".$uid)->select();
		$apply_date		= date("Y-m-d H:i:s",time()); // 申请时间
		$this			->assign("balance",$balance);
		$this			->assign("member",$member);
		$this			->assign("apply_date",$apply_date);
		$this			->display();
    }
	// 处理提现申请数据
	public function financePayDo(){
		$uid						= $_SESSION[C('WEB_AUTH_KEY')]; // 网站主ID
		$data["uid"] 				= $uid;
		$data["apply_date"] 		= time(); // 申请时间
		$data["withdraw_date"]		= time(); // 提现时间
		$data["withdraw_balance"] 	= $_POST["balance"]; // 申请提现金额
		$data["pre_tax"] 			= $_POST["balance"]; // 申请提现金额==税前总收入
		$data["after_tax"] 			= $data["pre_tax"]; // 税后==税前总收入
		$data["withdraw_auto"]		= (int)($_POST["withdraw_auto"]); // 是否托管
		$web_balance				= M("web_balance");
		$web						= $web_balance->where("uid =".$uid)->select();
		if(empty($data["withdraw_balance"])){
			$this					->error("申请提现金额不能为空！","WEB_URL?m=FinanceWeb&a=financePay"); 
		}elseif($data["withdraw_balance"]<20){
			$this					->error("申请提现金额不能低于20.00","WEB_URL?m=FinanceWeb&a=financePay"); 
		}elseif($data["withdraw_balance"]>$web[0]["total_balance"]){
			$this					->error("申请提现金额大于总余额！","WEB_URL?m=FinanceWeb&a=financePay");
		}else{
			$finance_apply	  		= M("finance_apply"); // 提现申请表
			$finance		  		= $finance_apply->where("uid=".$uid)->data($data)->add(); // 添加提现申请数据
			$web_balance	  		= M("web_balance");
			$web_balance			->where('uid='.$uid)->setInc('withdraw_num',1); // 提现次数+1
			$this					->success("提现申请提交成功","WEB_URL?m=FinanceWeb&a=index"); // 成功跳转提交明细页面
		}
	}
	// 结算明细
	public function financeDetail(){
		// 按结算时间所处时间段查询
		$start_date		= $_POST["time_start"]; // 周期开始时间
		$end_date		= $_POST["time_end"]; // 周期结束时间
		// 转换时间戳
		$start			= strtotime($start_date);
		$end			= strtotime($end_date);
		$in				= M("income"); // 结算表
		if(empty($start)&&empty($end)){
			$where		= "1"; // 默认
		}else if(empty($start)&&!empty($end)){
			$where		= "settlement_time <= ".$end; // 早于结束时间
		}else if(!empty($start)&&empty($end)){
			$where		= "settlement_time >= ".$start; // 晚于开始时间
		}else{
			$where		= "settlement_time >= ".$start." and settlement_time <= ".$end; // 介于开始结束时间之间
		}
		$income			= $this->memberPage($in, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$ap				= M("ad_plan"); // 活动计划表
		foreach($income as $key =>$val){
			$income[$key]["start_date"]		= date("Y-m-d",$val["start_date"]); // 周期开始时间
			$income[$key]["end_date"]		= date("Y-m-d",$val["end_date"]); // 周期结束时间
			$income[$key]["settlement_time"]= date("Y-m-d",$val["settlement_time"]); // 结算时间
			$ad_plan						= $ap->where("id=".$val["pid"])->select(); // pid关联活动计划表id
			$income[$key]["plan_name"]		= $ad_plan[0]["plan_name"]; // 计划名称
		}
		// 标注开始日期
		if(empty($start_date)){
			$this		->assign("start_date",date("Y-m-d",time()-86400*3));
		}else{
			$this		->assign("start_date",$start_date);
		}
		// 标注结束日期	
		if(empty($end_date)){
			$this		->assign("end_date",date("Y-m-d",time()));
		}else{
			$this		->assign("end_date",$end_date);
		}
		$this			->assign("title","结算明细");
		$this			->assign("income",$income);
		$this			->display();
	}
	// 收款人银行卡信息
	public function financeBank(){
		$this			->assign("title","银行信息");
		$uid 			= $_SESSION[C('WEB_AUTH_KEY')]; // 用户ID
		$me  			= M("member_detail"); // 会员详情表
		$member			= $me->where("uid=".$uid)->select(); 
		$this			->assign("member",$member); // 会员表
		$this			->display();
    }
	// 编辑银行信息
	public function financeBankEdit(){
		$this			->assign("title","修改银行信息");
		$uid			= $_SESSION[C('WEB_AUTH_KEY')]; // 获取用户信息
		$bank_type		= C('BANK_SHORT'); // 获取银行信息
		$this			->assign("bank_type",$bank_type);
		$me				= M("member");
		$member			= $me->where("id =".$uid)->select();
		$this			->assign("member",$member);
		$med			= M("member_detail");
		$memberDetail	= $med->where("uid=".$uid)->select();
		$this			->assign("memberDetail",$memberDetail);
		$this			->display();
    }
	// 处理银行信息数据
	public function financeBankEditDo(){
		$this			->assign("title","修改银行信息");
		$real_name 		= $_POST["account"]; // 开户人、收款人
		$legal_status 	= (int)($_POST["legal_status"]); // 法人状态：0个人、1企业
		$bank_short 	= $_POST["bank"]; // 银行简称
		$bank_name 		= $_POST["bank_name"]; // 银行全称
		$card_number 	= (int)($_POST["accounts"]); // 开户账号
		$accounts_again = (int)($_POST["accounts_again"]); // 再次输入账号
		// 更改member表信息
		$data_member["real_name"]			= $real_name; // 真实姓名
		$data_member["legal_status"]		= $legal_status; // 法人状态
		// 更改member_detail表信息 
		$data_member_detail["bank_short"]	= $bank_short; // 银行简称
		$data_member_detail["card_author"]	= $real_name; // 开户人
		$data_member_detail["bank_name"]	= $bank_name; // 银行名称
		$data_member_detail["card_number"]	= $card_number; // 银行卡号
		if(empty($real_name)){
			$this->error("收款人名称不能为空!","WEB_URL?m=FinanceWeb&a=financeBankEdit"); // 失败返回重新编辑
		}elseif(empty($bank_name)){
			$this->error("银行全称不能为空!","WEB_URL?m=FinanceWeb&a=financeBankEdit"); // 失败返回重新编辑
		}elseif(empty($card_number)){
			$this->error("收款人账号不能为空!","WEB_URL?m=FinanceWeb&a=financeBankEdit"); // 失败返回重新编辑
		}elseif(empty($accounts_again)){
			$this->error("请再次输入收款人账号!","WEB_URL?m=FinanceWeb&a=financeBankEdit"); // 失败返回重新编辑
		}elseif($card_number!=$accounts_again){ // PHP判断两次账号输入是否相同
			$this->error("两次银行卡卡号或存折账号输入不一致!","WEB_URL?m=FinanceWeb&a=financeBankEdit");
		}else{
			$uid			= $_SESSION[C('WEB_AUTH_KEY')]; // 获取会员id
			$me				= M("member");
			$member			= $me->where("id =".$uid)->data($data_member)->save(); // 更改真实姓名、法人信息
			$med			= M("member_detail");
			$memberDetail	= $med->where("uid=".$uid)->data($data_member_detail)->save(); // 更改收款人银行卡信息
			$this			->success("数据更改成功","WEB_URL?m=FinanceWeb&a=financeBank"); // 成功则跳转银行信息页面
		}	
	}
}