<?php
/**
 * 广告联盟系统  网站管理
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
class SiteWebAction extends CommonAction {
	//  网站列表
    public function index(){ 
		$this		->assign("title","网站列表");
		$st 		= M('site');
		$site		= $this->memberPage($st, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$stp 		= M("site_type");
		foreach($site as $key =>$val){
			$sitetype= $stp->where("id=".$val["site_type"])->select(); // 网站类型，对应site_type表id
			$site[$key]["code_name_zh"]=$sitetype[0]["code_name_zh"]; // 显示网站类型
		}
		$this		->assign("site",$site);
		$this		->display();
    }
	// 新增网站
	public function siteAdd(){
		$this		->assign("title","新增网站");
		$site_type	= M("site_type");
		$site		= $site_type->select(); // 获取网站类型
		$this		->assign("site",$site);
		$this		->display();
	}
	// 处理新增网站
	public function addCheck(){
		$this				->assign("title","新增网站");
		$site 				= M('site');
		$data["site_name"]	= $_POST["site_name"]; // 网站名称
		$data["site_domain"]= $_POST["site_domain"]; // 网站域名
		$data["site_type"]	= $_POST["site_type"]; // 网站类型，对应site_type表id
		$data["description"]= $_POST["description"]; // 网站简介
		$data["uid"]		= $_SESSION[C("WEB_AUTH_KEY")]; // 网站主ID
		$data["addtime"]	= time(); // 添加时间
		if(empty($data["site_name"])){
			$this			->error("网站名称不能为空！",'WEB_URL/?m=SiteWeb&a=siteAdd');
		}elseif(empty($data["site_domain"])){
			$this			->error("网站域名不能为空！",'WEB_URL/?m=SiteWeb&a=siteAdd');
		}else{
			$site			->data($data)->add(); // 添加网站
			$this			->success('网站添加成功！','WEB_URL/?m=SiteWeb&a=index');
		}
	}
	// 编辑网站
	public function siteEdit(){
		$id 				= (int)($_GET["site_id"]);
		$st 				= M('site');
		$site				= $st->where("id =".$id)->select();
		$stp				= M("site_type");
		$site_type			= $stp->select();
		$this				->assign("site",$site);
		$this				->assign("site_type",$site_type);
		$this				->display();
	}
	// 处理编辑网站
	public function editCheck(){
		$site 	 			= M('site');
		$data['id']			= (int)($_POST["site_id"]);
		$data['site_name']	= $_POST["site_name"];
		$data['site_domain']= $_POST["site_domain"];
		$data['site_type']	= $_POST["site_type"];
		$data['description']= $_POST["description"];
		if(empty($data["site_name"])){
			$this			->error("网站名称不能为空！",'WEB_URL/?m=SiteWeb&a=siteEdit&site_id='.$data['id']);
		}elseif(empty($data["site_domain"])){
			$this			->error("网站域名不能为空！",'WEB_URL/?m=SiteWeb&a=siteEdit&site_id='.$data['id']);
		}else{
			$site			->where("id =".$data['id'])->data($data)->save(); // 编辑网站
			$this			->success('网站更改成功！','WEB_URL/?m=SiteWeb&a=index');
		}
	}
	// 删除网站
	public function siteDelete(){
		$id		= (int)($_GET["site_id"]);
		$site 	= M("site");
		$site	->where("id =".$id)->delete();
		$this	->success('删除成功','WEB_URL/?m=SiteWeb&a=index');
	}
	// 频道列表
	public function channelList(){
		$ch			= M('channel');
		$channel	= $this->memberPage($ch, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$this		->assign("channel",$channel);
		$this		->display();
	}
	// 新增频道
	public function channelAdd(){
		// 创建数据库对象
		$st 	= M('site');
		$site	=$st->field("id,site_name")->select();
		$this	->assign("site",$site);
		$this	->display();
	}
	public function  channel_addCheck(){
		// 创建数据库对象
		$channel 				= M('channel');
		$channel->sort			= $_POST["sort"];
		$channel->sid			= $_POST["site_type"];
		$channel->name			= $_POST["name"];
		$channel->status		= $_POST["status"];
		$channel->desc			= $_POST["desc"];
		// 往数据库中添加
		$flag = $channel->add();
		if($flag){
			$this->success('数据添加成功','WEB_URL/?m=SiteWeb&a=channel_list');
		}else{
			$this->error("数据添加失败",'WEB_URL/?m=SiteWeb&a=channel_add');
		}
	}
	// 编辑频道
	public function channel_edit(){
		$id 		= (int)($_GET["channel_id"]);
		// 创建数据库对象
		$ch 		= M('channel');
		$channel	= $ch->where("id=".$id)->select();
		$st 		= M('site');
		$site		= $st->field("id,site_name")->select();
		$this		->assign("channel",$channel);
		$this		->assign("site",$site);
		$this		->display();
	}
	public function  channel_editCheck(){
		$this->assign("title","编辑频道");
		// 创建数据库对象
		$ch		 				= M('channel');
		$channel['id']			= $_POST["id"];
		$channel['sort']		= $_POST["sort"];
		$channel['sid']			= $_POST["site_type"];
		$channel['name']		= $_POST["name"];
		$channel['status']		= $_POST["status"];
		$channel['desc']		= $_POST["desc"];
		// 往数据库中添加
		$ch		-> where("id=".$_POST["id"])->data($channel)->save();
		$this	-> success('更改成功','WEB_URL/?m=SiteWeb&a=channel_list');
	}
	// 删除频道
	public function channel_delete(){
		$id		= (int)($_GET["channel_id"]);
		$ch 	= M("channel");
		$ch		->where("id =".$id)->delete();
		$this	->success('删除成功','WEB_URL/?m=SiteWeb&a=channel_list');
	}
}