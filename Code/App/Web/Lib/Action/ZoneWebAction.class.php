<?php
/**
 * 广告联盟系统  代码为管理
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
	// 代码位列表
    public function index(){ 
		$this		->assign("title","代码位管理");
		$uid		= $_SESSION[C("WEB_AUTH_KEY")];
		$zo 		= M('zone');
		$where		= "uid=".$uid;
		$zone		= $this->memberPage($zo, $where, $pageNum=15, $order='id desc'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$as			= M('ad_size');
		foreach($zone as $key=>$val){
			$zone[$key]["refresh_time"]	= date("Y-m-d",$val["refresh_time"]); // 更新时间
			$adsize	= $as->where("id=".$val['size'])->select(); // 查询代码位类型表
			foreach($adsize as $keys=>$value){
				$zone[$key]['display']	= $value['size_type']; // 代码位类型
				$zone[$key]['width']	= $value['width']; // 代码位宽
				$zone[$key]['height']	= $value['height']; // 代码位高
			}
		}
		$this		->assign("zone",$zone);
		$this		->display();
    }
	// 添加代码位
	public function zoneAdd(){
		$this	->assign("title","新增代码位");
		$st		= M('site');
		$uid	= $_SESSION[C("WEB_AUTH_KEY")]; // 获取网站主ID
		$site	= $st->where("uid=".$uid." and status=3")->field("id,site_domain")->select(); // 查找出属于该网站主下的正常状态网站的网站id，网站域名
		$this   ->assign("site",$site); 
		$ads	= M('ad_size');
		$adsize = $ads->select();
		$ad_size_type	= C("AD_SIZE_TYPE"); // 获取代码为类型		
		foreach($adsize as $key=>$val){
			$adsize[$key]["size_type"]=$ad_size_type[$val['size_type']];
		}
		$this   ->assign("adsize",$adsize);
		$this	->display();
	}
	// 处理添加代码位
	public function addCheck(){
		$zone 					= M('zone');
		$data["name"]			= $_POST["zone_name"]; // 代码位名称
		$data["sid"]			= (int)($_POST["site_id"]); // 所属网站ID
		$data["pay_type"]		= (int)($_POST["pay_type"]); // 计费类型（1CPM 2CPC）
		$data["size"]			= (int)($_POST["show_type"]); // 代码位尺寸
		$data["uid"]			= $_SESSION[C("WEB_AUTH_KEY")]; // 网站主ID
		$data["refresh_time"]	= time(); // 更新时间
		if(empty($data["name"])){
			$this->error("代码位名称不能为空！",'WEB_URL?m=ZoneWeb&a=zoneAdd');
		}else{
			$zone->data($data)->add();
			$this->success('代码位添加成功','WEB_URL?m=ZoneWeb&a=index');
		}
	}
	// 编辑代码位
	public function zoneEdit(){
		$this	->assign("title","编辑代码位");
		$id 	= (int)($_GET["zone_id"]); // 获取代码位id
		$zo  	= M("zone");
		$ads  	= M("ad_size");
		$zone 	= $zo->where("id=".$id)->select(); // 查找选中的代码位
		$adsize = $ads->where("id=".$zone[0]["size"])->select(); // 所属的代码位类型
		$ad_size_type	= C("AD_SIZE_TYPE"); // 获取代码为类型
		foreach($adsize as $key=>$val){
			$adsize[$key]["size_type"]=$ad_size_type[$val['size_type']];
		}
		$this	->assign("zone",$zone);
		$this	->assign("adsize",$adsize);
		$this	->display();
	}
	// 处理编辑代码位
	public function editCheck(){
		$zone 	= M('zone');
		$id		= (int)($_POST["zone_id"]);
		$name	= $_POST["zone_name"];
		$uid1	= $_SESSION[C("WEB_AUTH_KEY")];
		$uid2	= $zone->field("uid")->where("id=".$id)->find();
		if($uid1!=$uid2){ // 判断编辑代码位网站主与代码位网站主是否为同一个人
			$this	->error("数据异常！",'WEB_URL?m=ZoneWeb&a=zoneEdit&zone_id='.$id);
		}elseif(empty($name)){
			$this	->error("代码位名称不能为空！",'WEB_URL?m=ZoneWeb&a=zoneEdit&zone_id='.$id);
		}else{
			$zone	->where("id =".$id)->setField("name",$name);
			$this	->success('代码位更改成功','WEB_URL?m=ZoneWeb&a=index');
		}
	}
	// 编辑代码位状态（0启用、1停用）
	public function zoneDelete(){
		$status	= (int)($_GET["status"]);
		$id		= (int)($_GET["zone_id"]);
		$zone  	= M("zone");
		if($status ==0){
			$zone->where("id =".$id)->setField("status","1"); // 停用
		}else{
			$zone->where("id =".$id)->setField("status","0"); // 启用
		}
		$this	->success('状态修改成功','WEB_URL?m=ZoneWeb&a=index');
	}
	// 获取代码
	public function getCode(){
		$this->display();
	}
}