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
class ZoneWebAction extends CommonAction {
	function _initialize(){
		$this->assign("flag","zone");
	}
    public function index(){ 
		$this->assign("title","代码位管理");
		$zo 	= M("zone");
		$zone 	= $zo->select();
		$this	->assign("zone",$zone);
		$this	->display();
    }
	public function zone_list(){
		$this->assign("title","代码位管理");
		$zo 	= M("zone");
		$zone 	= $zo->select();
		$this	->assign("zone",$zone);
		$this	->display(index);
	}
	public function zone_add(){
		$this	->assign("title","新增代码位");
		$st		= M('site');
		$site	= $st->field("id,domain")->select();
		$this   ->assign("site",$site);
		$this	->display();
	}
	public function addCheck(){
		// 创建数据库对象
		$zone 				= M('zone');
		$zone->name			= $_POST["zone_name"];
		$zone->sid			= $_POST["site_id"];
		$zone->cp			= $_POST["pay_type"];
		$zone->size			= $_POST["show_type"];
		switch($_POST["show_type"]){
			case 1:
				$zone->display	= "1";
				break;
			case 2:
				$zone->display	= "1";
				break;
			case 3:
				$zone->display	= "2";
				break;
			case 4:
				$zone->display	= "3";
				break;
			case 5:
				$zone->display	= "3";
				break;
			case 6:
				$zone->display	= "4";
				break;
			case 7:
				$zone->display	= "5";
				break;
			case 8:
				$zone->display	= "6";
				break;
			case 9:
				$zone->display	= "6";
				break;
			default:
				break;
		}
		$zone->intelligence	= 0;
		$zone->status		= 0;
		$zone->uid			= 320000;
		$zone->updatedate	= time();
		// 往数据库中添加
		$flag = $zone->add();
		if($flag){
			$this->success('数据添加成功','SITE_URL/web.php/?m=ZoneWeb&a=zone_list');
		}else{
			$this->error("数据添加失败",'SITE_URL/web.php/?m=ZoneWeb&a=zone_add');
		}
	}
	public function zone_edit(){
		$this	->assign("title","编辑代码位");
		$id 	= $_GET["zone_id"];
		$zo  	= M("zone");
		$zone 	= $zo->where("id=".$id)->select();
		$this	->assign("zone",$zone);
		$this	->display();
	}
	public function editCheck(){
		// 创建数据库对象
		$zo 	 			= M('zone');
		$zone['id']			= $_POST["site_id"];
		$zone['name']		= $_POST["site_name"];
		$zone['domain']		= $_POST["site_domain"];
		$zone['type']		= $_POST["site_type"];
		$zone['description']= $_POST["description"];
		// 更改数据库数据
		$site->where("id =".$_POST["site_id"])->data($st)->save();
		$this->success('数据更改成功','SITE_URL/web.php/?m=SiteWeb&a=index');
	}
	public function zone_delete(){
		$status	= $_GET["status"];
		$id		= $_GET["zone_id"];
		$zone  	= M("zone");
		if($status ==0){
			$zone->where("id =".$id)->setField("status","1");
		}else{
			$zone->where("id =".$id)->setField("status","0");
		}
		$this	->success('状态修改成功','SITE_URL/web.php/?m=ZoneWeb&a=index');
	}
	public function get_code(){
		$this->display();
	}
}