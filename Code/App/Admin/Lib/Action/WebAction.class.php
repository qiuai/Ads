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
	// 网站列表
    public function index(){
		$status	  	= (int)($_GET["status"]); // 获取状态信息
		if(empty($status)){
			$where 	= "1"; // 全部状态
		}else{
			$where 	= "status=".$status;
		}
		$st			= M('site');
		$site		= $this->memberPage($st, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$stp 		= M("site_type");
		foreach($site as $key =>$val){ 
			$site[$key]["addtime"] = date("Y-m-d H:i:s",$val["addtime"]); // 添加时间
			$sitetype= $stp->where("id =".$site[$key]['site_type'])->select(); // 处理分类
			foreach($sitetype as $keys =>$value){
				$site[$key]["code_name_zh"]= $value["code_name_zh"];
			}
		}
		$this		->assign("status",$status);
		$this		->assign("site",$site);
		$this		->display();
	}
	// 按条件查询网站
	public function siteSearch(){
		$content	= $_GET["content"]; // 查询条件
		$condition	= $_GET["condition"]; // 查询条件类型
		switch($condition){
			case "site_id": // 按网站ID查询
				$where = "id =".$content;
			break;
			case "site_domain": // 按网站域名查询
				$where = "site_domain ='".$content."'";
			break;
			case "uid": // 按用户ID查询
				$where = "uid =".$content;
			break;
			default:
			break;
		}
		$st 		= M("site");
		$site		= $this->memberPage($st, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$stp 		= M("site_type");
		foreach($site as $key =>$val){ 
			$site[$key]["addtime"] = date("Y-m-d H:i:s",$val["addtime"]); // 添加时间
			$sitetype= $stp->where("id =".$site[$key]['site_type'])->select(); // 处理分类
			foreach($sitetype as $keys =>$value){
				$site[$key]["code_name_zh"]= $value["code_name_zh"]; // 分类名称
			}
		}
		$this		->assign("content",$content);
		$this		->assign("condition",$condition);
		$this		->assign("site",$site);
		$this		->display(index); // 调用首页模板
	}
	// 导出网站列表
	public function siteExport(){
		// 报表名称
		$filename		= "网站列表_".date("Y-m-d");
		// 输出内容
		$st				= M("site");
		$sp				= M("site_type");
		$status	  		= (int)($_GET["status"]); // 获取状态信息
		if(empty($status)){ // 按关键字搜索
			$content	= $_GET["content"]; // 搜索关键字
			$condition	= $_GET["condition"]; // 搜索类型，网站id，网站域名，网站主id
			if(empty($content)){
				$where 		= "1"; // 全部状态
			}else{
				switch($condition){
					case "site_id": // 按网站ID查询
						$where = "id =".$content;
					break;
					case "site_domain": // 按网站域名查询
						$where = "site_domain ='".$content."'";
					break;
					case "uid": // 按用户ID查询
						$where = "uid =".$content;
					break;
					default:
					break;
				}
			}
		}else{ // 按网站状态搜索
			$where		= "status=".$status; 
		}
		$site	 	  	= $st->where($where)->select();
		$ReportArr	  	= array(); // 组建内容输出数组
		// 将关系数组转换成索引数组
		foreach($site as $key =>$val){
			$ReportArr[$key][]	= $val["id"]; // 网站ID
			$ReportArr[$key][]	= $val["site_name"]; // 网站名称
			$ReportArr[$key][]	= $val["site_domain"]; // 网站域名
			$type=$sp->where("id=".$val["site_type"])->field("code_name_zh")->select(); // 查询网站类型
			$ReportArr[$key][]	= $type[0]["code_name_zh"]; // 网站类型
			$ReportArr[$key][]	= $val["uid"]; // 网站主ID
			$status			  	= C("SITE_STATUS"); // 获取网站状态
			$ReportArr[$key][]	= $status[$val["status"]]; // 网站状态		
		}
		$HeaderArr		= array();    // 组建excel表头数组
		$HeaderArr[]	= "网站ID";   // 判断是否要导出网站ID信息 
		$HeaderArr[]	= "网站名称"; // 判断是否要导出网站名称信息
		$HeaderArr[]	= "网站域名"; // 判断是否要导出网站域名信息
		$HeaderArr[]	= "网站类型"; // 判断是否要导出网站类型信息
		$HeaderArr[]	= "网站主ID"; // 判断是否要导出网站主ID信息
		$HeaderArr[]	= "网站状态"; // 判断是否要导出网站状态信息
		// 下载excel表方法
		$this->downloadExcel($filename,$ReportArr,$HeaderArr);
	}
	// 删除网站
	public function siteDelete(){
		$id		= (int)($_GET["site_id"]);
		$site 	= M("site");
		$site	->where("id =".$id)->delete();
		$this	->success('删除成功','SITE_URL?m=Web&a=index');
	}
	// 网站分类列表
	public function siteType(){
		$st 		= M("site_type");
		$siteType 	= $st ->select();
		$this		->assign("sitetype",$siteType);
		$this		->display();
	}
	// 批量操作，改变网站状态
	public function siteMulti(){
		$ids 		= $_POST["ids"]; // 得到选中的ID
		$ids 		= rtrim($ids,","); 
		$status 	= (int)($_POST["status"]);
		$st 		= M("site");
		$num 		= $st->where("id in (".$ids.")")->setField("status",$status); // 批量改变网站状态
		if(empty($num)){
			echo "1"; // 失败
		}else{
			echo "0"; // 成功
		}
	}
	// 编辑网站分类
	public function siteTypeEdit(){
		$id 		= (int)($_GET["code_id"]); // 得到网站分类ID
		$st 		= M("site_type");
		$siteType 	= $st ->where("id = ".$id)->select();
		$this		->assign("sitetype",$siteType);
		$this		->display();
	}
	// 添加站点分类
	public function addCheck(){
		$siteType			=	M('site_type');
		$data				=	array();
		$data["code_name_zh"]= 	$_POST["code_name_zh"]; // 站点分类名称
		$data["code_name_en"]=	$_POST["code_value"]; // 站点分类英文名称
		$data["status"]		=	(int)($_POST["status"]); // 是否显示
		$data["sort"]		=	(int)($_POST["sort"]); // 排序
		// 验证录入信息
		if(empty($data["code_name_zh"])){
			$this->error("分类名称不能为空！",'SITE_URL?m=Web&a=siteTypeAdd');
		}elseif(empty($data["code_name_en"])){
			$this->error("英文名称不能为空！",'SITE_URL?m=Web&a=siteTypeAdd');
		}else{
			$siteType->data($data)->add(); // 往数据库中添加
			$this->success('数据添加成功','SITE_URL?m=Web&a=siteType');
		}
	}
	// 编辑站点分类
	public function editCheck(){
		$siteType			=	M('site_type');
		$data['id']			=	(int)($_POST["code_id"]); // 获取网站分类ID
		$data['code_name_zh']=	$_POST["code_name_zh"];
		$data['code_name_en']=	$_POST["code_value"];
		$data['status']		=	(int)($_POST["status"]);
		$data['sort']		=	(int)($_POST["sort"]);
		// 验证录入信息
		if(empty($data["code_name_zh"])){
			$this->error("分类名称不能为空！",'SITE_URL?m=Web&a=siteTypeEdit&code_id='.$data['id']);
		}elseif(empty($data["code_name_en"])){
			$this->error("英文名称不能为空！",'SITE_URL?m=Web&a=siteTypeEdit&code_id='.$data['id']);
		}else{
			$siteType->where("id =".$data['id'])->data($data)->save(); // 更改数据
			$this->success('数据更改成功','SITE_URL?m=Web&a=siteType');
		}
	}
	// 代码位列表
	public function zone(){
		$zo 		= M('zone');
		$ad_size_type= C("AD_SIZE_TYPE"); // 获取代码位类型
		$site_id	= (int)($_GET["site_id"]); // 获取所属网站id
		if(empty($site_id)){
			$where	= "";
		}else{
			$where	= "sid=".$site_id;
		}
		$zone		= $this->memberPage($zo, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$ads		= M("ad_size"); // 查询广告尺寸表
		foreach($zone as $key =>$val){
			$adsize	= $ads->where("id=".$zone[$key]["size"])->select(); // zone表size关联ad_size表id
			foreach($adsize as $keys => $value){
				$zone[$key]["width"]	= $value["width"]; // 代码位尺寸宽
				$zone[$key]["height"]	= $value["height"]; // 代码位尺寸高 
				$zone[$key]["display"]	= $ad_size_type[$value['size_type']]; // 代码位类型 
			}
		}
		$this		->assign("zone",$zone);
		$this		->display();
	}
	// 按条件查询代码位
	public function zoneSearch(){
		$ad_size_type= C("AD_SIZE_TYPE"); // 获取代码位类型 
		$content	= (int)($_GET["content"]); // 查询条件
		$condition	= $_GET["condition"]; // 查询条件类型
		switch($condition){
			case "zone_id": // 按网站ID查询
				$where = "id =".$content;
			break;
			case "site_id": // 按网站域名查询
				$where = "sid =".$content;
			break;
			case "uid": // 按用户ID查询
				$where = "uid =".$content;
			break;
			default:
			break;
		}
		$zo 		= M("zone");
		$zone		= $this->memberPage($zo, $where, $pageNum=15, $order='id'); // 分页方法(数据库对象,查询条件,每页显示个数,排序字段)
		$ads 		= M("ad_size");
		foreach($zone as $key =>$val){
			$ad_size= $ads->where("id =".$zone[$key]['size'])->select();
			foreach($ad_size as $keys =>$value){
				$zone[$key]["width"]	=	$value["width"];
				$zone[$key]["height"]	=	$value["height"];
				$zone[$key]["display"]	= 	$ad_size_type[$value['size_type']]; // 代码位类型 
			}
		}
		$this		->assign("content",$content);
		$this		->assign("condition",$condition);
		$this		->assign("zone",$zone);
		$this		->display(zone); // 调用代码位列表模板
	}
	// 导出代码位报表
	public function zoneExport(){
		// 报表名称
		$filename		= "代码位列表_".date("Y-m-d");
		// 下载内容
		$zo				= M("zone");
		$ad_size		= M("ad_size");
		// 搜索条件
		$content		= (int)($_GET["content"]); // 查询条件
		$condition		= $_GET["condition"]; // 查询条件类型
		switch($condition){
			case "zone_id": // 按网站ID查询
				$where	= "id =".$content;
			break;
			case "site_id": // 按网站域名查询
				$where	= "sid =".$content;
			break;
			case "uid": // 按用户ID查询
				$where	= "uid =".$content;
			break;
			default:
			break;
		}
		$zone	 	  	= $zo->where($where)->select();
		$ReportArr	  	= array();
		$ad_pay_type	= C("AD_PAY_TYPE"); // 广告计费类型
		$ad_size_type	= C("AD_SIZE_TYPE"); // 获取代码位类型 
		// 将关系数组转换成索引数组
		foreach($zone as $key =>$val){
			$ReportArr[$key][]	=	$val["id"]; // 代码位ID
			$ReportArr[$key][]	=	$val["name"]; // 代码位名称
			$ReportArr[$key][]	=	$val["sid"]; // 所属网站ID
			$ReportArr[$key][]	=	$val["uid"]; // 所属用户ID
			$ReportArr[$key][]	=	$ad_pay_type[$val["pay_type"]]; // 广告计费类型
			$ad					=	$ad_size->where("id=".$val["size"])->select(); // 查询广告尺寸信息
			$ReportArr[$key][]	=	$ad_size_type[$ad[0]["size_type"]]; // 代码位类型
			$ReportArr[$key][]	=	$ad[0]["width"]."X".$ad[0]["height"]; // 代码位尺寸
			$ReportArr[$key][]	=	$val["auto_ad"]; // 智能广告
		}
		$HeaderArr		= array();		// 组建excel表头数组
		$HeaderArr[]	= "代码位ID";	// 判断是否要导出代码位ID信息 
		$HeaderArr[]	= "代码位名称";	// 判断是否要导出代码位名称信息
		$HeaderArr[]	= "所属网站ID";	// 判断是否要导出所属网站ID信息
		$HeaderArr[]	= "所属用户ID";	// 判断是否要导出所属用户ID信息
		$HeaderArr[]	= "计费类型";	// 判断是否要导出计费类型信息
		$HeaderArr[]	= "展示方式";	// 判断是否要导出展示方式信息
		$HeaderArr[]	= "尺寸";		// 判断是否要导出尺寸信息
		$HeaderArr[]	= "智能广告";	// 判断是否要导出智能广告信息
		// 下载Excel方法
		$this->downloadExcel($filename,$ReportArr,$HeaderArr);
	}
	/**
	 * 进入会员中心
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-12 下午5:32:59
	 */
	public function loginMemberHome(){
		$model			= M("Member"); // 会员信息表
		$id				= $_REQUEST['uid']; // 网站主ID
		$member			= $model->find($id); // 查找网站主账号密码
		$zone_id		= (int)($_GET["zone_id"]); // 代码位ID
		if(!empty($member)){
			redirect(C('HOME_URL').'?m=Public&a=adminLogin&username=' . $member['username'] . '&password=' . $member['password'].'&zone_id='.$zone_id); // 传参验证登入信息
		}
	}
}