<?php
/**
 * 广告联盟系统  广告服务
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
abstract class AdServiceAction extends Action {
	
	private $planId;	// 广告所属计划ID
	private $adId;		// 广告ID
	private $zoneId;	// 广告ID
	private $adName;	// 广告名
	private $visitIp;	// 访问者IP
	
	/**
	 * 初始化
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-28 下午4:28:47
	 */
	function _initialize() {
   }
   /**
    * 记录访问
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 上午10:28:19
    */
   function recordVisit(){
   	
   }
   /**
    * 生成代码
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 上午10:28:57
    */
   function createCode(){
   	
   }
   /**
    * 访问监控
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 上午10:29:57
    */
   function listenterVistit(){
   	
   }
   /**
    * 广告展现
    *
    * @author Vonwey <VonweyWang@gmail.com>
    * @CreateDate: 2013-12-30 上午11:09:09
    */
   function adShow(){
	   	
   }
}