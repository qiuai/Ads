<?php
/**
 * 广告联盟系统  其他管理
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
class OtherAction extends CommonAction {
    public function index(){
    	$this->display();
	}
	// 公告分类列表
	public function noticeCategoryList(){
		$noticetype 	= M("notice_type");
		$category		= $noticetype->select();
		$this			->assign("category",$category);
 		$this			->display();
	}
	// 新增公告分类
	public function noticeCategoryAdd(){		
 		$this			->display();
	}
	// 处理新增公告分类
	public function noticeCategorySave(){
		$type['pid'] 	= (int)($_POST["pid"]);
		$type['category_name'] 	= $_POST["category_name"];
		$category 		= M("notice_type");
		$category		->data($type)->add();
		$this			->success("添加成功","SITE_URL/?m=Other&a=notice_category_list");
	}
	
	// 编辑公告分类
	public function noticeCategoryEdit(){
		$id				= (int)($_GET["category_id"]);
		$noticetype 	= M("notice_type");
		$category		= $noticetype->where("id=".$id)->select();
		$this			->assign("category",$category);
		$this			->display();
	}
	// 处理编辑公告分类
	public function noticeCategoryEditSave(){
		$type['id']		= (int)($_POST['category_id']);
		$type['pid'] 	= (int)($_POST["pid"]);
		$type['category_name'] 	= $_POST["category_name"];
		$category 		= M("notice_type");
		$category		->setField($type);
		$this			->success("添加成功","SITE_URL/?m=Other&a=notice_category_list");
	}
	// 删除公告分类
	public function noticeCategoryDelete(){
		$id				= (int)($_GET["category_id"]);
		$category 		= M("notice_type");
		$category		->where("id=".$id)->delete();
		$this			->success("添加成功","SITE_URL/?m=Other&a=notice_category_list");
	}
	// 公告列表
	public function noticeList(){
		$no			 	= M("notice");
		$notice			= $no->select();
		foreach($notice as $key=>$val){
			$notice[$key]["pubdate"]=date("Y-m-d",$val["pubdate"]);
			if($val["is_display"]==0){
				$notice[$key]["is_display"]="显示";
			}else{
				$notice[$key]["is_display"]="隐藏";
			}
		}
		$this			->assign("notice",$notice);
 		$this			->display();
	}
	// 添加公告
	public function noticeAdd(){
		$no				= M("notice_type");
		$category		= $no->select();
		$this			->assign("category",$category);
 		$this			->display();
	}
	// 处理添加公告
	public function noticeSave(){
 		$type['title'] 		= $_POST["title"];
 		$type['author'] 	= $_POST["author"];
 		$type['category_id']= (int)($_POST["category_id"]);
 		$type['sort'] 		= (int)($_POST["sort"]);
 		$type['is_display'] = (int)($_POST["is_display"]);
 		$type['content'] 	= $_POST["content"];
		$type['pubdate'] 	= time();
		$category 			= M("notice");
		$category			->data($type)->add();
		$this				->success("添加成功","SITE_URL/?m=Other&a=notice_list");
	}
	// 编辑公告
	public function noticeEdit(){
		$not			= M("notice_type");
		$category		= $not->select();
		$this			->assign("category",$category);
 		$id				= (int)($_GET["notice_id"]);
		$no			 	= M("notice");
		$notice			= $no->where("id=".$id)->select();
		$this			->assign("notice",$notice);
		$this			->display();
	}
	// 处理编辑公告
	public function noticeEditSave(){
 		$type['id']		= (int)($_POST['notice_id']); //编号
		$type['title'] 	= $_POST["title"]; //标题
		$type['author'] = $_POST["author"]; //作者
		$type['category_id']= (int)($_POST["category_id"]); //分类编号
		$type['sort'] 	= (int)($_POST["sort"]); //排序
		$type['is_display']= (int)($_POST["is_display"]); //是否显示
		$type['content']= $_POST["content"]; //内容
		$notice 		= M("notice");
		$notice			->where("id=".$type['id'])->setField($type); //更改公告
		$this			->success("更改数据成功","SITE_URL/?m=Other&a=notice_list");
	}
	// 删除公告
	public function noticeDelete(){
 		$id				= (int)($_GET["notice_id"]); //得到公告ID
		$no			 	= M("notice");
		$no				->where("id=".$id)->delete();
		$this			->success("删除成功","SITE_URL/?m=Other&a=notice_list");
	}
}