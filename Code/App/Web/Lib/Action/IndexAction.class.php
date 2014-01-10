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
		$notice	= $no->where("category_id = 2")->order("id desc")->limit("0,10")->select();
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
}