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
	// 处理新增公告分类
	public function noticeCategorySave(){
		$type['pid'] 	= (int)($_POST["pid"]); // 父级类别
		$type['category_name'] 	= $_POST["category_name"]; // 分类名称 
		if(empty($type['category_name'])){
			$this		->error("分类名称不能为空！","SITE_URL/?m=Other&a=noticeCategoryAdd");
		}else{
			$category 	= M("notice_type");
			$category	->data($type)->add(); // 添加数据入库
			$this		->success("添加成功","SITE_URL/?m=Other&a=noticeCategoryList");
		}
	}
	
	// 编辑公告分类
	public function noticeCategoryEdit(){
		$id				= (int)($_GET["category_id"]); // 分类ID
		$noticetype 	= M("notice_type");
		$category		= $noticetype->where("id=".$id)->select();
		$this			->assign("category",$category);
		$this			->display();
	}
	// 处理编辑公告分类
	public function noticeCategoryEditSave(){
		$type['id']		= (int)($_POST['category_id']); // 分类ID
		$type['pid'] 	= (int)($_POST["pid"]); // 父类ID
		$type['category_name'] 	= $_POST["category_name"]; // 分类名称
		if(empty($type['category_name'])){
			$this		->error("分类名称不能为空！","SITE_URL/?m=Other&a=noticeCategoryEdit&category_id=".$type['id']);
		}else{
			$category 	= M("notice_type");
			$category	->setField($type); // 更改分类
			$this		->success("更改成功！","SITE_URL/?m=Other&a=noticeCategoryList");
		}
	}
	// 删除公告分类
	public function noticeCategoryDelete(){
		$id				= (int)($_GET["category_id"]);
		$category 		= M("notice_type");
		$category		->where("id=".$id)->delete();
		$this			->success("添加成功","SITE_URL/?m=Other&a=noticeCategoryList");
	}
	// 公告列表
	public function noticeList(){
		$no			 	= M("notice");
		$notice			= $this->memberPage($no, $where, $pageNum=15, $order='id desc'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		foreach($notice as $key=>$val){
			$notice[$key]["pubdate"]=date("Y-m-d",$val["pubdate"]); // 发布时间
			if($val["is_display"]==0){ // 是否显示 
				$notice[$key]["is_display"]	= "显示";
			}else{
				$notice[$key]["is_display"]	= "隐藏";
			}
		}
		$this			->assign("notice",$notice);
 		$this			->display();
	}
	// 添加公告
	public function noticeAdd(){
		$no				= M("notice_type");
		$category		= $no->select(); // 查找分类
		$this			->assign("category",$category);
 		$this			->display();
	}
	// 处理添加公告
	public function noticeSave(){
 		$type['title'] 		= $_POST["title"]; // 标题
 		$type['author'] 	= $_POST["author"]; // 作者
 		$type['category_id']= (int)($_POST["category_id"]); // 
 		$type['sort'] 		= (int)($_POST["sort"]); // 排序
 		$type['is_display'] = (int)($_POST["is_display"]); // 是否显示0显1隐
 		$type['content'] 	= $_POST["content"]; // 内容
		$type['pubdate'] 	= time(); // 发布时间
		$category 			= M("notice");
		if(empty($type['title'])){
			$this			->error("标题不能为空！","SITE_URL/?m=Other&a=noticeAdd");
		}elseif(empty($type['author'])){
			$this			->error("作者不能为空！","SITE_URL/?m=Other&a=noticeAdd");
		}elseif(empty($type['content'])){
			$this			->error("内容不能为空！","SITE_URL/?m=Other&a=noticeAdd");
		}else{
			$category		->data($type)->add();
			$this			->success("添加成功","SITE_URL/?m=Other&a=noticeList");
		}	
	}
	// 编辑公告
	public function noticeEdit(){
		$not			= M("notice_type");
		$category		= $not->select();
		$this			->assign("category",$category);
 		$id				= (int)($_GET["notice_id"]); // 公告ID
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
		$this			->success("更改数据成功","SITE_URL/?m=Other&a=noticeList");
	}
	// 删除公告
	public function noticeDelete(){
 		$id				= (int)($_GET["notice_id"]); //得到公告ID
		$no			 	= M("notice");
		$no				->where("id=".$id)->delete();
		$this			->success("删除成功","SITE_URL/?m=Other&a=noticeList");
	}
}