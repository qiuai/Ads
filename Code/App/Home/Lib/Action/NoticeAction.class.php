<?php
/**
 * 广告联盟系统  通知公告
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
class NoticeAction extends CommonAction {
    public function index(){
		// 获取公告分类ID
		if(empty($_GET["category_id"])){
			$gid 	= 1; // 默认为1，全体公告
		}else{
			$gid 	= (int)($_GET["category_id"]);
		}
		// 获取公告ID
		$nid		= (int)($_GET['notice_id']);
		$no 		= M("notice"); // 通知公告表	
		if(empty($nid)){ // nid为空则显示列表页
			if(!empty($_GET['keyword'])){
				$where	= "category_id=".$gid." and title like '%".$_GET['keyword']."%'";
			}else{
				$where	= "category_id=".$gid;
			}
			$notice		= $this->memberPage($no, $where, $pageNum=15, $order='id desc'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
			foreach($notice as $key =>$val){
				$notice[$key]["pubdate"]=date("Y-m-d H:i:s",$val["pubdate"]);
			}
		}else{ // 否则显示详细页
			$notice = $no->where("id=".$nid)->select();
		}
		$not 		= M("notice_type"); // 通知公告类型表
		$category   = $not->select();
		$cate	 	= $not->where("id=".$gid)->select();
		$this		->assign("category",$category);
		$this		->assign("notice",$notice);
		$this		->assign('gid',$gid);
		$this		->assign("nid",$nid);
		$this		->assign("cate",$cate);
		$this		->assign('keyword',$_GET['keyword']);
		$this		->display();
    }
}