<?php
/**
 * 广告联盟系统  首页
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
class IndexAction extends CommonAction {
	/**
	 * 头部
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-20 上午11:21:30
	 */
	public function header(){
		$model = M('User');
		$header = $model->find($_SESSION[C('USER_AUTH_KEY')]);
		
		$this->assign('header',$header);
		$this->display();
	}
	/**
	 * 报表展现
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-20 上午11:21:20
	 */
	public function right(){
		
		// 最近十天数据显示
		$this->tenDaysBefore();
		
		// 日历输出
		$this->getCalendar();
		
		// 当日新增情况
		$this->todayAdd();
		
		$this->display();
	}
	/**
	 * 当日新增情况
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-20 下午1:46:57
	 */
	public function todayAdd(){
		// 新增会员
		
		// 新增广告
		
		// 待处理提现数
		
		// 积分商城订单
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
		// 查询中心 当日时间或者选择时间
		$today = $_REQUEST['day'] ? $_REQUEST['day'] : date('d');
		
		// json 输出 格式：	day:"1117",income:10,count_ip:10,count_pv:20,count_click:40,count_cps:0,count_cpa:0};
// 		$tendaysbefore = mktime(0,0,0,date("m") ,intval($today)-10,date("Y"));
// 		$yestoday = mktime(0,0,0,date("m") ,-1,date("Y"));
// 		$yestoday = strtotime(date('Y-m-d'), $yestoday);
		
// 		var_dump(date('Y-m-d H:i:s',$yestoday));exit;
		
		// 获取数据
		$model = M('Income');
		for($i=10; $i>0; $i--){
			$day = mktime(0,0,0,date("m") ,$today-($i+1),date("Y"));
			$yestoday = mktime(0,0,0,date("m") ,$today-$i,date("Y"));
			$data = $model->query("select sum(click) as click, sum(pv) as pv, sum(cpm) as cpm, sum(cpc) as cpc, sum(real_income) as income, count(ip) as ip from " . C('DB_PREFIX') . "income where settlement_time < $yestoday and settlement_time >= $day");
			foreach($data[0] as $key=>$value){
				$data[0][$key] = $value ? $value : 0;
			}
			$data[0]['day'] = date('md', $yestoday);
			$list[] = $data[0];
		}
		
		$json = json_encode($list);
		
		$this->assign("chartData", $json);
		
	}
	/**
	 * 日历输出
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-20 上午11:20:51
	 */
	public function getCalendar(){
		$mdays=date("t");    //当月总天数
		$datenow=date("j");  //当日日期
		$monthnow=date("n"); //当月月份
		$yearnow=date("Y");  //当年年份
		//计算当月第一天是星期几
		$wk1st=date("w",mktime(0,0,0,$monthnow,1,$yearnow));
		$trnum=ceil(($mdays+$wk1st)/7); //计算表格行数
		
		//输出表格
		$tabstr='<table style="border-left:1px solid #DDDDDD;border-top:0px solid #dddddd; border-bottom:1px solid #DDDDDD;" align="center" bgcolor="dddddd" cellpadding="0" cellspacing="1" width="100%">';
		
		// 输出 字符 ：日历
		$tabstr.='<tr align="center" bgcolor="#CCCCCC">';
			$tabstr.='<td colspan="7" style="color:#333; font-size:18px; background-color:#eeeeee; padding-left:10px" align="left" height="30">';
			$tabstr.='日历';
			$tabstr.='</td>';
		$tabstr.='</tr>';
													
		// 输出X年X月
		$tabstr.='<tr align="center" bgcolor="#CCCCCC">';
			$tabstr.='<td bgcolor="#F8F8F8" height="19">';
			
				$tabstr.='<table align="center" bgcolor="#eeeeee" cellpadding="0" cellspacing="0" width="98%">';
				$tabstr.='<tr align="center" bgcolor="#CCCCCC">';
				$tabstr.='<td colspan="7" style="color:#333;padding-left:10px;font-size:20px;" align="left" bgcolor="#F8F8F8" height="25">';
				$tabstr.=date("Y"). '年' . date("m") . '月';
				$tabstr.='</td>';
				$tabstr.='</tr>';
				
				// 输出星期
				$tabstr.='<tr align="center" bgcolor="#CCCCCC">';
				$tabstr.='<td bgcolor="#F8F8F8" height="19" width="14%">日</td>';
				$tabstr.='<td bgcolor="#F8F8F8" width="14%">一</td>';
				$tabstr.='<td bgcolor="#F8F8F8" width="14%">二</td>';
				$tabstr.='<td bgcolor="#F8F8F8" width="14%">三</td>';
				$tabstr.='<td bgcolor="#F8F8F8" width="14%">四</td>';
				$tabstr.='<td bgcolor="#F8F8F8" width="14%">五</td>';
				$tabstr.='<td bgcolor="#F8F8F8" height="25" width="14%">六</td>';
				$tabstr.='</tr>';
				$tabstr.='</table>';
			
			$tabstr.="</td>";
		$tabstr.="</tr>";
		
		// 输出 日期
		$tabstr.='<table align="center" bgcolor="#eeeeee" cellpadding="0" cellspacing="1" width="98%" height="220">';

		for($i=0;$i<$trnum;$i++) {
			$tabstr.='<tr align="center">';
			for($k=0;$k<7;$k++) { //每行七个单元格
				$tabidx=$i*7+$k; //取得单元格自身序号
				//若单元格序号小于当月第一天的星期数($wk1st)或大于(月总数+$wk1st)
				//只填写空格，反之，写入日期
				($tabidx<$wk1st or $tabidx>$mdays+$wk1st-1) ? $dayecho="&nbsp" : $dayecho=$tabidx-$wk1st+1;
				//突出标明今日日期
				if($dayecho==$datenow){
					$todaybg = 'style="position:relative;width:14.3%" align="center" bgcolor="#F7B781" height="41"';
				}else{
					$todaybg = 'style="position:relative;width:14.3%" align="center" bgcolor="#ffffff" height="41"';
				}
				//如果为空格
				if($dayecho=="&nbsp"){
					$todaybg = 'bgcolor="#ffffff"';
				}
				$tabstr.="<td $todaybg>$dayecho</td>";
			}
			$tabstr.="</tr>";
		}
		
		$tabstr.="</table>";
		
		$tabstr.="</table>";
		
		$this->assign("calendar", $tabstr);
	}
}