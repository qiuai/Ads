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
	public function createCode($adManageInfo){
		
		// 创建动态的验证信息
		$this->createVerifyInfo();
		// 组装URL
		$jumpUrl = C('SITE_URL').'?m=AdService&a=clickAdJump&zoneId='.$this->zoneId.'&aid='.$adManageInfo['aid'];
		
		$this->assign('jumpUrl',$jumpUrl);
		
		$this->assign('adManageInfo',$adManageInfo);
		
		
		$rs = $this->view->fetch('adImage');
		
		// 统计代码添加
		$tongji = "<script type=\"text/javascript\" src=\"http://js.tongji.linezing.com/3399130/tongji.js\"></script><noscript><a href=\"http://www.linezing.com\"><img src=\"http://img.tongji.linezing.com/3399130/tongji.gif\"/></a></noscript>";
		
		$code = "document.write(\"". $this->jsformat($rs.$tongji) . "\");";
		
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
		
			if($adManageInfo = $this->getAdManageInfo()){		// 服务器端查询是否有合适的广告如果有创建广告
				
				// 调用进行过滤所用的函数 比如有些代码的代码位没有
				
				$this->createCode($adManageInfo);
		
			}else{
				echo '/**没有合适的广告*/';
			}
		}else{
			echo "当前代码位有误 或未启用";
			exit;
		}		
	}
	
	
}