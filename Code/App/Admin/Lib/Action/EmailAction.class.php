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
    	$htmlData = '';	// 编辑器默认文本
    	
    	$model = M("Member");
    	$member = $model->find($_GET['id']);
    	
    	$this->assign("member",$member);
    	
    	if (!empty($_POST['content'])) {
    		if (get_magic_quotes_gpc()) {
    			$htmlData = stripslashes($_POST['content']);
    		} else {
    			$htmlData = $_POST['content'];
    		}
    	}else{
    		$model = M("Member");
    		$member = $model->find($_GET['id']);
    		
    		$this->assign("member",$member);
    	}
    	$this->display();
	}
	/**
	 * 发送邮件
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-13 上午10:03:19
	 */
	public function sendEmail(){
		if($this->isPost()){
			if (get_magic_quotes_gpc()) {
				$htmlData = stripslashes($_POST['content']);
			} else {
				$htmlData = $_POST['content'];
			}
			var_dump($_POST);exit;
			
			import('ORG.Util.Email');//导入本类
			$data['mailto'] 	= 	$_POST['username']; //收件人
			$data['subject'] 	=	$_POST['title'];    //邮件标题
			$data['body'] 		=	$htmlData;    		//邮件正文内容
			$mail = new Email();
			if($mail->send($data))
			{
				//邮件发送成功...
				$this->success("邮件发送成功！");
			}
			else
			{
				//邮件发送失败...
				$this->error("邮件发送失败！");
			}
			// 		$mail->debug(true)->send($data);   //开启调试功能
		}
	}
}