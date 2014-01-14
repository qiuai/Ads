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
	function createCode($adManageInfos){
		
		foreach($adManageInfos as $key=>$adManageInfo){
			
			if(empty($adManageInfo)){
				return false;
			}
			$jumpUrl = C('SITE_URL').'?m=AdService&a=clickAdJump&zoneId='.$this->zoneId.'&aid='.$adManageInfo['aid'];
			
			$adUrl .= "'$jumpUrl',";
			
			$code .= "document.write(\"<script>var adUrl=[$adUrl]; var POPUP_URL$key = '$jumpUrl';</script>\");";
			
		}
		
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
	/**
	 * 获取广告尺寸信息
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-31 上午11:29:47
	 */
	function getAdManageInfo(){
		// 查询当前广告的宽度和高度
		$adSize = M("AdSize");
		$numLimit = C('AD_NUM_LIMIT');
	
		// 根据尺寸id值查询相关的广告信息
		$adSizeInfo = $adSize->where('id = '.$this->sizeId)->find();
	
		// 根据查询出代码位中的信息中的尺寸值随机查询当前尺寸的广告
		$adManage = M("adManage");
		$adManageInfo = $adManage->where("show_type = ".$this->sizeId." and status = 2")->order("rand()")->limit(2)->select();

		return $adManageInfo;
	}
}