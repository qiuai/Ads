<?php
// 用户模型
class MemberModel extends CommonModel {
    public $_validate	=	array(
        array('username','/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i','帐号格式错误'),
        array('password','require','密码必须'),
        array('confirm_password','require','确认密码必须'),
        array('confirm_password','password','确认密码不一致',self::EXISTS_VALIDATE,'confirm'),
        array('username','','帐号已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        );

    public $_auto		=	array(
        array('password','pwdHash',self::MODEL_BOTH,'callback'),
    	array('status','1',self::MODEL_INSERT,'string'),	// 状态
    	array('legal_status','1',self::MODEL_INSERT,'string'),	// 法律身份
    	array('is_feed','0',self::MODEL_INSERT,'string'),	// 是否接受邮件订阅
    	array('ip','getIp',self::MODEL_INSERT,'callback'),	// ip地址
        array('create_time','time',self::MODEL_INSERT,'function'),
        array('update_time','time',self::MODEL_UPDATE,'function'),
        );

    protected function pwdHash() {
        if(isset($_POST['password'])) {
            return pwdHash($_POST['password']);
        }else{
            return false;
        }
    }
    /**
     * 获取IP地址
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-19 上午11:34:45
     * @return unknown
     */
    public function getIp(){
    	return $_SERVER['SERVER_ADDR'];
    }
}