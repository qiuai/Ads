<?php
/**
 * 广告联盟系统  图片广告
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
class AdImageAction extends AdServiceAction {
	/**
	 * 生成浮窗代码
	 * @see AdServiceAction::createCode()
	 */
	function createCode($adManageInfo){
		$code = "document.write('<style>*{margin:0px;padding:0px;border:0px;}</style>";
		/*$code.= "<iframe width=\'".$adSizeInfo['width']."\' scrolling=\"no\" height=\"".$adSizeInfo['height']."\" frameborder=\"0\" align=\"center,center\" allowtransparency=\"true\" marginheight=\"0\" marginwidth=\"0\" src=\"./index3.html\" ></iframe>";*/
		$code.="<div style=\"z-index:100000;position:absolute;bottom:0;right:0px;position:fixed;\" width=\'".$adSizeInfo['width']."\' height=\'".$adSizeInfo['height']."\' ><a href=\'".$adManageInfo['jump_url']."\' target=\"_blank\" >".$adManageInfo['content']."<\/a><\/div>";
		$code=$code."');";
		
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