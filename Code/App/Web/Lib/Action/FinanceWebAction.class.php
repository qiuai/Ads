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
	// 提现明细
    public function index(){
		$fi=M("finance");
		// 按状态检索
		$status=(int)($_POST["status"]);
		if(empty($status)){
			$finance=$fi->select();
		}else{
			$finance=$fi->where("payment_status =".$status)->select();
		}
		// 处理提现时间，支付时间，支付状态
		foreach($finance as $key =>$val){
			$finance[$key]["withdraw_date"] = date("Y-m-d",$val["withdraw_date"]);
			$finance[$key]["paid_time"] = date("Y-m-d",$val["paid_time"]);
			switch($val["payment_status"]){
				case 1:
					$finance[$key]["payment_status"] = "未支付";
				break;
				case 2:
					$finance[$key]["payment_status"] = "已支付";
				break;
				case 3:
					$finance[$key]["payment_status"] = "异常";
				break;
				default:
				break;
			}
		}
		$this->assign("title","提现明细");
		$this->assign("finance",$finance);
		$this->display();
    }
	// 申请提现
	public function financePay(){
		$this	->assign("title","申请提现");
		$uid 	= $_SESSION[C('WEB_AUTH_KEY')];
		// 创建web_balance表对象
		$web  	= M("web_balance");
		$balance= $web->where("uid=".$uid)->select();
		// 创建member_detail表对象
		$me  	= M("member_detail");
		$member	= $me->where("uid=".$uid)->select();
		$apply_date= date("Y-m-d H:i:s",time());
		$this	->assign("balance",$balance);
		$this	->assign("member",$member);
		$this	->assign("apply_date",$apply_date);
		$this	->display();
    }
	// 处理提现申请数据
	public function financePayDo(){
		$uid						= $_SESSION[C('WEB_AUTH_KEY')];
		$data["uid"] 				= $uid;
		$data["apply_date"] 		= time();
		$data["withdraw_balance"] 	= $_POST["balance"];
		$data["withdraw_auto"]		= $_POST["withdraw_auto"];
		$finance_apply	  			= M("finance_apply");
		$finance		  			= $finance_apply->where("uid=".$uid)->data($data)->add(); // 添加提现申请数据
		$web_balance	  			= M("web_balance");
		$web_balance				->where('uid='.$uid)->setInc('withdraw_num',1);  // 提现次数+1
		$this						->success("提现申请提交成功","SITE_URL/?m=FinanceWeb&a=financePay"); 
		// 成功跳转提交明细页面
	}
	// 收款人银行卡信息
	public function financeBank(){
		$this	->assign("title","银行信息");
		$uid 	= $_SESSION[C('WEB_AUTH_KEY')];
		$me  	= M("member_detail");
		$member	= $me->where("uid=".$uid)->select();
		$this	->assign("member",$member);
		$this	->display();
    }
	// 编辑银行信息
	public function financeBankEdit(){
		$this->assign("title","修改银行信息");
		$uid=$_SESSION[C('WEB_AUTH_KEY')]; // 获取用户信息
		$bank_type=C('BANK_TYPE'); // 获取银行信息
		$this->assign("bank_type",$bank_type);
		$me=M("member");
		$member=$me->where("id =".$uid)->select();
		$this->assign("member",$member);
		$med=M("member_detail");
		$memberDetail=$med->where("uid=".$uid)->select();
		$this->assign("memberDetail",$memberDetail);
		$this->display();
    }
	// 处理银行信息数据
	public function financeBankEditDo(){
		$this			->assign("title","修改银行信息");
		$real_name 		= $_POST["account"]; // 开户人、收款人
		$legal_status 	= $_POST["legal_status"]; // 法人状态：个人、企业
		$bank_short 	= $_POST["bank"]; // 银行简称
		$bank_name 		= $_POST["bank_name"]; // 银行全称
		$card_number 	= $_POST["accounts"]; // 开户账号
		$accounts_again = $_POST["accounts_again"]; // 再次输入账号
		// 更改member表信息
		$data_member["real_name"]= $real_name;
		$data_member["legal_status"]= $legal_status;
		// 更改member_detail表信息 
		$data_member_detail["bank_short"]= $bank_short;
		$data_member_detail["card_author"]= $real_name;
		$data_member_detail["bank_name"]= $bank_name;
		$data_member_detail["card_number"]= $card_number;
		// PHP判断两次账号输入是否相同
		if($card_number!=$accounts_again){
			$this->error("两次银行卡卡号或存折账号 输入不一致!","SITE_URL/?m=FinanceWeb&a=financeBankEdit"); // 失败返回重新编辑
		}else{
			$uid=$_SESSION[C('WEB_AUTH_KEY')]; // 获取会员id
			$me=M("member");
			$member=$me->where("id =".$uid)->data($data_member)->save(); // 更改真实姓名、法人信息
			$med=M("member_detail");
			$memberDetail=$med->where("uid=".$uid)->data($data_member_detail)->save(); // 更改收款人银行卡信息
			$this->success("数据更改成功","SITE_URL/?m=FinanceWeb&a=financeBank"); // 成功则跳转银行信息页面
		}
		
	}
	// 结算明细
	public function financeDetail(){
		// 按结算时间所处时间段查询
		$start_date	= $_POST["time_start"];
		$end_date	= $_POST["time_end"];
		$start		= strtotime($start_date);
		$end		= strtotime($end_date);
		$in			= M("income");
		if(empty($start)&&empty($end)){
			$income=$in->select(); // 默认
		}else if(empty($start)&&!empty($end)){
			$income=$in->where("settlement_time <= ".$end)->select(); // 早于结束时间
		}else if(!empty($start)&&empty($end)){
			$income=$in->where("settlement_time >= ".$start)->select(); // 晚于开始时间
		}else{
			$income=$in->where("settlement_time >= ".$start." and settlement_time <= ".$end)->select(); // 介于开始结束时间之间
		}
		foreach($income as $key =>$val){
			$income[$key]["start_date"] = date("Y-m-d",$val["start_date"]); // 周期开始时间
			$income[$key]["end_date"] = date("Y-m-d",$val["end_date"]); // 周期结束时间
			$income[$key]["settlement_time"] = date("Y-m-d",$val["settlement_time"]); // 结算时间
		}
		// 标注开始日期
		if(empty($start_date)){
			$this->assign("start_date",date("Y-m-d",time()-86400*3));
		}else{
			$this->assign("start_date",$start_date);
		}
		// 标注结束日期	
		if(empty($end_date)){
			$this->assign("end_date",date("Y-m-d",time()));
		}else{
			$this->assign("end_date",$end_date);
		}
		$this->assign("income",$income);
		$this->display();
	}
}