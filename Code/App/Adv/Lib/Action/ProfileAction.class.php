<?php
/**
 * 广告主 个人信息
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午10:07:57
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午10:07:57      todo
 */
class ProfileAction extends CommonAction {
	/**
	 * 个人信息
	 * @see CommonAction::index()
	 */
    public function index(){
    	$User	=	M('Member');
    	$info	=	$User->find($_SESSION[C('ADV_AUTH_KEY')]);
    	$detail	=	M("member_detail");
    	$info_detail	=	$detail->where('uid = '. $info['id'])->find();
    	$this->assign("info",$info);
    	$this->assign("info_detail",$info_detail);
		$this->display();
    }
    /**
     * 个人信息修改
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-13 下午7:32:49
     */
    public function profileEdit(){
    	if($this->isPost()){
    		R('Admin://Member/update', array($_SESSION[C('ADV_AUTH_KEY')]));
    	}else{
    		R('Admin://Member/getMemberInfo', array($_SESSION[C('ADV_AUTH_KEY')]));
    		$this->display();
    	}
    }
    /**
     * 修改密码
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-14 下午1:48:14
     */
    public function profilePassword(){
    	if($this->isPost()){
    		if($_REQUEST['oldpass'] && (R('Admin://Member/getPassword') == $this->pwdHash($_REQUEST['oldpass']))) {
				$Member = M('Member');
				$data['password'] = $this->pwdHash($_REQUEST['newpass']);
				$data['id'] = $_SESSION[C('ADV_AUTH_KEY')];
				if($Member->save($data)){
					$this->success('修改成功！');
				}
    		}else{
    			$this->error("原密码错误！");
    		}
    	}else{
    		$this->display();
    	}
    }
    /**
     * 得到密码
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-14 下午1:48:43
     */
    public function getPassword(){
    	if($_REQUEST['oldpass'] && (R('Admin://Member/getPassword') == $this->pwdHash($_REQUEST['oldpass']))){
    		echo 1;
    	}
    }
}