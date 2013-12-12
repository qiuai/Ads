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
);
?>