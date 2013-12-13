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
	 * phpmailer 邮箱配置
	 */
	'THINK_EMAIL' => array(
		'SMTP_HOST'   => 'smtp.163.com', //SMTP服务器
		'SMTP_PORT'   => '25', //SMTP服务器端口
		'SMTP_USER'   => 'zhts_vonwey@163.com', //SMTP服务器用户名
		'SMTP_PASS'   => 'zhtsjs003', //SMTP服务器密码
		'FROM_EMAIL'  => 'zhts_vonwey@163.com', //发件人EMAIL
		'FROM_NAME'   => '广告联盟', //发件人名称
		'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
		'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
	),
);
?>