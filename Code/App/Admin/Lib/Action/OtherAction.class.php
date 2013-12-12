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
	// 添加公告分类
	public function notice_category_add(){		
 		$this			->display();
	}
	public function notice_category_save(){
		$type['pid'] 	= $_POST["pid"];
		$type['category_name'] 	= $_POST["category_name"];
		$category 		= M("noticetype");
		$category		->data($type)->add();
		$this			->success("添加成功","SITE_URL/?m=Other&a=notice_category_list");
	}
	// 公告分类列表
	public function notice_category_list(){
		$noticetype 	= M("noticetype");
		$category		= $noticetype->select();
		$this			->assign("category",$category);
 		$this			->display();
	}
	// 编辑公告分类
	public function notice_category_edit(){
		$id				= (int)($_GET["category_id"]);
		$noticetype 	= M("noticetype");
		$category		= $noticetype->where("id=".$id)->select();
		$this			->assign("category",$category);
		$this			->display();
	}
	public function notice_category_edit_save(){
		$type['id']		= $_POST['category_id'];
		$type['pid'] 	= $_POST["pid"];
		$type['category_name'] 	= $_POST["category_name"];
		$category 		= M("noticetype");
		$category		->setField($type);
		$this			->success("添加成功","SITE_URL/?m=Other&a=notice_category_list");
	}
	// 删除公告分类
	public function notice_category_delete(){
		$id				= (int)($_GET["category_id"]);
		$category 		= M("noticetype");
		$category		->where("id=".$id)->delete();
		$this			->success("添加成功","SITE_URL/?m=Other&a=notice_category_list");
	}
	// 公告列表
	public function notice_list(){
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
	public function notice_add(){
		$no				= M("noticetype");
		$category		= $no->select();
		$this			->assign("category",$category);
 		$this			->display();
	}
	public function notice_save(){
 		$type['title'] 		= $_POST["title"];
 		$type['author'] 	= $_POST["author"];
 		$type['category_id']= $_POST["category_id"];
 		$type['sort'] 		= $_POST["sort"];
 		$type['is_display'] = $_POST["is_display"];
 		$type['content'] 	= $_POST["content"];
		$type['pubdate'] 	= time();
		$category 			= M("notice");
		$category			->data($type)->add();
		$this				->success("添加成功","SITE_URL/?m=Other&a=notice_list");
	}
	// 编辑公告
	public function notice_edit(){
		$not			= M("noticetype");
		$category		= $not->select();
		$this			->assign("category",$category);
		
 		$id				= (int)($_GET["notice_id"]);
		$no			 	= M("notice");
		$notice			= $no->where("id=".$id)->select();
		$this			->assign("notice",$notice);
		$this			->display();
	}
	public function notice_edit_save(){
 		$type['id']		= $_POST['notice_id'];
		$type['title'] 	= $_POST["title"];
		$type['author'] = $_POST["author"];
		$type['category_id']= $_POST["category_id"];
		$type['sort'] 	= $_POST["sort"];
		$type['is_display']= $_POST["is_display"];
		$type['content']= $_POST["content"];
		$notice 		= M("notice");
		$notice			->where("id=".$type['id'])->setField($type);
		$this			->success("更改数据成功","SITE_URL/?m=Other&a=notice_list");
	}
	// 删除公告
	public function notice_delete(){
 		$id				= (int)($_GET["notice_id"]);
		$no			 	= M("notice");
		$no				->where("id=".$id)->delete();
		$this			->success("删除成功","SITE_URL/?m=Other&a=notice_list");
	}
}