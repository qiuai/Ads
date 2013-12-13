<?php
/**
 * 广告联盟系统  首页
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午9:58:45
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午9:58:45      todo
 */
class EmailAction extends CommonAction {
	/**
	 * 发送邮件页面
	 * @see CommonAction::index()
	 */
    public function index(){
    	$this->display();
	}
	/**
	 * 发送邮件
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-13 上午10:03:19
	 */
	public function sendEmail(){
		import('ORG.Util.Email');//导入本类
		$data['mailto'] 	= 	'i@pengyong.info'; //收件人
		$data['subject'] =	'邮件正文标题';    //邮件标题
		$data['body'] 	=	'邮件正文内容';    //邮件正文内容
		$mail = new Email();
		if($mail->send($data))
		{
			//邮件发送成功...
		}
		else
		{
			//邮件发送失败...
		}
// 		$mail->debug(true)->send($data);   //开启调试功能
	}
}