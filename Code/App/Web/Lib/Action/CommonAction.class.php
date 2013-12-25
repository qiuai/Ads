<?php
/**
 * 广告联盟系统 公共方法
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午10:02:31
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午10:02:31      todo
 */
class CommonAction extends Action {
	/**
	 * 构造函数
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:07:29
	 */
	function _initialize(){
    	$this->checkUser();
    	
		$this->assign("flag",MODULE_NAME);
    }
    /**
     * 检测登录
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-12 下午6:04:13
     */
    public function checkUser(){
    	$Public = A('Public');
    	$Public->checkUser();
	}
	/**
	 * 加密
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-12-14 上午11:43:08
	 * @param string $password
	 * @param string $type
	 * @return string
	 */
	function pwdHash($password, $type = 'md5') {
		return hash ( $type, $password );
	}
	/**
     * 分页方法 ThinkPHP
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-12 上午11:09:19
     * @param unknown_type $model
     * @param unknown_type $pageNum
     * @param unknown_type $where
     * @param unknown_type $order
     */
    public function memberPage($model, $where=array(), $pageNum=10, $order=''){
    	$_GET['p'] = $_GET['p'] ? $_GET['p'] : 0;
    	// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
    	$list = $model->where($where)->order($order)->page($_GET['p'].','.$pageNum)->select();
    	$this->assign('list',$list);// 赋值数据集
//     	echo $model->getLastSql();
    	import("ORG.Util.Page");// 导入分页类
    	$count      = $model->where($where)->count();// 查询满足要求的总记录数
    	$Page       = new Page($count,$pageNum);// 实例化分页类 传入总记录数和每页显示的记录数
    
    	$Page->setConfig('first','首页');
    	$Page->setConfig('last','尾页');
    
    	$Page->setConfig('theme','共%totalRow% %header% %nowPage%/%totalPage% 页  %first% %upPage% %prePage% %linkPage% %downPage% %end%');
    
    	$show       = $Page->show();// 分页显示输出
    
    	$this->assign('page',$show);// 赋值分页输出
    	
    	return $list;
    }
	
	
}