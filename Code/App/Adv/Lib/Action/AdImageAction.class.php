<?php

/**
 * 
 * 广告联盟 图片广告代码
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2014-1-2 上午10:21:56
 * @version 1.0
 */
class AdImageAction extends AdServiceAction{
	
	/**
	 * 生成图片广告代码
	 * (non-PHPdoc)
	 * @see AdServiceAction::createCode()
	 */
	public function createCode($sizeId){
		
	}
	
	/**
	 * 	广告的展示
	 * (non-PHPdoc)
	 * @see AdServiceAction::adShow()
	 */
	public function adShow($id){
		

		$this->zoneId = $id;	// 广告位ID
		
		if($zoneInfo = ($this->checkAdExsit())){
				
			$this->sizeId = $zoneInfo['size'];
		
			if($adManageInfo = $this->getAdManageInfo()){		// 服务器端开始计录本次访问
		
				$this->createCode($adManageInfo);
		
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