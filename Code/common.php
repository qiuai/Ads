<?php
error_reporting(0);
if ($_GET['debug_mode'] != true) {
	//开启调试模式
	define('APP_DEBUG', true);
	error_reporting(7);
}

/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);

define('ROOT_PATH', dirname(__FILE__));
define('ADS_ROOT', ROOT_PATH . '/App/');
