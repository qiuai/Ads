<?php
/**
 * 广告联盟系统  首页
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
class AdAdvAction extends CommonAction {
	

	// 定义控制器名称
	private $actionName;
	// 定义变量保存表前缀
	private  $table_pre;
	// 初始化方法 必须调用
	function _initialize(){
	
		// 先调用父类的初始化方法
		parent::_initialize();
			
		// 初始化数据库句柄
		//$this->AdPlan =
		$this->actionName = $this->getActionName();
		$this->table_pre = C('DB_PREFIX');
	}
	
    public function index(){
    	
    	// 查询当前广告主的所有的计划
    	$adPlan = M('AdPlan');
    	$adPlanInfo = $adPlan->where("uid = ".$_SESSION[C('ADV_AUTH_KEY')])->select();
    	$this->assign("adPlanInfo",$adPlanInfo);

    	// 调用函数把广告展示形式列表数据分配到前端
    	$this->dealSizeTypeInfo();
    	
		$this->assign("title","广告列表");
		$AdManage = M('AdManage');
		$where = 'admanage.show_type = adsize.id and admanage.pid = adplan.id  and admanage.sid = '.$_SESSION[C('ADV_AUTH_KEY')];
		/*if($_GET['isSearch']){ // 代表是搜索状态
			$_GET['status'] = intval($_GET['status']);
			// 组装条件查询的条件
			if($_GET['status']===0 || $_GET['status'] ){
				$_GET['status'] = intval($_GET['status']);
				$where = $where." and admanage.status =".$_GET['status'];
			}
		}
		if($_GET['pid']){
			$_GET['pid'] = intval($_GET['pid']);
			$where = $where." and admanage.pid =".$_GET['pid'];
		}*/
		foreach ($_GET as $key => $val){
			
    		if($val){
    			$key = strip_tags($key);
    			$val = strip_tags($val);
    			/*if($key == 'plan_name'){
    				$where = $where." and adplan.".$key." like '%".$val."%'";
    			}elseif ($key == 'search' || $key == "p"){
    				continue;
    			}else{
    				$where = $where." and adplan.".$key." = ".$val;
    			}*/
				if($key=="p"){
					continue;
				}
				$where = $where." and admanage.".$key." = ".$val;
    		}
    			
    	}
		// 查询出所有的信息
		$AdManageInfo = $this->memberLinkPage($AdManage,$where,5,'admanage.aid desc',array($this->table_pre.'ad_manage'=>'admanage',$this->table_pre.'ad_size'=>'adsize',$this->table_pre.'ad_plan'=>'adplan'),'admanage.*,adsize.size_type,adplan.plan_name as plan_name');
		//$AdManageInfo = $AdManage->table(array($this->table_pre.'ad_manage'=>'admanage',$this->table_pre.'ad_size'=>'adsize'))->field('admanage.*,adsize.size_type')->where('admanage.show_type = adsize.id')->select();
		
		//$AdPlanInfo = $this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'))->field('adplan.*,ad_plan_category.name')->where('ad_plan_category.id = adplan.category_id')->select();
		
		
		// 处理数据添加
		$AdManageInfo = $this->dealDataArr($AdManageInfo);
		//dump($AdManageInfo);
		
		
		// 把数据分配到前台模版
		$this->assign('AdManageInfo',$AdManageInfo);
		//$this->getAdStatusInfo();
		$this->display();
    }
    
    /**
     *
     * 处理好广告展示形式数据并且分配到前端
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-14 下午3:01:41
     */
    private function dealSizeTypeInfo(){
    
    	// 定义数组保存sizetypeinfo
    	$sizeTypeInfo = array();
    	// 1 获取广告的尺寸类型
    	$sizeType = C('AD_SIZE_TYPE');
    
    
    	// 在数据库中查出所有广告尺寸相关的数据
    	$adSize = M('AdSize');
    	$adSizeInfo = $adSize->field('id,description,size_type')->select();
    
    
    
    	// 遍历数组得到相关的数据并组装好数据
    	foreach ($adSizeInfo as $key => $val){
    		$val['size_type_name'] =  $sizeType[$val['size_type']];
    		$sizeTypeInfo[$val['size_type']][] = $val;
    		//$sizeTypeInfo[$val['size_type']][]['name'] = $sizeType[$val['size_type']];
    	}
    
    	// 把数据分配到前端模版
    	$this->assign('sizeTypeInfo',$sizeTypeInfo);
    
    	// 获取广告展示形式的数据
    
    
    	//dump($sizeTypeInfo);
    	/*foreach ($sizeType as $key => $val){
    
    	// 保存相关的数据
    	$sizeTypeInfo[$val['val']][] =
    		
    	$i++;
    	}*/
    }
    
    /**
     * 广告修改
     * (non-PHPdoc)
     * @see CommonAction::edit()
     */
    public function edit(){
	
		// 得到aid进行处理
		$this->dealSubmitData();
		
		// 创建数据库连接对象查询当前条数据
		$AdManage = M('AdManage');
		
		// 查询出所有的信息
		$AdManageInfo = $AdManage->table(array($this->table_pre.'ad_manage'=>'admanage',$this->table_pre.'ad_size'=>'adsize',$this->table_pre.'ad_plan'=>'adplan'))->field('admanage.*,adsize.size_type,adplan.plan_name')->where('admanage.show_type = adsize.id and adplan.id = admanage.pid and admanage.sid = '.$_SESSION[C('ADV_AUTH_KEY')].' and admanage.status != 0 and admanage.aid = '.$_GET['aid'])->find();
		
		if(!$AdManageInfo){
			// 说明广告不存在或者不属于这个广告主
			$this->error("广告不存在或者不属于当前广告主或者处于审核状态中不可以修改",C('SITE_URL')."?m=".$this->actionName.'&a=index');
		}
		// 处理数据
		$AdManageInfo = $this->dealDataOne($AdManageInfo);
		
		// 把数据分配到前端模版
		$this->assign('AdManageInfo',$AdManageInfo);
		
		//dump($AdManageInfo);
		$this->display();
    }
	
	public function doEdit(){
	
			
		// 创建数据库对象
		$adManage = D('AdManage');
		$adManageD = M('AdManage'); // 创建另外一个数据库句柄专门用来查询广告信息
		// 得到aid进行处理
		$this->dealSubmitData();
		
		// 查询广告的数据如果广告处于新增待审状态则不能进行修改
		// 查询相关的数据
		$AdManageInfo =$adManageD->where("aid = ".$_POST['aid'])->find();
		
		if($AdManageInfo['status']==0){ // 新增待审核状态不可是修改
			$this->error('广告处于新增待审核状态不可以修改',C('SITE_URL')."?m=".$this->actionName.'&a=edit&aid='.$_POST['aid']);
		}
		
		if(!$adManage->create()){
				
			// 说明验证未通过 跳转到添加页面
			$this->error($adManage->getError(),C('SITE_URL')."?m=".$this->actionName.'&a=edit&aid='.$_POST['aid']);
				
		}else{
			if($_FILES['content']['error'] === 0){	// 说明有图片上传代表当前广告为图片广告 且用户更换换图片
					
				
				$adSizeInfo = $this->getAdSize($AdManageInfo['show_type']);
			
			
				// 删除原来的图片
				$this->delUpload($AdManageInfo['ad_pic']);
				
				// 往服务器上传图片
				$info = $this->upload($this->actionName);
				
				// 保存相关的信息
				if($info['flag']==1){  // 说明文件上传成功保存图片的相关信息
					
					// 修改数据库中保存图片的信息值
					$adManage->ad_pic = $info['message'][0]['completionPath'];
					
					// 组装广告的内容
					$adContent = '<img   src="'.C('UPLOAD_URL').'/'.$adManage->ad_pic.'" width="'.$adSizeInfo['width'].'" height="'.$adSizeInfo['height'].'" />';
					//dump($adContent);
					//exit;
					$adManage->content = $adContent;
						
				}else{
				
					// 提示图片上传失败
					$this->error("图片上传失败".$info['message'],C('SITE_URL')."?m=".$this->actionName.'&a=edit&aid='.$_POST['aid']);
				}
			}else{  // 说明是文字广告
				if(!$_REQUEST['content'] && $_REQUEST['picFlag']==0){ // 说明没有提交广告的数据跳转

					// 提示图片上传失败
					$this->error("必须填写广告的内容".$info['message'],C('SITE_URL')."?m=".$this->actionName.'&a=edit&aid='.$_POST['aid']);
					
				}
				
			}
			
			// 把广告的状态改成修改待审
			$adManage->status = 1;
			if($adManage->save()){
			
				$this->success('数据修改成功',C('SITE_URL')."?m=".$this->actionName.'&a=index');
			}else{
				//echo $this->AdPlan->getLastSql()."<br/>";
				//exit;
				$this->error('数据修改失败或数据没有改动',C('SITE_URL')."?m=".$this->actionName.'&a=edit&aid='.$_POST['aid']);
			}
		}
	}
	/**
	 * 
	 * 处理用户提交过来的数据的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-16 下午4:12:07
	 */
	private function dealSubmitData(){
	
		// 如果有提交过来id值则把id值转化为整型
		if($_POST['aid']){
			$_POST['aid'] = intval($_POST['aid']);
		}
	
		if($_GET['aid']){
			$_GET['aid'] = intval($_GET['aid']);
		}
	
	}
    
    /**
     *
     * 处理查询出的数据转化为相应的格式（主要用于二维数组的数据）
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-16 下午1:54:01
     */
    private function dealDataArr($AdManageInfo){
    
    	// 创建数据库连接句柄
    	foreach ($AdManageInfo as $key=>$val){
    		$AdManageInfo[$key] = $this->dealData($AdManageInfo[$key]);
    	}
    
    	return $AdManageInfo;
    }
    
    /**
     *
     * 处理查询出的数据转化为相应的格式 （主要对于一维数组的数据）
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-16 下午1:57:11
     */
    private function dealDataOne($AdManageInfo){
    
    	$AdManageInfo = $this->dealData($AdManageInfo);
    	return $AdManageInfo;
    }
    
    /**
     *
     * 具体处理某一条数据的方法
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-16 下午1:59:55
     */
    private function dealData($AdManageInfo){
    
    	// 获取广告位置分类信息 （先存储在配置文件中读取配置文件)
    	$sizeType = C('AD_SIZE_TYPE');
    
    	// 获取配置文件中对应的广告状态值
    	$adStatus = C('AD_STATUS');
    
    	// 把广告尺寸类型根据传递过去的size_type转化为相对应的类别值
    	$AdManageInfo['size_type'] = $sizeType[$AdManageInfo['size_type']];
    
    	// 把审核状态值转化为文字的形式添加到数组中
    	$AdManageInfo['status_name'] =$adStatus[$AdManageInfo['status']];
    
    	// 把添加时间转换为相应的格式
    	$AdManageInfo['time'] = date("Y-m-d",$AdManageInfo['time']);
    
    	// 如果是图片广告则添加图片广告的标志变量
    	if (preg_match('/^<img/is', $AdManageInfo['content'])){
    		$AdManageInfo['picFlag']=1;
    	}else{
    		$AdManageInfo['picFlag']=0;
    	}
    
    	return $AdManageInfo;
    }
    
	public function adList(){
		$this->assign("title","广告列表");
		$this->display(index);
	}
	public function adAdd(){
		// 查询计划列表的信息
		$adPlan = M('AdPlan');
		if($_GET['pid']){
			$_GET['pid'] = intval($_GET['pid']);
			
			$adPlanInfo = $adPlan->where("id = ".$_GET['pid'])->find();
			if($adPlanInfo['plan_status'] != 2 ){ // 说明计划不再激活状态 不可以添加广告
					
				$this->error('广告计划不在激活状态中不可以添加广告',C('SITE_URL')."?m=PlanAdv&a=index");
			}else{
				$this->assign("pid",$_GET['pid']);
			}
		}else{						
			$adPlanInfo = $adPlan->where('plan_status = 2 and uid = '.$_SESSION[C('ADV_AUTH_KEY')])->field('id,plan_name')->select();
			// 数据分配到前端模版
			$this->assign('adPlanInfo',$adPlanInfo);
		}
		// 调用函数把广告展示形式列表数据分配到前端
		$this->dealSizeTypeInfo();
		//dump($adPlanInfo);
		
		$this->assign("title","新增广告");
		$this->display();
	}
	
	public function doAdd(){
	
		// 处理添加广告数据前对数据的处理
		$this->dealAddData();
		
		// 创建数据库对象资源
		$adManage = D('AdManage');
		
		
		
		if(!$adManage->create()){
			
			// 说明验证未通过 跳转到添加页面
			$this->error($adManage->getError(),C('SITE_URL')."?m=".$this->actionName.'&a=adAdd');	
			
		}else{
			
			// 处理提交过来的数据
			// $adManage->sid = $_SESSION[C('ADMIN_AUTH_KEY')];
			
			// 调用方法通过查询广告尺寸数据库得到广告的尺寸数据
			// $adManage->size = $this->getSize($_REQUEST['show_type']);
			$adSizeInfo = $this->getAdSize($_REQUEST['show_type']);
			$adManage->size = $adSizeInfo['width']."*".$adSizeInfo['height'];
			
			
			$adManage->time = time();
			// 其中只有文字广告添加的是文字 其他的都是图片
			if($_FILES['content']['error'] === 0){ // 说明有图片提交 是属于图片广告
				
				// 往服务器上传图片
				$info = $this->upload($this->actionName);
				
				// 保存相关的信息
				if($info['flag']==1){  // 说明文件上传成功保存图片的相关信息
					
					$src =  $info['message'][0]['completionPath'];
					$adManage->ad_pic = $src;
					$adContent = '<img   src="'.C('UPLOAD_URL').'/'.$src.'" width="'.$adSizeInfo['width'].'" height="'.$adSizeInfo['height'].'" />';
					//dump($adContent);
					//exit;
					$adManage->content = $adContent;
						
				}else{
				
					// 提示图片上传失败
					$this->error("图片上传失败".$info['message'],C('SITE_URL')."?m=".$this->actionName.'&a=adAdd');
				}
			}else{
				
				// 说明是文字广告必须传入文字内容
				if(!$_POST['content']){
					$this->error("数据添加失败",C('SITE_URL')."?m=".$this->actionName.'&a=adAdd');					
				}
			}
			//  往数据库中添加
			if($adManage->add()){
					
				$this->success('数据添加成功',C('SITE_URL')."?m=".$this->actionName.'&a=index');
			}else{
				$this->error("数据添加失败",C('SITE_URL')."?m=".$this->actionName.'&a=doAdd');
			}							
		}
	}
	
	/**
	 * 
	 * 处理广告投放前的数据
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-25 上午9:55:05
	 */
	private function dealAddData(){
		$_POST['pid'] = intval($_POST['pid']);
		
		// 查询计划是否处于投放中 并且是否属于当前广告主
		$adPlan = M('AdPlan');
		$adPlanInfo = $adPlan->where("id = ".$_POST['pid']." and plan_status = 2 and uid = ".$_SESSION[C('ADV_AUTH_KEY')])->find();
		if(!$adPlanInfo){
			if(!$_POST['adPlanFlag']){
				$this->error("当前广告计划不存在或者不是处于激活状态不能添加广告",C('SITE_URL')."?m=".$this->actionName.'&a=index');
			}else{
				$this->error("当前广告计划不存在或者不是处于激活状态不能添加广告",C('SITE_URL')."?m=".$this->actionName.'&a=index&pid='.$_POST['pid']);
			}
		}
		
		// 判断当前展现形式是否存在
		$_POST['show_type'] = intval($_POST['show_type']);
		
		$adSize = M("AdSize");
		$adSizeInfo = $adSize->where("id = ".$_POST['show_type'])->find();
		
		if(!$adSizeInfo){
			if(!$_POST['adPlanFlag']){
				$this->error("当前广告展现形式不存在请重新选择",C('SITE_URL')."?m=".$this->actionName.'&a=doAdd');
			}else{
				$this->error("当前广告展现形式不存在请重新选择",C('SITE_URL')."?m=".$this->actionName.'&a=doAdd&pid='.$_POST['pid']);
			}
		}
		$_POST['sid'] = $_SESSION[C('ADV_AUTH_KEY')];
	}
	
	/**
	 * 
	 * 或取当前广告位的尺寸信息
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-16 下午6:30:52
	 */
	private function getAdSize($showType){
		
		$adSize = M('AdSize');
		
		// 查询相关的尺寸信息
		$adSizeInfo = $adSize->where("id = ".$showType)->find();
		
		// 返回相关的值
		return $adSizeInfo;
		
	}
}