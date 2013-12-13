<?php
return array(
	/**
	  * 上传文件相关的配置
	  */
	'UPLOAD_MAX_SIZE'		=> 2000000,// 上传文件的最大值
	
	/**
	 * 账号类型
	 */
	'BANK_TYPE'				=>array(
			'ABC'	=>	"中国银行",
			'ICBC'	=>	"工商银行",
			'CMB'	=>	"招商银行",
			),
		
	/**
	 * 会员状态
	 */
	'MEMBER_STATUS'			=>array(
			'0'		=>	"<span style=\"color:green\">正常</span>",
			'1'		=>	"<span style=\"color:orange\">待审</span>",
			'2'		=>	"<span style=\"color:red\">锁定</span>",
			'3'		=>	"<span style=\"color:gray\">拒绝</span>",
			'4'		=>	"<span style=\"color:gray\">邮件未激活</span>",
			),
		
	/**
	 * 邮箱配置
	 */
	'SMTP_SERVER' =>'smtp.126.com',					//邮件服务器
	'SMTP_PORT' =>25,								//邮件服务器端口
	'SMTP_USER_EMAIL' =>'zhts_vonwey@163.com', 		//SMTP服务器的用户邮箱(一般发件人也得用这个邮箱)
	'SMTP_USER'=>'zhts_vonwey@163.com',				//SMTP服务器账户名
	'SMTP_PWD'=>'zhtsjs003',						//SMTP服务器账户密码
	'SMTP_MAIL_TYPE'=>'HTML',						//发送邮件类型:HTML,TXT(注意都是大写)
	'SMTP_TIME_OUT'=>30,							//超时时间
	'SMTP_AUTH'=>true,								//邮箱验证(一般都要开启)
);
?>