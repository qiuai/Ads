<?php
$dbConfig			=	require ROOT_PATH . '/Conf/db.global.php';
$systemConfig		=	require ROOT_PATH . '/Conf/config.system.php';
$commonConfig   	=   require ROOT_PATH . '/Conf/config.global.php';
$domainCommonConfig =   require ROOT_PATH . '/Conf/domains.php';
$domainConfig 		=   require 'domains.php';
$domain = array_merge($domainConfig,$domainCommonConfig);

$array=array(
	'TMPL_PARSE_STRING' 		=>  $domain,  // 模板常量
);

return array_merge($dbConfig, $commonConfig, $array, $domainCommonConfig, $domain, $systemConfig);
?>