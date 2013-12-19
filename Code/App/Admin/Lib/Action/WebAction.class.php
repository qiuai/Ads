<?php
/**
 * 广告联盟系统  网站管理
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
	//网站列表
    public function index(){
		$st			= M('site');
		import('ORG.Util.Page'); //调用分页类
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
		foreach($site as $key =>$val){ //添加时间
			$site[$key]["addtime"] = date("Y-m-d H:i:s",$val["addtime"]);
		}
		$this		->assign('page',$show);	
		$this		->assign('count',$count); //记录个数
		$this		->assign("site",$site);
		$stp 		= M("site_type");
		$sitetype   = $stp->where("id =".$site[0]['site_type'])->select();
		$this		->assign("sitetype",$sitetype[0]['code_name_zh']);	
		$this		->display();
	}
	//按条件查询
	public function site_search(){
		$content	= $_GET["content"]; //查询条件
		$condition	= $_GET["condition"]; //查询条件类型
		switch($condition){
			case "site_id": //按网站ID查询
				$where = "id =".$content;
			break;
			case "site_domain": //按网站域名查询
				$where = "site_domain ='".$content."'";
			break;
			case "uid": //按用户ID查询
				$where = "uid =".$content;
			break;
			default:
			break;
		}
		$st 		= M("site");
		$site       = $st->where($where)->select();
		$stp 		= M("site_type");
		$sitetype   = $stp->where("id =".$site[0]['site_type'])->select();
		$this		->assign("sitetype",$sitetype[0]['code_name_zh']);
		$this		->assign("site",$site);
		$this		->display(index);
	}
	//删除网站
	public function site_delete(){
		$id		= (int)($_GET["site_id"]);
		$site 	= M("site");
		$site	->where("id =".$id)->delete();
		$this	->success('删除成功','SITE_URL/?m=Web&a=index');
	}
	//网站分类列表
	public function site_type(){
		$st = M("site_type");
		$siteType = $st ->select();
		$this->assign("sitetype",$siteType);
		$this->display();
	}
	//批量操作，改变网站状态
	public function site_multi(){
		$ids 	= $_POST["ids"]; //得到选中的ID
		$ids 	= rtrim($ids,","); 
		$status = (int)($_POST["status"]);
		$st 	= M("site");
		$num 	= $st->where("id in (".$ids.")")->setField("status",$status); //批量改变网站状态
		if(empty($num)){
			echo "1"; //失败
		}else{
			echo "0"; //成功
		}
	}
	//编辑网站分类
	public function site_type_edit(){
		$id 		= (int)($_GET["code_id"]); //得到网站分类ID
		$st 		= M("site_type");
		$siteType 	= $st ->where("id = ".$id)->select();
		$this		->assign("sitetype",$siteType);
		$this		->display();
	}
	//代码位列表
	public function zone(){
		$zo 		= M('zone');
		import('ORG.Util.Page'); //调用分页类
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
		$ads		= M("ad_size"); //查询广告尺寸表
		foreach($zone as $key =>$val){
			$adsize	= $ads->where("id=".$zone[$key]["size"])->select(); //zone表size关联ad_size表id
			foreach($adsize as $keys => $value){
				$zone[$key]["width"]=$value["width"];
				$zone[$key]["height"]=$value["height"];
				switch($value["size_type"]){
					case 1:
						$zone[$key]["display"]="图片";
					break;
					case 2:
						$zone[$key]["display"]="文字";
					break;
					case 3:
						$zone[$key]["display"]="漂浮";
					break;
					case 4:
						$zone[$key]["display"]="对联";
					break;
					case 5:
						$zone[$key]["display"]="弹窗";
					break;
					case 6:
						$zone[$key]["display"]="视窗";
					break;
					default:
					break;
				}
			}
		}
		$this		->assign('page',$show);
		$this		->assign('count',$count);
		$this		->assign("zone",$zone);
		$this		->display();
	}
	//添加站点分类
	public function addCheck(){
		$siteType 			= M('site_type');
		$siteType->code_name_zh	= $_POST["code_name_zh"]; //站点分类名称
		$siteType->code_name_en	= $_POST["code_value"]; //站点分类英文名称
		$siteType->status	= (int)($_POST["status"]); //是否显示
		$siteType->sort		= (int)($_POST["sort"]); //排序
		// 往数据库中添加
		$flag = $siteType->add();
		if($flag){
			$this->success('数据添加成功','SITE_URL/?m=Web&a=site_type');
		}else{
			$this->error("数据添加失败",'SITE_URL/?m=Web&a=site_type_add');
		}
	}
	//编辑站点分类
	public function editCheck(){
		$siteType 		= M('site_type');
		$st['id']		= (int)($_POST["code_id"]);
		$st['code_name_zh']	= $_POST["code_name_zh"];
		$st['code_name_en']	= $_POST["code_value"];
		$st['status']	= (int)($_POST["status"]);
		$st['sort']		= (int)($_POST["sort"]);
		// 更改数据库数据
		$siteType->where("id =".$_POST["code_id"])->data($st)->save();
		$this->success('数据更改成功','SITE_URL/?m=Web&a=site_type');
	}
}