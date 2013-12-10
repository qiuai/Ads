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
class SiteWebAction extends CommonAction {
	function _initialize(){
		$this->assign("flag","site");
	}
    public function index(){ 
		$this		->assign("title","网站列表");
		$st 		= M('site');
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
		$sitetype   = $stp->select();
		$this		->assign("sitetype",$sitetype);
		$this		->display();
    }
	public function site_list(){
		$this		->assign("title","网站列表");
		$st 		= M('site');
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
		$sitetype   = $stp->select();
		$this		->assign("sitetype",$sitetype);
		$this		->display(index);
	}
	public function site_add(){
		$this->assign("title","新增网站");
		$this->display();
	}
	public function addCheck(){
		$this->assign("title","新增网站");
		// 创建数据库对象
		$site 				= M('site');
		$site->sitename		= $_POST["site_name"];
		$site->domain		= $_POST["site_domain"];
		$site->type			= $_POST["site_type"];
		$site->description	= $_POST["description"];
		$site->uid			= 300200;
		$site->pr			= 0;
		$site->weight		= 0;
		$site->rank			= 0;
		$site->register		= 0;
		$site->addtime		= time();
		// 往数据库中添加
		$flag = $site->add();
		if($flag){
			$this->success('数据添加成功','SITE_URL/web.php/?m=SiteWeb&a=index');
		}else{
			$this->error("数据添加失败",'SITE_URL/web.php/?m=SiteWeb&a=site_add');
		}
	}
	public function site_edit(){
		$id 				= $_GET["site_id"];//dump($id);exit;
		// 创建数据库对象
		$site 				= M('site');
		$siteOld			= $site->where("id =".$id)->select();
		$this				->assign("siteOld",$siteOld);
		$this->display();
	}
	public function editCheck(){
		// 创建数据库对象
		$site 	 			= M('site');
		$st['id']			= $_POST["site_id"];
		$st['name']			= $_POST["site_name"];
		$st['domain']		= $_POST["site_domain"];
		$st['type']			= $_POST["site_type"];
		$st['description']	= $_POST["description"];
		// 更改数据库数据
		$site->where("id =".$_POST["site_id"])->data($st)->save();
		$this->success('数据更改成功','SITE_URL/web.php/?m=SiteWeb&a=index');
	}
	public function site_delete(){
		$id		= $_GET["site_id"];
		$site 	= M("site");
		$site	->where("id =".$id)->delete();
		$this	->success('删除成功','SITE_URL/web.php/?m=SiteWeb&a=index');
	}
	public function channel_list(){
		// 创建数据库对象
		$ch			= M('channel');
		import('ORG.Util.Page');
		$count		= $ch->count();
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
		$channel    = $ch->order('id')->page($nowPage.','.$Page->listRows)->select();
		$this		->assign('page',$show);
		$this		->assign('count',$count);
		$this		->assign("channel",$channel);
		$this		->display();
	}
	public function channel_add(){
		// 创建数据库对象
		$st 	= M('site');
		$site	=$st->field("id,sitename")->select();
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
			$this->success('数据添加成功','SITE_URL/web.php/?m=SiteWeb&a=channel_list');
		}else{
			$this->error("数据添加失败",'SITE_URL/web.php/?m=SiteWeb&a=channel_add');
		}
	}
	public function channel_edit(){
		$id 		= $_GET["channel_id"];
		// 创建数据库对象
		$ch 		= M('channel');
		$channel	= $ch->where("id=".$id)->select();
		$st 		= M('site');
		$site		= $st->field("id,sitename")->select();
		$this	->assign("channel",$channel);
		$this	->assign("site",$site);
		$this	->display();
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
		$this	-> success('更改成功','SITE_URL/web.php/?m=SiteWeb&a=channel_list');
	}
	public function channel_delete(){
		$id		= $_GET["channel_id"];
		$ch 	= M("channel");
		$ch		->where("id =".$id)->delete();
		$this	->success('删除成功','SITE_URL/web.php/?m=SiteWeb&a=channel_list');
	}
}