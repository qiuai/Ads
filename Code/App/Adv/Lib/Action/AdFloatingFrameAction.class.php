<?php
/**
 * 广告联盟系统  右下角浮动窗口
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
class AdFloatingFrameAction extends AdServiceAction {
	/**
	 * 生成浮窗代码
	 * @see AdServiceAction::createCode()
	 */
	function createCode($adManageInfo){
		// 组装URL
		$jumpUrl = C('SITE_URL').'?m=AdService&a=clickAdJump&zoneId='.$this->zoneId.'&aid='.$adManageInfo['aid'];
		
		$this->assign('jumpUrl',$jumpUrl);
		
		$this->assign('adManageInfo',$adManageInfo);
		
		$rs = $this->view->fetch('adFloatingFrame');
		
		$code = "document.write(\"". $this->jsformat($rs) . "\");";
		
		echo $code;
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
			 
			if($adManageInfo = $this->getAdManageInfo()){		// 服务器端开始计录本次访问
				
				$this->createCode($adManageInfo);
	
				// 往数据表zhts_zone_visit中添加数据
				$this->addZoneVisit(1);	 // 参数值为1代表的是展示
	
				// 往数据表zhts_zone_visit_count中添加数据
				$this->addZoneVisitCount(1); // 参数值为1代表的是展示
			}else{
				echo "获取代码位尺寸信息失败";
			}
		}else{
			echo "当前代码位有误 或未启用";
			exit;
		}
		 
	}
}