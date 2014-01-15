<?php
/**
 * 广告联盟系统  广告主  首页
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
		$this->assign("title","网站主-首页");
		
		//  报表查询
		$this->tenDaysBefore();
		$no		= M("notice"); // 查询通知公告表_广告主公告
		$notice	= $no->where("category_id = 3")->order("id desc")->limit("0,10")->select();
		$this	->assign("notice",$notice);
		
		$this->getBalance();	// 账户余额显示

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
     * 账户余额
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2014-1-13 下午1:18:19
     */
    public function getBalance(){
        $model = M('income');
        $where['uid'] = $_SESSION[C('ADV_AUTH_KEY')];
        $data = $model->where($where)->find();
    
        // 昨日佣金
        $this->yestodayBalance();
    
        $this->assign('balance',$data);
    }
    /**
     * 昨日佣金
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2014-1-13 下午1:28:03
     */
    public function yestodayBalance(){
        $model = M();
        $day = mktime(0,0,0,date("m") ,date('d')-1,date("Y"));
        $yestoday = mktime(0,0,0,date("m"),date('d'),date("Y"));
        $data = $model->query("select zv.click_ip_num as click, zv.view_pv_num as pv, p.pay_type, p.price, p.site_master_display_price, p.site_master_pay_price, zv.view_pv_num as ip from " . C('DB_PREFIX') . "zone_visit_count zv join " . C('DB_PREFIX') . "zone z on z.id = zv.zid join " . C('DB_PREFIX') . "ad_plan p on p.id = zv.pid where zv.day_start_time < $yestoday and zv.day_start_time >= $day and p.uid = ".$_SESSION[C('ADV_AUTH_KEY')]);
        $yestodayBalance = ($data[0]['price'] - $data[0]['site_master_pay_price']) * $data[0]['ip'];	// 联盟收入
    
        $this->assign('yestodayBalance',$yestodayBalance);
    }
}