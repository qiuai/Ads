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
	 * @CreateDate: 2013-12-13 下午2:46:20
	 */
	public function sendEmail(){
		if($this->isPost()){
				
			$to 		= 	$_POST['username']; //收件人
			$real_name 	= 	$_POST['real_name']; //收件人 真实姓名
			$title 		=	$_POST['title'];    //邮件标题
			$content 	=	$_POST['content'];    		//邮件正文内容
			
			$info = $this->thinkSendEmail($to, $real_name, $title, $content);
			
			if($info === true)
			{
				$this->success("邮件发送成功！");
			}
			else
			{
				$this->error("邮件发送失败！$info");
			}
		}
	}
	
	/**
	 * 系统邮件发送函数
	 * @param string $to    接收邮件者邮箱
	 * @param string $name  接收邮件者名称
	 * @param string $subject 邮件主题
	 * @param string $body    邮件内容
	 * @param string $attachment 附件列表
	 * @return boolean
	 * 
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-13 下午2:11:04
	 */
	function thinkSendEmail($to, $name, $subject = '', $body = '', $attachment = null){
		$config = C('THINK_EMAIL');
		vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
		$mail             = new PHPMailer(); //PHPMailer对象
		
		$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->IsSMTP();  // 设定使用SMTP服务
		$mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
		//$mail->SMTPSecure = 'ssl';                 // 使用安全协议
		$mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
		$mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
		$mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
		$mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
		$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
		$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
		$replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
		$mail->AddReplyTo($replyEmail, $replyName);
		$mail->Subject    = $subject;
		$mail->MsgHTML($body);
		$mail->Encoding = '8bit';
		$mail->ContentType = 'text/html; charset=utf-8\r\n';
		$mail->AddAddress($to, $name);
		$mail->isHTML(TRUE);
		
		if(is_array($attachment)){ // 添加附件
			foreach ($attachment as $file){
				is_file($file) && $mail->AddAttachment($file);
			}
		}
		return $mail->Send() ? true : $mail->ErrorInfo;
		$mail->SmtpClose();
	}
}