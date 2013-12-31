<?php
/**
 * 广告联盟系统  广告服务
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
class AdServiceAction extends Action {
	
	private $planId;	// 广告所属计划ID
	private $adId;		// 广告ID
	private $zoneId;	// 广告位ID
	private $adName;	// 广告名
	private $visitIp;	// 访问者IP
	
	
   /**
    * 初始化
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 下午4:14:52
    */
   function _initialize($zid){
   		
   }
   public function index(){
   		$this->adShow();
   }
   /**
    * $view_or_click 代表是展示还是点击 1 为代码位的展示 2 为点击
    * 往代码位记录表中添加数据
    * @author Yumao <815227173@qq.com>
    * @CreateDate: 2013-12-18 下午8:00:36
    */
   private function addZoneVisit($view_or_click){
   
   	// 创建zone_visit句柄
   	$zoneVisit = M('ZoneVisit');
   
   	// 获取当前客户端的ip
   	$cip = $this->getIp();
   
   	// 往数据表中添加一条浏览的数据
   	$data['visit_ip']  = $cip; // 记录客户端ip
   	$data['view_or_click'] = $view_or_click; // 1表示浏览 2表示点击
   	$data['visit_time'] = time(); // 本次访问的时间戳
   	if($view_or_click==1){
   		$data['zid'] = $_GET['id']; // 当前广告为的id
   	}elseif ($view_or_click==2){
   		$data['zid'] = intval($_GET['zoneId']);
   	}
   
   	// 往zone_visit表添加记录
   	$zoneVisit->add($data);
   
   }
   
   /**
    * $view_or_click 代表是展示还是点击 1 为代码的位的展示 2 为点击
    * 往代码位访问计数表中添加数据
    * @author Yumao <815227173@qq.com>
    * @CreateDate: 2013-12-18 下午8:24:32
    */
   private function addZoneVisitCount($view_or_click){
   
   	// 创建zone_visit_count句柄
   	$zoneVisitCount = M('ZoneVisitCount');
   
   	// 调用函数创建当前0时0分0秒的时间戳
   	$dayStartTime = $this->createDayStartTime();
   
   	// 定义变量保存第二天0时0分0秒的时间戳
   	$tomorrowStartTime = $dayStartTime+3600*24;
   
   	/**
   	 *
   	 * 查询zone_visit_count表看看是否数据库中存在符合zid为当前代码位id 并且day_start_time为当天0时0分0秒的数据 如果没有则往
   	 * zone_visit_count中添加一条数据并且把view_pv_num值设为1 view_ip_num值为1 zid 为当前广告的代码位的id值
   	 * click_pv_num 值为0 click_ip_num 值为0 day_start_time 为当天0时0分0秒的时间
   	 * 总之同一个zid(即同一个代码为在同一天在zone_visit_count只有一条记录)
   	 */
   	// 组装查询条件的数据
   	$data['day_start_time'] = $dayStartTime;
   	if($view_or_click==1){
   		$data['zid'] = $_GET['id']; // 当前广告为的id
   	}elseif ($view_or_click==2){
   		$data['zid'] = intval($_GET['zoneId']);
   	}
   
   	$zoneVisitCountInfo = $zoneVisitCount->where($data)->find();
   	if(!$zoneVisitCountInfo){
   			
   		// 组装添加的数据
   		$insertData = array();
   		$insertData['day_start_time'] = $dayStartTime;
   		$insertData['zid'] = $_GET['id'];
   			
   		if($view_or_click==1){
   			$insertData['view_pv_num'] = 1;
   			$insertData['view_ip_num'] = 1;
   			$insertData['click_pv_num'] = 0;
   			$insertData['click_ip_num'] = 0;
   		}elseif($view_or_click==2){
   			$insertData['view_pv_num'] = 0;
   			$insertData['view_ip_num'] = 0;
   			$insertData['click_pv_num'] = 1;
   			$insertData['click_ip_num'] = 1;
   		}
   			
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
   		if($view_or_click==1){
   			$data['zid'] = $_GET['id']; // 当前广告为的id
   		}elseif ($view_or_click==2){
   			$data['zid'] = intval($_GET['zoneId']);
   		}
   		$data['view_or_click'] = $view_or_click;
   		$data['visit_time'] = array(array("egt",$dayStartTime),array("lt",$tomorrowStartTime),'and');
   		$data['visit_ip'] = $this->getIp();
   			
   		// 定义zone_visit句柄
   		$zoneVisit = M('ZoneVisit');
   			
   		// 查询数据
   		$zoneVisitInfo = $zoneVisit->where($data)->select();
   			
   		if(count($zoneVisitInfo) >= 2){
   
   			// 组装数据库中更新的数据
   			$updateData = array();
   			$updateData['id'] = $zoneVisitCountInfo['id'];
   			if($view_or_click==1){
   				$updateData['view_pv_num'] = $zoneVisitCountInfo['view_pv_num']+1;
   			}elseif($view_or_click==2){
   				$updateData['click_pv_num'] = $zoneVisitCountInfo['click_pv_num']+1;
   			}
   
   			// 更改数据
   			$zoneVisitCount->save($updateData);
   		}else{
   
   			// 组装数据更新数据库
   			$updateData = array();
   			$updateData['id'] = $zoneVisitCountInfo['id'];
   			if($view_or_click==1){
   				$updateData['view_pv_num'] = $zoneVisitCountInfo['view_pv_num']+1;
   				$updateData['view_ip_num'] = $zoneVisitCountInfo['view_ip_num']+1;
   			}elseif ($view_or_click==2){
   				$updateData['click_pv_num'] = $zoneVisitCountInfo['click_pv_num']+1;
   				$updateData['click_ip_num'] = $zoneVisitCountInfo['click_ip_num']+1;
   			}
   			// 更改数据
   			$zoneVisitCount->save($updateData);
   		}
   	}
   }
   /**
    * 检测广告是否存在
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 下午5:32:43
    */
   function checkAdExsit($id){
	   	$this->dealSubmitData();
	   	
	   	// 查询相关的信息随机生成广告信息
	   	$zone = M("Zone");
	   	
	   	// 查询代码位相关的信息必须是启用状态的代码位
	   	$zoneInfo = $zone->where("id = ".$this->zoneId." and status = 1")->find();
	   	if($zoneInfo){
	   		// 处理客户端访问的来源问题 如果和申请广告时的来源地址不同则不能投放
// 	   		$this->verifyVisitSource($zoneInfo);
	   		return $zoneInfo;
	   	}else{
	   		return false;
	   	}
   }
   /**
    * 记录访问
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 上午10:28:19
    */
   function recordVisit(){
   	
   }
   /**
    * 生成代码
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 上午10:28:57
    */
   function createCode($sizeId){
   	// 查询当前广告的宽度和高度
   	$adSize = M("AdSize");
   	
   	// 根据尺寸id值查询相关的广告信息
   	$adSizeInfo = $adSize->where('id = '.$sizeId)->find();
   	
   	// 根据查询出代码位中的信息中的尺寸值随机查询当前尺寸的广告
   	$adManage = M("adManage");
   	$adManageInfo = $adManage->where("show_type = ".$sizeId." and status = 2")->order("rand()")->find();
   	
   	if($adManageInfo){
   			
   		// 			/**
   		// 			 * 产生随机数保存到服务器端SESSION中用来连接到广告的链接地址后
   		// 			 * 这样每次用户刷新一下页面所产生的广告连接地址都不相同
   		// 			 * 从而可以避免用户是直接输入广告地址的跳转广告的页面因为随机数一直在发生变化
   		// 			 */
   		//$codeRandNum = md5(rand(100,10000).md5(time()).rand(100,10000));
   			
   		switch ($sizeId){
   			case 1:{	// 文字 广告
   				// 					break;
   			}
   			case 2:{	// 图片 广告
   				// 					break;
   			}
   			case 3:{	// 文字 广告
   				// 					break;
   			}
   			case 4:{	// 文字 广告
   				// 					break;
   			}
   			case 5:{	// 文字 广告
   				// 					break;
   			}
   			case 6:{	// 文字 广告
   					
   				// 把随机数保存到当前广告
   				// 组装url连接地址
   				$jumpUrl = C('SITE_URL')."?m=".$this->actionName.'&a=clickAdJump&zoneId='.$_GET['id'].'&aid='.$adManageInfo['aid'];
   				// 组装div框中的图片或文字的广告
   					
   					
   				$code = "document.write('<style>*{margin:0px;padding:0px;border:0px;}</style>";
   				/*$code.= "<iframe width=\'".$adSizeInfo['width']."\' scrolling=\"no\" height=\"".$adSizeInfo['height']."\" frameborder=\"0\" align=\"center,center\" allowtransparency=\"true\" marginheight=\"0\" marginwidth=\"0\" src=\"./index3.html\" ></iframe>";*/
   				$code.="<div width=\'".$adSizeInfo['width']."\' height=\'".$adSizeInfo['height']."\' ><a href=\'".$jumpUrl."\' target=\"_blank\" >".$adManageInfo['content']."</a></div>";
   				$code=$code."');";
   					
   				break;
   			}
   			case 10:{	// 右下角浮窗
   					
   				$AdFloating = A('AdFloatingFrame');
   				$code = $AdFloating->createCode($adManageInfo);
   					
   				break;
   			}
   			default:	// 匹配失败
   				break;
   		}
   			
   			
   		return $code;
   	}else{
   			
   		return "没有适合当前代码位中所定义的尺寸大小的广告";
   	}
   }
   /**
    * 访问监控
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 上午10:29:57
    */
   function listenterVistit(){
   	
   }
   /**
    * 广告展现
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 上午11:09:09
    */
   function adShow($id){
   		$this->zoneId = $id;
	   	if($zoneInfo = ($this->checkAdExsit())){
	   		$code = $this->createCode($zoneInfo['size']);
	   		echo $code;
	   			
	   		if($code){		// 服务器端开始计录本次访问
	   				
	   			// 往数据表zhts_zone_visit中添加数据
	   			$this->addZoneVisit(1);	 // 参数值为1代表的是展示
	   				
	   			// 往数据表zhts_zone_visit_count中添加数据
	   			$this->addZoneVisitCount(1); // 参数值为1代表的是展示
	   		}
	   	}else{
	   		echo "当前代码位有误 或未启用";
	   		exit;
	   	}
   }
   /**
    * 禁用广告
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 下午12:02:32
    */
   function forbiddenAd(){
   	
   }
   /**
    * 锁定用户
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 下午12:02:53
    */
   function forbiddenMember(){
   	
   }
   /**
    *
    * 处理提交过来的数据
    * @author Yumao <815227173@qq.com>
    * @CreateDate: 2013-12-17 下午8:52:22
    */
   function dealSubmitData(){
   
	   	// 如果有提交过来id值则把id值转化为整型
	   	if($this->zoneId){
	   		$this->zoneId = intval($this->zoneId);
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
    * 其中参数$zoneInfo 为 代码位的信息
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
}