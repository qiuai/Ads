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
		$no		= M("notice"); // 查询通知公告表
		$all	= $no->where("category_id=1")->order("id desc")->limit("0,6")->select();
		foreach($all as $key=>$val){
			$all[$key]["pubdate"]	= date("m-d",$val["pubdate"]);
		}
		$web	= $no->where("category_id=2")->order("id desc")->limit("0,6")->select();
		foreach($web as $key=>$val){
			$web[$key]["pubdate"]	= date("m-d",$val["pubdate"]);
		}
		$adv	= $no->where("category_id=3")->order("id desc")->limit("0,6")->select();
		foreach($adv as $key=>$val){
			$adv[$key]["pubdate"]	= date("m-d",$val["pubdate"]);
		}
		$this	->assign("all",$all);
		$this	->assign("web",$web);
		$this	->assign("adv",$adv);
		$this	->display();
    }
}