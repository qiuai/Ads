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
		
		// 创建动态的验证信息
		$this->createVerifyInfo();
		// 组装URL
		$jumpUrl = C('SITE_URL').'?m=AdService&a=clickAdJump&zoneId='.$this->zoneId.'&aid='.$adManageInfo['aid'];
		
		$this->assign('jumpUrl',$jumpUrl);
		
		$this->assign('adManageInfo',$adManageInfo);
		
		$rs = $this->view->fetch('adFloatingFrame');
		
		$code = "document.write(\"". $this->jsformat($rs) . "\");";
		
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
			
			//$code = $this->createCode($this->getAdManageInfo());
			 
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