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
		$this		->assign("title","代码位管理");
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
		$as			= M('adsize');
		foreach($zone as $key=>$val){
			$zone[$key]["updatedate"]=date("Y-m-d",$val["updatedate"]);
			// 连表查询代码位尺寸展示类型
			$adsize	= $as->where("id=".$val['size'])->select();
			foreach($adsize as $keys=>$value){
				$zone[$key]['display']	=$adsize[$keys]['size_type'];
				$zone[$key]['width']	=$adsize[$keys]['width'];
				$zone[$key]['height']	=$adsize[$keys]['height'];
			}
		}
		$this		->assign('page',$show);
		$this		->assign('count',$count);
		$this		->assign("zone",$zone);
		$this		->display();
    }
	public function zone_add(){
		$this	->assign("title","新增代码位");
		$st		= M('site');
		$site	= $st->field("id,domain")->select();
		$this   ->assign("site",$site);
		$ads	= M('adsize');
		$adsize = $ads->select();
		foreach($adsize as $key=>$val){
			switch($val['size_type']){
				case 1:
					$adsize[$key]["size_type"]="图片";
				break;
				case 2:
					$adsize[$key]["size_type"]="漂浮";
				break;
				case 3:
					$adsize[$key]["size_type"]="对联";
				break;
				case 4:
					$adsize[$key]["size_type"]="文本";
				break;
				case 5:
					$adsize[$key]["size_type"]="右下角悬浮";
				break;
				case 6:
					$adsize[$key]["size_type"]="弹窗";
				break;
				default:
				break;
			}
		}
		$this   ->assign("adsize",$adsize);
		$this	->display();
	}
	public function addCheck(){
		// 创建数据库对象
		$zone 				= M('zone');
		$zone->name			= $_POST["zone_name"];
		$zone->sid			= $_POST["site_id"];
		$zone->cp			= $_POST["pay_type"];
		$zone->size			= $_POST["show_type"];
		$zone->intelligence	= 0;
		$zone->status		= 0;
		$zone->uid			= 320000;
		$zone->updatedate	= time();
		// 往数据库中添加
		$flag = $zone->add();
		if($flag){
			$this->success('数据添加成功','SITE_URL/?m=ZoneWeb&a=index');
		}else{
			$this->error("数据添加失败",'SITE_URL/?m=ZoneWeb&a=zone_add');
		}
	}
	public function zone_edit(){
		$this	->assign("title","编辑代码位");
		$id 	= $_GET["zone_id"];
		$zo  	= M("zone");
		$ads  	= M("adsize");
		$zone 	= $zo->where("id=".$id)->select();
		$adsize = $ads->where("id=".$zone[0]["size"])->select();
		foreach($adsize as $key=>$val){
			switch($val['size_type']){
				case 1:
					$adsize[$key]["size_type"]="图片";
				break;
				case 2:
					$adsize[$key]["size_type"]="漂浮";
				break;
				case 3:
					$adsize[$key]["size_type"]="对联";
				break;
				case 4:
					$adsize[$key]["size_type"]="文本";
				break;
				case 5:
					$adsize[$key]["size_type"]="右下角悬浮";
				break;
				case 6:
					$adsize[$key]["size_type"]="弹窗";
				break;
				default:
				break;
			}
		}
		$this	->assign("zone",$zone);
		$this	->assign("adsize",$adsize);
		$this	->display();
	}
	public function editCheck(){
		// 创建数据库对象
		$zo 	= M('zone');
		$id		= $_POST["zone_id"];
		$name	= $_POST["zone_name"];
		// 更改数据库数据
		$zo		->where("id =".$id)->setField("name",$name);
		$this	->success('数据更改成功','SITE_URL/?m=ZoneWeb&a=index');
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
		$this	->success('状态修改成功','SITE_URL/?m=ZoneWeb&a=index');
	}
	public function get_code(){
		$this->display();
	}
}