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
class AdTextAction extends AdServiceAction{
	
	/**
	 * 生成文字广告代码
	 * (non-PHPdoc)
	 * @see AdServiceAction::createCode()
	 */
	public function createCode($adManageInfo){
		
		// 创建动态的验证信息
		$this->createVerifyInfo();
	
		// 组装URL
		$jumpUrl = C('SITE_URL').'?m=AdService&a=clickAdJump&zoneId='.$this->zoneId.'&aid='.$adManageInfo['aid'];
	
		$this->assign('jumpUrl',$jumpUrl);
	
		$this->assign('adManageInfo',$adManageInfo);
		// 查询广告的尺寸分配到前端
		$adSizeInfo = $this->adSizeInfo;
		$this->assign("width",$adSizeInfo['width']);
		$this->assign("height",$adSizeInfo['height']);
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
	
				// 调用进行过滤所用的函数 比如有些代码的代码位没有 启用 有些代码位已经超过他的当天的
	
				$this->createCode($adManageInfo);
	
			}else{
				echo "/**没有合适的广告*/";
			}
		}else{
			echo "当前代码位有误 或未启用";
			exit;
		}
	
	}
}