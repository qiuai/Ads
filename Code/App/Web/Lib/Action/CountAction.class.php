<?php

/**
 * 
 * 网站访问计数专用控制器
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2013-12-17 下午8:37:12
 * @version 1.0
 */
class CountAction extends Action{
	
	/**
	 * 
	 * 广告展示计数对应的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-17 下午8:39:00
	 */
	public function index(){
		
		
		header("Content-type: text/html; charset=utf-8");
		
		
		
		
		//dump($_SERVER['HTTP_REFERER']);
		
		// 获取当前客户端访问useragent
		
		// 获取提交过来的代码位信息
		$_GET['id'] = $_GET['zone'];
		$this->dealSubmitData();
		
		// 查询相关的信息随机生成广告信息
		$zone = M("Zone");
		
		// 查询代码位相关的信息必须是启用状态的代码位
		$zoneInfo = $zone->where("id = ".$_GET['id']." and status = 1")->find();
		
		// 处理客户端访问的来源问题 如果和申请广告时的来源地址不同则不能投放
		$this->verifyVisitSource($zoneInfo);
		
		if(!$zoneInfo){   // 没有查询到数据代表代码位不存在或未启用
			echo "当前代码位有误 或未启用";
			exit;
		}else{
			
			// 代码执行到这里说明本次访问的来源网站是正确的 并且当前代码为已经启用 
			// 组装广告代码展示	

			$code = $this->createAdCode($zoneInfo['size']);
			echo $code;
			
			if($code){		// 服务器端开始计录本次访问
												
				// 往数据zhts_zone_visit中添加数据
				$this->addZoneVisit();				
			}
		
		}
		//echo "document.write('<a href=\" \" target=>".$adManageInfo['content']."</a>');";
		
		
		// 获取当前广告的id值
		
		// 获取当前广告位网站主的id值
		
		
		// 往计数表中添加数据
			
		
		
		/*if(!$_SESSION['uid']){
			$_SESSION['uid'] = rand(1,1000000);
		}
		dump($_SESSION);
		dump($_COOKIE);
		file_put_contents("d:/jjjjj.txt", $_COOKIE['PHPSESSID']);*/
	}
	
	/**
	 * 
	 * 往代码位记录表中添加数据
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-18 下午8:00:36
	 */
	private function addZoneVisit(){
		
		// 创建zone_visit句柄
		$zoneVisit = M('ZoneVisit');
		
		// 获取当前客户端的ip
		$cip = $this->getIp();
		
		// 往数据表中添加一条浏览的数据
		$data['visit_ip']  = $cip; // 记录客户端ip
		$data['view_or_click'] = 1; // 表示浏览
		$data['visit_time'] = time(); // 本次访问的时间戳
		$data['zid'] = $_GET['id']; // 当前广告为的id
		
		// 往zone_visit表添加记录
		$zoneVisit->add($data);
				
	}
	
	/**
	 * 
	 * 往代码位访问计数表中添加数据 
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-18 下午8:24:32
	 */
	private function addZoneVisitCount(){
		
		// 创建zone_visit_count句柄
		$zoneVisitCount = M('ZoneVisitCount');
		
		// 调用函数创建当前0时0分0秒的时间戳
		$dayStartTime = $this->createDayStartTime();
		
		// 定义变量保存第二天0时0分0秒的时间戳
		$tomorrowStartTime = $dayStartTime+3600*24;
		
		
		//$data['view_ip_num'] = $this->getIp();
		
		
		
		/**
		 * 
		 * 查询zone_visit_count表看看是否数据库中存在符合zid为当前代码位id 并且day_start_time为当天0时0分0秒的数据 如果没有则往
		 * zone_visit_count中添加一条数据并且把view_pv_num值设为1 view_ip_num值为1 zid 为当前广告的代码位的id值 
		 * click_pv_num 值为0 click_ip_num 值为0 day_start_time 为当天0时0分0秒的时间
		 * 总之同一个zid(即同一个代码为在同一天在zone_visit_count只有一条记录)
		 */
		// 组装查询条件的数据
		$data['day_start_time'] = $dayStartTime;
		$data['zid'] = $_GET['id'];
		$zoneVisitCountInfo = $zoneVisitCount->where($data)->find();
		if(!$zoneVisitCountInfo){
			
			// 组装添加的数据
			$insertData = array();
			$insertData['day_start_time'] = $dayStartTime;
			$insertData['zid'] = $_GET['id'];
			$insertData['view_pv_num'] = 1;
			$insertData['view_ip_num'] = 1;
			$insertData['click_pv_num'] = 0;
			$insertData['click_ip_num'] = 0;
			
			// 往数据库中添加数据
			$zoneVisitCount->add($insertData);
		}else{
		
			/**
			 * 查询zone_visit_count表看看是否数据库中存在符合zid为当前代码位id 并且day_start_time为当天0时0分0秒的数据 如果有则表明
			 * 当前代码位当天的记录已经存在 则查询zone_visit表看看是否数据库中存在符合zid为当前代码位id
			 * 并且时间在当天0时0分0秒到第二天0时0分0秒之间 并且view_or_click为1（即浏览）并且ip为当前客户端ip的数据 如果存在则
			 * 在当前的数据上面view_pv_num加1,view_ip_num不变 如果不存在则view_pv_num加1,view_ip_num也加1
			 */
			
			// 组装查询条件
			$data = array();
			//$data['day_start_time'] = $dayStartTime;
			$data['zid'] = $_GET['id'];
			$data['view_or_click'] = 1;
			$data['visit_time'] = array(array("egt",$dayStartTime),array("lt",$tomorrowStartTime),'and');
			$data['visit_ip'] = $this->getIp(); 
			
			// 定义zone_visit句柄
			$zoneVisit = M('ZoneVisit');
			
			// 查询数据
			$zoneVisitInfo = $zoneVisit->where($data)->find();
			
			if($zoneVisitInfo){
				
				// 组装数据库中更新的数据
				$updateData = array();
				$updateData['id'] = $zoneVisitInfo['id'];
			//	$updateData['view_pv_num'] = 
			}
								
		}
		// 查询zone_visit表看看是否数据库中存在符合zid为当前代码位id 并且时间在当天0时0分0秒到第二天0时0分0秒之间 并且view_or_click为1（即浏览）并且ip为当前客户端ip的数据 如果存在则
		
		
		// 根据上面组装的条件如果数据库中已经有记录  说明当前的ip当天浏览
		
	}
	/**
	 * 
	 * 创建当天0时0分0秒时的时间戳
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-18 下午8:40:37
	 */
	public function createDayStartTime(){
		
		// 获取当前的年
		$year = date("Y",time());
		
		// 获取当前的月
		$month = date("m",time());
		
		// 获取当前的天
		$day = date("d",time());
		
		return mktime(0,0,0,$month,$day,$year);
	}
	
	/**
	 * 
	 * 判断当前的访问是否来源于广告主在代码位申请的时候填写的网站，如果来源不正确则直接退出正确才进行下面的展示广告和计数的问题
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-18 下午5:23:52
	 */
	private function verifyVisitSource($zoneInfo){
		
		// 根据$zoneInfo中的sid值查询网站的信息
		$site = M("Site");
		
		// 查询当前代码位所对应的网站域名
		$siteInfo = $site->where("id = ".$zoneInfo['sid'])->find();
		
		
		if(!$siteInfo['site_domain']){
			
			// 申请的代码位时网站主未填写用来投放的网站域名 
			exit;
		}else{
			
			// 正则匹配 判断本次访问的来源是否与网站主填写的网址相匹配
			if(!preg_match('/^((http)|(ftp)|(https))\:\/\/'.$siteInfo['site_domain'].'/is', $_SERVER['HTTP_REFERER'])){
				
				 // 访问来源有误
				exit;
			}
		}
	}
	
	/**
	 * 
	 * 具体生成广告代码的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-18 下午2:38:34
	 */
	private  function createAdCode($sizeId){
		
		
		// 查询当前广告的宽度和高度
		$adSize = M("AdSize");

		// 根据尺寸id值查询相关的广告信息
		$adSizeInfo = $adSize->where('id = '.$sizeId)->find();
		
		// 根据查询出代码位中的信息中的尺寸值随机查询当前尺寸的广告
		$adManage = M("adManage");
		$adManageInfo = $adManage->where("show_type = ".$sizeId." and status = 2")->order("rand()")->find();
		
		if($adManageInfo){
			
			// 组装div框中的图片或文字的广告
			
		
			$code = "document.write('<style>*{margin:0px;padding:0px;border:0px;}</style>";
			/*$code.= "<iframe width=\'".$adSizeInfo['width']."\' scrolling=\"no\" height=\"".$adSizeInfo['height']."\" frameborder=\"0\" align=\"center,center\" allowtransparency=\"true\" marginheight=\"0\" marginwidth=\"0\" src=\"./index3.html\" ></iframe>";*/
			$code.="<div width=\'".$adSizeInfo['width']."\' height=\'".$adSizeInfo['height']."\' ><a href=\'".$adManageInfo['jump_url']."\' target=\"_blank\" >".$adManageInfo['content']."</a></div>";
			$code=$code."');";
			return $code;
		}else{
			
			return "没有适合当前代码位中所定义的尺寸大小的广告";
		}
	}
	
	//public function createAdCode
	/**
	 * 
	 * 处理提交过来的数据
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-17 下午8:52:22
	 */
	private function dealSubmitData(){
	
		// 如果有提交过来id值则把id值转化为整型
		if($_POST['aid']){
			$_POST['aid'] = intval($_POST['aid']);
		}
	
		if($_GET['aid']){
			$_GET['aid'] = intval($_GET['aid']);
		}
	
	}
	
	/**
	 * 
	 * 获取当前访问的ip
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-18 下午7:46:13
	 */
	public function getIp(){
		if(!empty($_SERVER["HTTP_CLIENT_IP"])){
			$cip = $_SERVER["HTTP_CLIENT_IP"];
		}
		elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif(!empty($_SERVER["REMOTE_ADDR"])){
			$cip = $_SERVER["REMOTE_ADDR"];
		}
		else{
			$cip = "无法获取！";
		}
		return $cip;
	}		
}