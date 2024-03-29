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
class UserAction extends CommonAction {
    public function index(){
    	$this->userList();
	}
	/**
	 * 用户列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 下午3:27:42
	 */
	public function userList(){
		$this->getRole();
		// 搜索
		if($_REQUEST['roleId']){
			$where = "where ru.role_id = " .$_REQUEST['roleId'];
		}
		
		$p = $_GET['p'] ? $_GET['p'] : 1;
		$num = 10;
		$limit = " limit ". ($p-1)*$num .",".$num;
		$sql = "select r.*, u.* from " . C('DB_PREFIX') . "user u left join " . C('DB_PREFIX') . "role_user ru on u.id = ru.user_id left join ". C('DB_PREFIX') ."role r on r.id = ru.role_id ". $where." order by u.id desc". $limit;
		$count = "select count(*) as num from " . C('DB_PREFIX') . "user u join " . C('DB_PREFIX') . "role_user ru on u.id = ru.user_id join ". C('DB_PREFIX') ."role r on r.id = ru.role_id ". $where." limit 1";
		
		$list = $this->pageList($sql, $count, $num);
		
		$this->assign("location","管理员列表");
		$this->display('userList');
	}
	/**
	 * 用户增加
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 下午3:28:09
	 */
	public function userAdd(){
		
		$this->getRole();
		
		$this->display();
	}
	/**
	 * 用户编辑
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 上午11:55:56
	 */
	public function userEdit(){
		$this->getRole();
		
		$User = M("User");
		$userList = $User->find($_GET['id']);
		$group = $this->getGroup($_GET['id']);
		$userList['group_id'] = $group['role_id'];
		
		$this->assign("userList",$userList);
		$this->assign("location","修改信息");
		$this->display();
	}
	/**
	 * 更新数据
	 * (non-PHPdoc)
	 * @see CommonAction::update()
	 */
	public function update(){
		$id = intval($_REQUEST['id']);
		$name=$this->getActionName();
		$model = D ( $name );
		
		$RoleUser = M("role_user");
		$data['user_id']	=	$_REQUEST['id'];
		$data['role_id']	=	$_REQUEST['role_id'];
		if(!($RoleUser->find($data['user_id']))){
			$role=$RoleUser->where("user_id = $data[user_id]")->save($data);
		}else{
			$role=$RoleUser->add($data);
		}
		 
		$model->update_time = time();
		$model->id = $_REQUEST['id'];
		$model->status = $_REQUEST['status'];
		$model->username = $_REQUEST['account'];
		if($_REQUEST['password']){
			$model->password = pwdHash($_POST['password']);
		}
		// 更新数据
		$list=$model->save();
		 
		if (false != $list || $role != false)
		{
			$this->success ("更新成功！", C('SITE_URL')."?m=User");
		}
		else
		{
			//错误提示
			$this->error ("修改失败！", C('SITE_URL')."?m=User");
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see CommonAction::insert()
	 */
	public function insert() {
		
		// 创建数据对象
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				$this->addRole($result);
				$this->success('用户添加成功！', C('SITE_URL')."?m=User");
			}else{
				$this->error('用户添加失败！', C('SITE_URL')."?m=User");
			}
		}
	}
	/**
	 * 返回组名
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 上午11:16:24
	 * @param unknown_type $id
	 */
	public function getGroup($id){
		$Group = M();
		$result= $Group->Table(C("DB_PREFIX")."role r")
		->join(C("DB_PREFIX")."role_user ru on r.id = ru.role_id")
		->join(C("DB_PREFIX")."user u on u.id = ru.user_id ")->where("u.id = $id")
		->find();
		return $result;
	}
	/**
	 * 返回角色
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午2:24:03
	 */
	public function getRole(){
		$Role = M("Role");
		$array= array(
// 				array('neq',1),	// 隐藏管理员
				array('neq',2),	// 隐藏广告主
				array('neq',3)	// 隐藏网站主
				);
		$where['id'] = $array;
		$roleList = $Role->where($where)->select();
		
		$this->assign("roleList",$roleList);
		
	}
	/**
	 * 写入角色数据
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午2:38:47
	 * @param unknown_type $userId
	 */
	protected function addRole($userId) {
		//新增用户自动加入相应权限组
		$RoleUser = M("Role_user");
		$RoleUser->user_id	=	$userId;
		// 默认加入网站编辑组
		$RoleUser->role_id	=	$_REQUEST['role_id'];
		$RoleUser->add();
	}
	/**
	 * 重置密码
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-3 下午2:39:05
	 */
	public function resetPwd() {
		$id  =  $_POST['id'];
		$password = $_POST['password'];
		if(''== trim($password)) {
			$this->error('密码不能为空！');
		}
		$User = M('User');
		$User->password	=	md5($password);
		$User->id			=	$id;
		$result	=	$User->save();
	}
	/**
	 * 操作记录列表
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-17 下午3:07:57
	 */
	public function logList(){
		// 搜索
		if($_REQUEST['aid']){
			$where['aid'] = intval($_REQUEST['aid']);
		}
		// 列表
		$model = M("Log");
		$this->memberPage($model, $where, $pageNum=15, $order='id desc');
		$this->display();
	}
	/**
	 * 清除前30天的记录
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-17 下午3:22:37
	 */
	public function logClean(){
		$log = M("Log");
		$month = mktime(0,0,0,date("m")-1,date("d"),date("Y"));
		$where['create_time'] = array('lt',$month);
		$log->where($where)->delete();
		$this->redirect(C(SITE_URL)."?m=User&a=logList");
	}
}