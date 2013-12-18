<?php
// 日志模型
class LogModel extends CommonModel {
    public $_auto		=	array(
        array('aid','getAid',self::MODEL_INSERT,'callback'),
    	array('module','getModule',self::MODEL_INSERT,'callback'),
    	array('action','getAction',self::MODEL_INSERT,'callback'),
    	array('argument','getArg',self::MODEL_INSERT,'callback'),
    	array('create_time','time',self::MODEL_INSERT,'function'),
        );
        
    /**
     * 操作员ID
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-17 下午2:24:00
     * @return unknown
     */
    public function getAid(){
    	return $_SESSION[C('USER_AUTH_KEY')];
    }
    /**
     * 返回URL参数
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-17 下午2:25:04
     * @return unknown
     */
    public function getArg(){
    	return $_SERVER["QUERY_STRING"];
    }
    /**
     * 返回控制器名
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-17 下午2:37:30
     * @return string
     */
    public function getModule(){
    	return MODULE_NAME;
    }
    /**
     * 返回方法名
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-17 下午2:37:42
     * @return string
     */
    public function getAction(){
    	return ACTION_NAME;
    }
}