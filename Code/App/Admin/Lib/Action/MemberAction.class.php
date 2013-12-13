<?php
/**
 * 广告联盟系统  会员管理
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
class MemberAction extends CommonAction {
    public function index(){
    	$this->memberList();
	}
	/**
	 * 会员注册
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-9 下午4:37:38
	 */
	public function memberAdd(){
		if($this->isPost()){
			if(empty($_POST['username'])) {
				$this->error('帐号错误！');
			}elseif (empty($_POST['password'])){
				$this->error('密码必须！');
			}elseif (empty($_POST['verify'])){
				$this->error('验证码必须！');
			}elseif($_POST['password'] != $_POST['confirm_password']){
				$this->error("密码不一致！");
			}
			$Member = M("Member");
			$_POST['status'] = 1; // 状态
			$_POST['legal_status'] = '个人'; // 法律身份
			$_POST['is_feed'] = 1; // 是否接受邮件订阅
			$_POST['password'] = MD5($_POST['password']); // 密码加密
			$_POST['create_time'] = time(); // 创建时间
			$_POST['ip'] = $_SERVER['SERVER_ADDR']; // 创建时间
			if($Member->create()){
				if($Member->add()){
					$this->success("注册成功！");
				}else{
					$this->error("注册失败！");
				}
			}else{
				$this->error("注册失败！");
			}
		}
	}
	/**
	 * 会员搜索
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-12 上午10:13:11
	 */
	public function memberSearch(){
		
	}
	/**
	 * 全部会员列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-11 下午2:36:12
	 */
	public function memberList(){
		
		$this->getMemberData();
		
		$this->assign("location","会员列表");
		$this->display("list");
	}
	/**
	 * 广告主会员列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-11 下午7:47:05
	 */
	public function advMemberList(){
		$this->getMemberData("adv");
		
		$this->assign("location","会员列表");
		$this->assign("user_type","adv");
		$this->display("list");
	}
	/**
	 * 网站主会员列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-11 下午7:47:05
	 */
	public function webMemberList(){
		$this->getMemberData("web");
	
		$this->assign("location","会员列表");
		$this->assign("user_type","web");
		$this->display("list");
	}
	/**
	 * 更新会员状态
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-12 下午1:13:00
	 */
	public function updateStatus(){
		$model = M("Member");
		$where['uid'] = (int)$_REQUEST['uid'];
		$data['status'] = (int)$_REQUEST['status'];
		if($model->where($where)->save($data)){
			echo 'ok';
		}
	}
	/**
	 * 编辑会员信息
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-12 下午2:04:55
	 */
	public function _before_edit(){
		$model = M("member_detail");
		$where['uid'] = $_REQUEST['id'];
		$vo = $model->where($where)->find();
		$this->assign('bank', $vo);
	}
	/**
	 * 更新会员信息
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-12 下午4:05:22
	 */
	public function update(){
		$model = M("Member");
		$_POST['password'] = pwdHash($_POST['password']);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
		// 更新数据
        $list = $model->save();
        if (false !== $list) {
	        $detail = M("member_detail");
	        $condition['uid']		= $_POST['id'];
	        $data['id_card']	= $_POST['id_card'];
	        $data['bank_name']	= $_POST['bank_name'];
	        $data['card_author']= $_POST['card_author'];
	        $data['card_number']= $_POST['card_number'];
	        $data['uid']= $_POST['id'];
	        // 更新数据
	        $list = $detail->where($condition)->save($data);
	        if($detail->where("uid = ". $_POST['id'])->find()){
	        	$list = $detail->where($condition)->save($data);
	        }else{
	        	$list = $detail->where($condition)->add($data);
	        }
	        if (false !== $list) {
	            //成功提示
	            $this->success('编辑成功!');
	        } else {
	            //错误提示
	            $this->error('编辑失败!');
	        }
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
	}
	/**
	 * 进入会员中心
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-12 下午5:32:59
	 */
	public function loginMemberHome(){
		$model = M("Member");
		$id = $_REQUEST['id'];
		$member = $model->find($id);
		
		//生成认证条件
		$map            =   array();
		// 支持使用绑定帐号登录
		$map['username']	= $member['username'];
		$map["status"]	=	array('gt',0);
		import ( '@.ORG.Util.RBAC' );
		$authInfo = RBAC::authenticate($map);
		$_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
		$_SESSION['email']	=	$authInfo['email'];
		$_SESSION['loginUserName']		=	$authInfo['username'];
		$_SESSION['login_count']	=	$authInfo['username'];
		
		if($member['user_type'] == "web"){
			redirect(C('WEB_URL'));
		}else{
			redirect(C('ADV	_URL'));
		}
	}
	/**
	 * 获取会员数据
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-11 下午6:26:38
	 * @param unknown_type $type
	 */
	public function getMemberData($type=''){
		$model = M("Member");
		
		if($type){
			$where['user_type'] = $type;
		}
		if($_REQUEST['status'] != ''){
			$where['status'] = $_REQUEST['status'];
		}
		if($_REQUEST['condition']){
			$where[$_REQUEST['condition']] = $_REQUEST['content'];
		}
		
		$this->memberPage($model, $where, 20);
	}
}