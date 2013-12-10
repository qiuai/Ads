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
class WebAction extends CommonAction {
    public function index(){
		$st			= M('site');
		import('ORG.Util.Page');
		$count		= $st->count();
		$Page     	= new Page($count,15);
		$nowPage  	= isset($_GET['p'])?$_GET['p']:1;
		$Page 		-> setConfig("first","首页");
		$Page 		-> setConfig("last", "尾页");
		$Page 		-> setConfig("prev","上一页");
		$Page 		-> setConfig("next","下一页");
		$Page 		-> setConfig("theme","%first%%upPage%%linkPage%%downPage%%end% 共%totalPage%页");
		$show     	= $Page->show();
		if($count<16){
			$show 	= '';
		}
		$site     	= $st->order('id')->page($nowPage.','.$Page->listRows)->select();
		$this		->assign('page',$show);
		$this		->assign('count',$count);
		$this		->assign("site",$site);
		$stp 		= M("sitetype");
		$sitetype   = $stp->where("id =".$site[0]['type'])->select();
		$this		->assign("sitetype",$sitetype[0]['name']);	
		$this		->display();
	}
	public function header(){
		$this->display();
	}
	public function footer(){
		$this->display();
	}
	public function left(){
		$this->display();
	}
	public function right(){
		$this->display();
	}
	public function site_search(){
		$content	= $_GET["content"];
		$condition	= $_GET["condition"];	
		switch($condition){
			case "site_id":
				$where = "id =".$content;
			break;
			case "site_domain":
				$where = "domain ='".$content."'";
			break;
			case "uid":
				$where = "uid =".$content;
			break;
			default:
			break;
		}
		$st 		= M("site");
		$site       = $st->where($where)->select();
		$stp 		= M("sitetype");
		$sitetype   = $stp->where("id =".$site[0]['type'])->select();
		$this		->assign("sitetype",$sitetype[0]['name']);
		$this		->assign("site",$site);
		$this		->display(index);
	}
	public function site_delete(){
		$id		= $_GET["site_id"];
		$site 	= M("site");
		$site	->where("id =".$id)->delete();
		$this	->success('删除成功','Web/index');
	}
	public function site_type(){
		$st = M("sitetype");
		$siteType = $st ->select();
		$this->assign("sitetype",$siteType);
		$this->display();
	}
	public function site_list(){
		$status = $_GET["status"];
		if($status==null){
			$where = 1;
		}else{
			$where = "status = ".$status;
		}
		$st 	= M("site");
		$site	= $st ->where($where)->select();
		$this->assign("site",$site);
		$this->display(index);
	}
	public function site_multi(){
		$ids 	= $_POST["ids"];
		$ids 	= rtrim($ids,",");
		$status = $_POST["status"];
		$st 	= M("site");
		$num = $st->where("id in (".$ids.")")->setField("status",$status);
		if(empty($num)){
			echo "1";
		}else{
			echo "0";
		}
	}
	public function site_type_edit(){
		$id = $_GET["code_id"];
		$st = M("sitetype");
		$siteType = $st ->where("id = ".$id)->select();
		$this->assign("sitetype",$siteType);
		$this->display();
	}
	public function zone(){
		// 创建数据库对象
		$zo 		= M('zone');
		import('ORG.Util.Page');
		$count		= $zo->count();
		$Page     	= new Page($count,15);
		$nowPage  	= isset($_GET['p'])?$_GET['p']:1;
		$Page 		-> setConfig("first","首页");
		$Page 		-> setConfig("last", "尾页");
		$Page 		-> setConfig("prev","上一页");
		$Page 		-> setConfig("next","下一页");
		$Page 		-> setConfig("theme","%first%%upPage%%linkPage%%downPage%%end% 共%totalPage%页");
		$show     	= $Page->show();
		if($count<16){
			$show 	= '';
		}
		$zone     	= $zo->order('id')->page($nowPage.','.$Page->listRows)->select();
		$this		->assign('page',$show);
		$this		->assign('count',$count);
		$this		->assign("zone",$zone);
		$this		->display();
	}
	public function addCheck(){
		// 创建数据库对象
		$siteType 			= M('sitetype');
		$siteType->name		= $_POST["code_name_zh"];
		$siteType->ename	= $_POST["code_value"];
		$siteType->status	= $_POST["status"];
		$siteType->sort		= $_POST["sort"];
		// 往数据库中添加
		$flag = $siteType->add();
		if($flag){
			$this->success('数据添加成功','Web/site_type');
		}else{
			$this->error("数据添加失败",'Web/site_type_add');
		}
	}
	public function editCheck(){
		// 创建数据库对象
		$siteType 		= M('sitetype');
		$st['id']		= $_POST["code_id"];
		$st['name']		= $_POST["code_name_zh"];
		$st['ename']	= $_POST["code_value"];
		$st['status']	= $_POST["status"];
		$st['sort']		= $_POST["sort"];
		// 更改数据库数据
		$siteType->where("id =".$_POST["code_id"])->data($st)->save();
		$this->success('数据更改成功','Web/site_type');
	}
}