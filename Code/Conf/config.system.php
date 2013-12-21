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
			1=>array(
				1=>'BC',
				2=>"中国银行"
			),
			2=>array(
				1=>'ICBC',
				2=>"中国工商银行"
			),
			3=>array(
				1=>'CMB',
				2=>"中国招商银行"
			),
			4=>array(
				1=>'CBC',
				2=>"中国建设银行"
			),
			5=>array(
				1=>'ABC',
				2=>"中国农业银行"
			),
			6=>array(
				1=>'PBC',
				2=>"中国人民银行"
			),
			7=>array(
				1=>'CEB',
				2=>"中国光大银行"
			),
			8=>array(
				1=>'CTB',
				2=>"中国交通银行"
			),
			9=>array(
				1=>'CUB',
				2=>"中国农村信用社"
			),
			10=>array(
				1=>'HXB',
				2=>"华夏银行"
			),
			11=>array(
				1=>'CITIC',
				2=>"中信实业银行"
			),
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
	 * 广告尺寸类型
	 */
	'AD_SIZE_TYPE' => array('1'=>'图片','2'=>'文字','3'=>'漂浮','4'=>'对联','5'=>'弹窗','6'=>'视窗'),				// 尺寸类型相关的配置

	/**
	 * 广告对应的状态值
	 */
	'AD_STATUS' => array('新增待审','修改待审','投放中','审核拒绝','计划停止'),

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
		
	/**
	 * 广告计费形式
	 */
	'AD_PAY_TYPE' => array(
		'1' => 'cpm',  // 按照展示次数计费
		'2' => 'cpc',  // 按照点击次数计费	
	),	
	
	/**
	 * 广告计划的审核方式
	 */
	'AD_PLAN_CHECK' => array(
		'1' => "无需审核", 
		'2' => '手动审核',
		//'2' => "自动审核",	
	),
	
	/**
	 * 广告计划中广告金额的结算方式
	 */
	'CLEARING_FORM' => array(
		'1'	=> "日结",
		'2' => "周结",
		'3' => "月结",		
	),
	
);
?>