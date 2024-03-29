<?php
/**
 * 广告联盟系统 公共方法
 * 
 * @copyright (C)2012 ZHTS Inc.
 * @project project_name
 * @author Vonwey <VonweyWang@gmail.com>
 * @CreateDate: 2013-11-25 上午9:56:28
 * @version 1.0
 *
 * @ModificationHistory  
 * Who          When                What 
 * --------     ----------          ------------------------------------------------ 
 * Vonwey   2013-11-25 上午9:56:28      todo
 */
class CommonAction extends Action {
	/**
	 * 构造函数
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-25 上午10:06:53
	 */
    function _initialize(){
    	$this->checkUser();
    	$this->assign("module_name",MODULE_NAME);
    	$this->assign("action_name",ACTION_NAME);
    	
    	/**
    	 * 权限检测
    	 */
    	import('@.ORG.Util.Cookie');
    	// 用户权限检查
    	if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
    		import('@.ORG.Util.RBAC');
    		if (!RBAC::AccessDecision()) {
    			//检查认证识别号
    			if (!$_SESSION [C('USER_AUTH_KEY')]) {
    				//跳转到认证网关
    				redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
    			}
    			// 没有权限 抛出错误
    			if (C('RBAC_ERROR_PAGE')) {
    				// 定义权限错误页面
    				redirect(C('RBAC_ERROR_PAGE'));
    			} else {
    				if (C('GUEST_AUTH_ON')) {
    					$this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
    				}
    				// 提示错误信息
    				$this->error(L('_VALID_ACCESS_'));
    			}
    		}
    	}
    }
    /**
     * 检测用户是否登录
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-11-25 上午10:10:26
     */
    public function checkUser(){
    	$Public = A('Public');
		$Public->checkUser();
		$this->logRecord();
    }
    /**
     * 获取应用信息
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-4 上午11:30:43
     */
    public function getGroup(){
    	$model = M("Group");
    	$list = $model->select();
    	
    	$this->assign("groupList",$list);
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
    	    //	echo $model->getLastSql();
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
     * SQL 分页
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-16 下午3:43:45
     */
    public function pageList($sql, $countSql, $pageNum){
    	$model = M('');
    	// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
    	$list = $model->query($sql);
//     	echo $model->getLastSql();
    	$this->assign('list',$list);// 赋值数据集
    	import("ORG.Util.Page");// 导入分页类
    	$count      = $model->query($countSql);;// 查询满足要求的总记录数
//     	echo $model->getLastSql();
    	$Page       = new Page($count[0]['num'],$pageNum);// 实例化分页类 传入总记录数和每页显示的记录数
    	
    	$Page->setConfig('first','首页');
    	$Page->setConfig('last','尾页');
    	
    	$Page->setConfig('theme','共%totalRow% %header% %nowPage%/%totalPage% 页  %first% %upPage% %prePage% %linkPage% %downPage% %end%');
    	
    	$show       = $Page->show();// 分页显示输出
    	$this->assign('page',$show);// 赋值分页输出
    	
    	return $list;
    }
// 	public function index() {
//         //列表过滤器，生成查询Map对象
//         $map = $this->_search();
//         if (method_exists($this, '_filter')) {
//             $this->_filter($map);
//         }
//         $name = $this->getActionName();
//         $model = D($name);
//         if (!empty($model)) {
//             $this->_list($model, $map);
//         }
//         $this->display();
//         return;
//     }

    /**
      +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    function getReturnUrl() {
        return __URL__ . '?' . C('VAR_MODULE') . '=' . MODULE_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $name 数据对象名称
      +----------------------------------------------------------
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _search($name = '') {
        //生成查询条件
        if (empty($name)) {
            $name = $this->getActionName();
        }
        $name = $this->getActionName();
        $model = D($name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST [$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _list($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得满足条件的记录数
        $count = $model->where($map)->count('id');
        if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);
            //分页查询数据

            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        cookie('_currentUrl_', __SELF__);
        return;
    }

    function insert($data=array(), $url='') {
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) { //保存成功
        	$url = $url ? $url : cookie('_currentUrl_');
            $this->success('新增成功!',$url);
        } else {
            //失败提示
            $this->error('新增失败!');
        }
    }

    function read() {
        $this->edit();
    }

    function edit() {
        $name = $this->getActionName();
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
        $this->display();
    }

    function update() {
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            $this->success('编辑成功!',cookie('_currentUrl_'));
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }

    /**
      +----------------------------------------------------------
     * 默认删除操作
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    public function delete() {
        //删除指定记录
        $name = $this->getActionName();
        $model = M($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                $list = $model->where($condition)->delete();
                if ($list !== false) {
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }

    public function foreverdelete() {
        //删除指定记录
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete()) {
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }

    public function clear() {
        //删除指定记录
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            if (false !== $model->where('status=1')->delete()) {
                $this->success(L('_DELETE_SUCCESS_'),$this->getReturnUrl());
            } else {
                $this->error(L('_DELETE_FAIL_'));
            }
        }
        $this->forward();
    }

    /**
      +----------------------------------------------------------
     * 默认禁用操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    public function forbid() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_REQUEST [$pk];
        $condition = array($pk => array('in', $id));
        $list = $model->forbid($condition);
        if ($list !== false) {
            $this->success('状态禁用成功',$this->getReturnUrl());
        } else {
            $this->error('状态禁用失败！');
        }
    }

    public function checkPass() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->checkPass($condition)) {
            $this->success('状态批准成功！',$this->getReturnUrl());
        } else {
            $this->error('状态批准失败！');
        }
    }

    public function recycle() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->recycle($condition)) {
            $this->success('状态还原成功！',$this->getReturnUrl());
        } else {
            $this->error('状态还原失败！');
        }
    }

    public function recycleBin() {
        $map = $this->_search();
        $map ['status'] = - 1;
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    /**
      +----------------------------------------------------------
     * 默认恢复操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    function resume() {
        //恢复指定记录
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->resume($condition)) {
            $this->success('状态恢复成功！',$this->getReturnUrl());
        } else {
            $this->error('状态恢复失败！');
        }
    }

    function saveSort() {
        $seqNoList = $_POST ['seqNoList'];
        if (!empty($seqNoList)) {
            //更新数据对象
            $name = $this->getActionName();
            $model = D($name);
            $col = explode(',', $seqNoList);
            //启动事务
            $model->startTrans();
            foreach ($col as $val) {
                $val = explode(':', $val);
                $model->id = $val [0];
                $model->sort = $val [1];
                $result = $model->save();
                if (!$result) {
                    break;
                }
            }
            //提交事务
            $model->commit();
            if ($result !== false) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            } else {
                $this->error($model->getError());
            }
        }
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
     * 记录操作
     *
     * @author Vonwey <VonweyWang@gmail.com>
     * @CreateDate: 2013-12-17 上午11:39:26
     */
    public function logRecord(){
    	// 是否记录操作
		$module = M("Node");
		$where['module'] = MODULE_NAME;
		$where['action'] = ACTION_NAME;
		$data = $module->where($where)->find();
		if(!empty($data) && $data['record']){
			$log = D("Log");
			if($log->create($where)){
				$log->add();
			}
		}
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

	/**
     * 下载Excel报表
     *
     * @author Xieyihong <zhts_nimei@163.com>
     * @CreateDate: 2013-12-24 下午3:43:41
	 * $filename    报表名称
	 * $ReportArr 	输出内容，为二维数组
	 * $HeaderArr   表头，为一维数组
     */
	public function downloadExcel($filename,$ReportArr,$HeaderArr){
		//输出的文件类型为excel
		header("Content-type:application/vnd.ms-excel");
		//提示下载
		header("Content-Disposition:attachement;filename=".$filename.".xls");
		//报表数据
		$ReportContent = '';
		$num1 = count($ReportArr);
		for($i=0;$i<$num1;$i++){
			$num2 = count($ReportArr[$i]);
			for($j=0;$j<$num2;$j++){
				//ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
				$ReportContent .= '"'.$ReportArr[$i][$j].'"'."\t";
			}
			//最后连接\n 表示换行
			$ReportContent .= "\n";
		}
		for($k=0;$k<count($HeaderArr);$k++){
			// ecxel都是一格一格的，用\t将每一行的数据连接起来 \t制表符
			$ReportTitle .= '"'.$HeaderArr[$k].'"'."\t";
		}
		//输出即提示下载
		echo $ReportTitle."\n".$ReportContent;
	}
}