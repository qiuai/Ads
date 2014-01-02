<?php

/**
 * 
 * 广告联盟 文字广告展示代码
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2014-1-2 下午5:41:01
 * @version 1.0
 */
class AdTextAction extends Action{
	
	/**
	 * 生成文字广告代码
	 * (non-PHPdoc)
	 * @see AdServiceAction::createCode()
	 */
	public function createCode($adManageInfo){
	
		// 组装URL
		$jumpUrl = C('SITE_URL').'?m=AdService&a=clickAdJump&zoneId='.$this->zoneId.'&aid='.$adManageInfo['aid'];
	
		$this->assign('jumpUrl',$jumpUrl);
	
		$this->assign('adManageInfo',$adManageInfo);
	
		$rs = $this->view->fetch('adText');
	
		$code = "document.write(\"". $this->jsformat($rs) . "\");";
	
		echo $code;
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
	
				// 调用进行过滤所用的函数 比如有些代码的代码位没有
	
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