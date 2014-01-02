<?php
/**
 * 广告联盟系统  弹窗广告
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
class AdPopAction extends AdServiceAction {
	/**
	 * 生成弹窗代码
	 * @see AdServiceAction::createCode()
	 */
	function createCode($adManageInfo){
		$jumpUrl = C('SITE_URL').'?m=AdService&a=clickAdJump&zoneId='.$this->zoneId.'&aid='.$adManageInfo['aid'];
		
		$code = "document.write(\"<script>var POPUP_URL = '$jumpUrl';</script>\");";
		$code .= "document.write('<script language=\"javascript\" type=\"text/javascript\" src=\"" . C('STATIC_URL') . "/default/js/popup.js\"></script>');";
		
		echo $code;
		return $code;
	}
	/**
	 * 广告展现
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-30 上午11:09:09
	 */
	function adShow($id){
	
		$this->zoneId = $id;	// 广告位ID
		
		if($zoneInfo = ($this->checkAdExsit())){
			
			$this->sizeId = $zoneInfo['size'];
			$code = $this->createCode($this->getAdManageInfo());
			 
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
}