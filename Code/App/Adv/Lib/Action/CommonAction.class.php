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
		$this->assign("module_name",MODULE_NAME);
		$this->assign("action_name",ACTION_NAME);
		$this->assign("advId",$_SESSION[C('ADV_AUTH_KEY')]);
		$this->assign("loginAdvName",$_SESSION['loginAdvName']);
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
     *
     * 连表查询分页
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-23 下午8:44:19
     * @param unknown_type $model
     * @param unknown_type $where
     * @param unknown_type $pageNum
     * @param unknown_type $order
     * @return unknown
     */
    public function memberLinkPage($model, $where=array(), $pageNum=10, $order='',$table="",$field=""){
    	$_GET['p'] = $_GET['p'] ? $_GET['p'] : 0;
    	// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
    
    	$list = $model->table($table)->where($where)->field($field)->order($order)->page($_GET['p'].','.$pageNum)->select();
    	//echo $model->getLastSql();
    	$this->assign('list',$list);// 赋值数据集
    	//     	echo $model->getLastSql();
    	import("ORG.Util.Page");// 导入分页类
    	$count      = $model->table($table)->where($where)->count();// 查询满足要求的总记录数
    	$Page       = new Page($count,$pageNum);// 实例化分页类 传入总记录数和每页显示的记录数
    
    	$Page->setConfig('first','首页');
    	$Page->setConfig('last','尾页');
    
    	$Page->setConfig('theme','共%totalRow% %header% %nowPage%/%totalPage% 页  %first% %upPage% %prePage% %linkPage% %downPage% %end%');
    
    	$show       = $Page->show();// 分页显示输出
    
    	$this->assign('page',$show);// 赋值分页输出
    
    	return $list;
    }
	
	/**
     *
     * 图片上传对应的方法
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-14 下午5:17:53
     * @param unknown_type $uploadPathDir
     * @return Ambigous <string, multitype:number NULL string >
     */
   protected  function upload($uploadPathDir){
    
    	// 定义变量保存上传图片的相关信息
    	$info = array();
    	import('ORG.Net.UploadFile');
    	$upload = new UploadFile();// 实例化上传类
    	$upload->maxSize  = C('UPLOAD_MAX_SIZE');// 设置附件上传大小
    
    	// 获取当前的年月
    	$yearMonth = date("Ym",time());
    
    	// 获取当前的日期
    	$day = date("d",time());
    
    	// 创建上传文件夹路径
    	$uploadPathCompletion = ROOT_PATH."/../Uploadfile/".$uploadPathDir."/".$yearMonth."/".$day;
    
    	// 创建文件夹
    	mkdir($uploadPathCompletion,0777,true);
    
    	$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    
    	// 设置上传文件的保存目录
    	$upload -> savePath =  $uploadPathCompletion.'/';
    
    	if(!$upload->upload()){
    
    		// 发生错误跳转
    		$info['message'] = $upload->getErrorMsg();
    		$info['flag'] = 0;
    	}else{
    
    		// 上传文件成功返回文件的相关信息
    		$info['message'] = $upload->getUploadFileInfo();
    		$info['message']['0']['completionPath'] = $uploadPathDir."/".$yearMonth."/".$day."/".$info['message'][0]['savename'];
    		$info['flag'] = 1;  // 代表上传成功
    	}
    
    	return $info;
    
    }
    
    /**
     * 删除上传的图片文件
     *
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-12 下午4:28:41
     */
    protected  function delUpload($filePath){
    
    	if(file_exists(ROOT_PATH."/../Uploadfile/".$filePath)){
    		unlink(ROOT_PATH."/../Uploadfile/".$filePath);
    	}
    }
}