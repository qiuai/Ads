<?php
/**
 * 广告联盟系统  首页
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
class IndexAction extends CommonAction {
    public function index(){
		$this	->assign("title","网站主-首页");
		//  报表查询
		$this	->tenDaysBefore();
		$no		= M("notice"); // 查询通知公告表_网站主公告
		$notice	= $no->where("categroy_id = 2")->order("id desc")->limit("0,10")->select();
		$this	->assign("notice",$notice);
		$this	->display();
    }
    /**
     * 最近十天数据
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-25 下午4:49:19
     */
    public function tenDaysBefore(){
    	R('Report/tenDaysBefore');
    }
    /**
     * 最近十天数据
     * backup
     * cpm cpc
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-20 下午1:45:06
     */
    public function tenDaysBefores(){
    	$uid = $_SESSION[C('WEB_AUTH_KEY')];
    	// 查询中心 当日时间或者选择时间
    	$today = date('d');
    	 
    	// 获取数据
    	$model = M('Income');
    	for($i=9; $i>=0; $i--){
    		$day = mktime(0,0,0,date("m") ,$today-($i+1),date("Y"));
    		$yestoday = mktime(0,0,0,date("m") ,$today-$i,date("Y"));
    		$data = $model->query("select sum(click) as click, sum(pv) as pv, sum(cpm) as cpm, sum(cpc) as cpc, sum(real_income) as income, count(ip) as ip from " . C('DB_PREFIX') . "income where settlement_time < $yestoday and settlement_time >= $day and uid = $uid");
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