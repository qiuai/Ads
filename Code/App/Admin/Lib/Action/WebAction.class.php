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
		$status	   = (int)($_GET["status"]); // 获取状态信息
		if(empty($status)){
			$where = "1"; // 全部
		}else{
			$where = "status=".$status;
		}
		$st			= M('site');
		import('ORG.Util.Page'); // 调用分页类
		$count		= $st->where($where)->count();
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
		$site     	= $st->where($where)->order('id')->page($nowPage.','.$Page->listRows)->select();
		$this		->assign('page',$show);	
		$this		->assign('count',$count); // 记录个数
		$stp 		= M("site_type");
		foreach($site as $key =>$val){ 
			$site[$key]["addtime"] = date("Y-m-d H:i:s",$val["addtime"]); // 添加时间
			$sitetype= $stp->where("id =".$site[$key]['site_type'])->select(); // 处理分类
			foreach($sitetype as $keys =>$value){
				$site[$key]["code_name_zh"]= $value["code_name_zh"];
			}
		}
		$this		->assign("site",$site);
		$this		->display();
	}
	// 按条件查询网站
	public function site_search(){
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
		$site       = $st->where($where)->select();
		$stp 		= M("site_type");
		foreach($site as $key =>$val){ 
			$site[$key]["addtime"] = date("Y-m-d H:i:s",$val["addtime"]); // 添加时间
			$sitetype= $stp->where("id =".$site[$key]['site_type'])->select(); // 处理分类
			foreach($sitetype as $keys =>$value){
				$site[$key]["code_name_zh"]= $value["code_name_zh"];
			}
		}
		$this		->assign("content",$content);
		$this		->assign("condition",$condition);
		$this		->assign("site",$site);
		$this		->display(index);
	}
	// 导出网站列表报表
	public function siteExport(){
		// 输出的文件类型为excel
		header("Content-type:application/vnd.ms-excel");
		// 提示下载
		header("Content-Disposition:attachement;filename=网站列表_".date("Y-m-d").".xls");
		// 查询提现申请表
		$st				= M("site");
		$sp				= M("site_type");
		$site	 	  	= $st->select();
		$ReportArr	  	= array();
		// 将关系数组转换成索引数组
		foreach($site as $key =>$val){
			$ReportArr[$key][]=$val["id"]; // 网站ID
			$ReportArr[$key][]=$val["site_name"]; // 网站名称
			$ReportArr[$key][]=$val["site_domain"]; // 网站域名
			$type=$sp->where("id=".$val["site_type"])->field("code_name_zh")->select();// 查询网站类型
			$ReportArr[$key][]=$type[0]["code_name_zh"]; // 网站类型
			$ReportArr[$key][]=$val["uid"]; // 网站主ID
			switch($val["status"]){// 处理网站状态
				case 0:
					$status ="未验证";
				break;
				case 1:
					$status ="审核中";
				break;
				case 2:
					$status ="正常";
				break;
				case 3:
					$status ="锁定";
				break;
				case 4:
					$status ="拒绝";
				break;
				default:
				break;
			}
			$ReportArr[$key][]=$status; // 网站状态		
		}
		// 报表数据
		$ReportContent = '';
		$num1 = count($ReportArr);
		for($i=0;$i<$num1;$i++){
			$num2 = count($ReportArr[$i]);
			for($j=0;$j<$num2;$j++){
				// ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
				$ReportContent .= '"'.$ReportArr[$i][$j].'"'."\t";
			}
			// 最后连接\n 表示换行
			$ReportContent .= "\n";
		}
		$t[]="网站ID";// 判断是否要导出网站ID信息 
		$t[]="网站名称";// 判断是否要导出网站名称信息
		$t[]="网站域名";// 判断是否要导出网站域名信息
		$t[]="网站类型";// 判断是否要导出网站类型信息
		$t[]="网站主ID";// 判断是否要导出网站主ID信息
		$t[]="网站状态";// 判断是否要导出网站状态信息
		for($k=0;$k<count($t);$k++){
			// ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
			$ReportTitle .= '"'.$t[$k].'"'."\t";
		}
		// 输出即提示下载
		echo $ReportTitle."\n".$ReportContent;
	}
	// 删除网站
	public function site_delete(){
		$id		= (int)($_GET["site_id"]);
		$site 	= M("site");
		$site	->where("id =".$id)->delete();
		$this	->success('删除成功','SITE_URL/?m=Web&a=index');
	}
	// 网站分类列表
	public function site_type(){
		$st = M("site_type");
		$siteType = $st ->select();
		$this->assign("sitetype",$siteType);
		$this->display();
	}
	// 批量操作，改变网站状态
	public function site_multi(){
		$ids 	= $_POST["ids"]; // 得到选中的ID
		$ids 	= rtrim($ids,","); 
		$status = (int)($_POST["status"]);
		$st 	= M("site");
		$num 	= $st->where("id in (".$ids.")")->setField("status",$status); // 批量改变网站状态
		if(empty($num)){
			echo "1"; // 失败
		}else{
			echo "0"; // 成功
		}
	}
	// 编辑网站分类
	public function site_type_edit(){
		$id 		= (int)($_GET["code_id"]); // 得到网站分类ID
		$st 		= M("site_type");
		$siteType 	= $st ->where("id = ".$id)->select();
		$this		->assign("sitetype",$siteType);
		$this		->display();
	}
	// 代码位列表
	public function zone(){
		$zo 		= M('zone');
		import('ORG.Util.Page'); // 调用分页类
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
		$ads		= M("ad_size"); // 查询广告尺寸表
		foreach($zone as $key =>$val){
			$adsize	= $ads->where("id=".$zone[$key]["size"])->select(); // zone表size关联ad_size表id
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
	// 添加站点分类
	public function addCheck(){
		$siteType 			= M('site_type');
		$siteType->code_name_zh	= $_POST["code_name_zh"]; // 站点分类名称
		$siteType->code_name_en	= $_POST["code_value"]; // 站点分类英文名称
		$siteType->status	= (int)($_POST["status"]); // 是否显示
		$siteType->sort		= (int)($_POST["sort"]); // 排序
		// 往数据库中添加
		$flag = $siteType->add();
		if($flag){
			$this->success('数据添加成功','SITE_URL/?m=Web&a=site_type');
		}else{
			$this->error("数据添加失败",'SITE_URL/?m=Web&a=site_type_add');
		}
	}
	// 编辑站点分类
	public function editCheck(){
		$siteType 		= M('site_type');
		$st['id']		= (int)($_POST["code_id"]);
		$st['code_name_zh']	= $_POST["code_name_zh"];
		$st['code_name_en']	= $_POST["code_value"];
		$st['status']	= (int)($_POST["status"]);
		$st['sort']		= (int)($_POST["sort"]);
		$siteType->where("id =".$_POST["code_id"])->data($st)->save();
		$this->success('数据更改成功','SITE_URL/?m=Web&a=site_type');
	}
	// 按条件查询代码位
	public function zoneSearch(){
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
		$zone       = $zo->where($where)->select();
		$ads 		= M("ad_size");
		foreach($zone as $key =>$val){
			$ad_size= $ads->where("id =".$zone[$key]['size'])->select();
			foreach($ad_size as $keys =>$value){
				$zone[$key]["width"]	=	$value["width"];
				$zone[$key]["height"]	=	$value["height"];
				switch($value["size_type"]){
					case 1:
						$size_type="图片";
					break;
					case 2:
						$size_type="文字";
					break;
					case 3:
						$size_type="漂浮";
					break;
					case 4:
						$size_type="对联";
					break;
					case 5:
						$size_type="弹窗";
					break;
					case 6:
						$size_type="视窗";
					break;
					default:
					break;
				}
				$zone[$key]['display']	= $size_type;
			}
		}
		$this		->assign("content",$content);
		$this		->assign("condition",$condition);
		$this		->assign("zone",$zone);
		$this		->display(zone);
	}
	// 导出代码位报表
	public function zoneExport(){
		// 输出的文件类型为excel
		header("Content-type:application/vnd.ms-excel");
		// 提示下载
		header("Content-Disposition:attachement;filename=代码位列表_".date("Y-m-d").".xls");
		// 查询代码位表
		$zo				= M("zone");
		$ad_size		= M("ad_size");
		$zone	 	  	= $zo->select();
		$ReportArr	  	= array();
		// 将关系数组转换成索引数组
		foreach($zone as $key =>$val){
			$ReportArr[$key][]=$val["id"]; // 代码位ID
			$ReportArr[$key][]=$val["name"]; // 代码位名称
			$ReportArr[$key][]=$val["sid"]; // 所属网站ID
			$ReportArr[$key][]=$val["uid"]; // 所属用户ID
			switch($val["pay_type"]){
				case 1:
					$pay_type="CPS";
				break;
				case 2:
					$pay_type="CPA";
				break;
				case 3:
					$pay_type="CPC";
				break;
				case 4:
					$pay_type="CPM";
				break;
				default:
				break;
			}
			$ReportArr[$key][]=$pay_type; // 计费类型
			$ad=$ad_size->where("id=".$val["size"])->select();// 查询广告尺寸信息
			switch($ad[0]["size_type"]){
				case 1:
					$size_type="图片";
				break;
				case 2:
					$size_type="文字";
				break;
				case 3:
					$size_type="漂浮";
				break;
				case 4:
					$size_type="对联";
				break;
				case 5:
					$size_type="弹窗";
				break;
				case 6:
					$size_type="视窗";
				break;
				default:
				break;
			}
			$ReportArr[$key][]=$size_type; // 展示方式
			$ReportArr[$key][]=$ad[0]["width"]."X".$ad[0]["height"]; // 尺寸
			$ReportArr[$key][]=$val["auto_ad"]; // 智能广告
		}
		// 报表数据
		$ReportContent = '';
		$num1 = count($ReportArr);
		for($i=0;$i<$num1;$i++){
			$num2 = count($ReportArr[$i]);
			for($j=0;$j<$num2;$j++){
				// ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
				$ReportContent .= '"'.$ReportArr[$i][$j].'"'."\t";
			}
			// 最后连接\n 表示换行
			$ReportContent .= "\n";
		}
		$t[]="代码位ID";// 判断是否要导出代码位ID信息 
		$t[]="代码位名称";// 判断是否要导出代码位名称信息
		$t[]="所属网站ID";// 判断是否要导出所属网站ID信息
		$t[]="所属用户ID";// 判断是否要导出所属用户ID信息
		$t[]="计费类型";// 判断是否要导出计费类型信息
		$t[]="展示方式";// 判断是否要导出展示方式信息
		$t[]="尺寸";// 判断是否要导出尺寸信息
		$t[]="智能广告";// 判断是否要导出智能广告信息
		for($k=0;$k<count($t);$k++){
			// ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
			$ReportTitle .= '"'.$t[$k].'"'."\t";
		}
		// 输出即提示下载
		echo $ReportTitle."\n".$ReportContent;
	}
}