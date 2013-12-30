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
		$code = "document.write('<style>*{margin:0px;padding:0px;border:0px;}</style>";
		/*$code.= "<iframe width=\'".$adSizeInfo['width']."\' scrolling=\"no\" height=\"".$adSizeInfo['height']."\" frameborder=\"0\" align=\"center,center\" allowtransparency=\"true\" marginheight=\"0\" marginwidth=\"0\" src=\"./index3.html\" ></iframe>";*/
		$code.="<div style=\"z-index:100000;position:absolute;bottom:0;right:0px;position:fixed;\" width=\'".$adSizeInfo['width']."\' height=\'".$adSizeInfo['height']."\' ><a href=\'".$adManageInfo['jump_url']."\' target=\"_blank\" >".$adManageInfo['content']."<\/a><\/div>";
		$code=$code."');";
		return $code;
	}
}