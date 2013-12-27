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
class PlanAdvAction extends CommonAction {

	private $AdPlan;
	private $actionName;
	// 定义变量保存表前缀
	private  $table_pre;
	function _initialize(){
	
		// 先调用父类的初始化方法
		parent::_initialize();
			
		// 初始化数据库句柄
		//$this->AdPlan =
		$this->actionName = $this->getActionName();
		$this->table_pre = C('DB_PREFIX');
	}
	
    public function index(){
    	
    	$this->getAdPayTypeInfo();
    	
    	// 获取计划审核方式的相关信息
    	$this -> getAdPlanCheckInfo();
    	
    	// 广告计划的状态信息列表
    	$this -> getAdPlanStatusInfo();
    	
    	// 获取行业表中的行业相关的信息
    	$industry = M('AdPlanCategory');
    	//dump($industry);
    	$industryInfo = $industry->select();
    	$this->assign("industryInfo",$industryInfo);
    	
    	// 查询获取所有的计划
    	$this->AdPlan = D($this->actionName);
    	
    	// 组装查询条件
    	$where = 'ad_plan_category.id = adplan.category_id and adplan.uid = '.$_SESSION[C('ADV_AUTH_KEY')];
    	foreach ($_GET as $key => $val){
    		if($val){
    	
    			if($key == 'plan_name'){
    				$where = $where." and adplan.".$key." like '%".$val."%'";
    			}elseif ($key == 'search' || $key == "p"){
    				continue;
    			}else{
    				$where = $where." and adplan.".$key." = ".$val;
    			}
    	
    		}
    			
    	}
    	// 查询本广告主所有的广告计划
    	$AdPlanInfo  = $this->memberLinkPage($this->AdPlan,$where,10,'id desc',array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'),'adplan.*,ad_plan_category.name as category_name');
    	//dump($AdPlanInfo);
    	// 处理数据
    	$AdPlanInfo=$this->dealDataArr($AdPlanInfo);
    	$this->assign("AdPlanInfo",$AdPlanInfo);
    	//dump($AdPlanInfo);
		$this->assign("title","计划列表");
		$this->display();
    }
    
    /**
     *
     * 把一维数组转换根据键和值转换为二维数组
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-20 下午2:14:15
     */
    private function oneDimensionalArrayToTwoDimensionalArray($oneDimensionalArray,$infoSelectArr=0){
    
    	// 定义数组保存转换后的数组
    	$changeEndArr = array();
    
    	// 定义变量保存转换后的数组的下标值
    	$i = 0;
    	if(!$infoSelectArr){
    		while (list($key,$val)=each($oneDimensionalArray)){
    			$changeEndArr[$i]['key'] = $key;
    			$changeEndArr[$i]['val'] = $val;
    			$i++;
    		}
    	}else{
    		while (list($key,$val)=each($oneDimensionalArray)){
    			$changeEndArr[$i]['key'] = $key;
    			$changeEndArr[$i]['val'] = $val;
    			if(in_array($changeEndArr[$i]['key'],$infoSelectArr)){
    				$changeEndArr[$i]['selectFlag'] = 1;
    			}else{
    				$changeEndArr[$i]['selectFlag'] = 0;
    			}
    			$i++;
    		}
    	}
    
    	return $changeEndArr;
    
    }
    /**
     *
     * 获取广告的广告计划的状态列表
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-23 上午11:50:41
     */
    private function getAdPlanStatusInfo(){
    	$adPlanStatusInfo = C('AD_PLAN_STATUS');
    	$this->assign("adPlanStatusInfo",$adPlanStatusInfo);
    	//dump($adPlanStatusInfo);
    }
    
    /**
     *
     * 或取审核方式的相关信息
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-23 上午11:28:15
     */
    private function getAdPlanCheckInfo(){
    	// 读取广告计划的审核方式
    	$adPlanCheck = C('AD_PLAN_CHECK');
    
    	// 把数据转换为二维数组
    	$adPlanCheckInfo = $this->oneDimensionalArrayToTwoDimensionalArray($adPlanCheck);
    
    	// 分配到前端模版
    	$this->assign('adPlanCheckInfo',$adPlanCheckInfo);
    }
    
    /**
     *
     * 获取广告的结算方式的信息
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-23 上午11:32:47
     */
    private function getAdClearingFormInfo(){
    
    	// 读取广告的结算方式
    	$adClearingForm = C('CLEARING_FORM');
    	$adClearingFormInfo = $this->oneDimensionalArrayToTwoDimensionalArray($adClearingForm);
    	$this->assign("adClearingFormInfo",$adClearingFormInfo);
    }
    /**
     *
     * 获取广告计划的计费形式
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-23 上午11:25:55
     */
    private function getAdPayTypeInfo(){
    
    	// 读取配置文件获取获取广告计费形式的相关信息
    	$adPayType = C('AD_PAY_TYPE');
    
    	//dump($adPayType);
    	// 把数据组装成二维数组
    	$adPayTypeInfo = $this->oneDimensionalArrayToTwoDimensionalArray($adPayType);
    	$this->assign("adPayTypeInfo",$adPayTypeInfo);
    }
	
	/**
	 * 
	 * 获取网站类型的定向相关的数据
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-23 上午11:35:38
	 */
	private function getDirectionalSiteTypeArrInfo($infoSelectArr=0){
		
		// 读取网站类型的定向相关的数据
		$directionalSiteTypeArr = C('DIRECTIONAL_SITE_TYPE_ARR');
		$directionalSiteTypeArrInfo = $this->oneDimensionalArrayToTwoDimensionalArray($directionalSiteTypeArr,$infoSelectArr);
		$this->assign("directionalSiteTypeArrInfo",$directionalSiteTypeArrInfo);
	}
	
	/**
	 * 
	 * 获取网站星期定向的相关信息
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-23 上午11:36:53
	 */
	private function getDirectionalWeekArrInfo($infoSelectArr=0){
		
		// 读取星期定向相关的数据
		$directionalWeekArr = C('DIRECTIONAL_WEEK_ARR');
		$directionalWeekArrInfo = $this->oneDimensionalArrayToTwoDimensionalArray($directionalWeekArr,$infoSelectArr);
		$this->assign("directionalWeekArrInfo",$directionalWeekArrInfo);
	}
	
	/**
	 * 
	 * 获取时间定向的相关数据
	 * 用来展示时间定向的列表如果$infoSelectArr 参数 则表示在其中添加了选择状态主要是修改页面用来展示定向列表时用
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-23 上午11:39:01
	 */
	private function getDirectionalTimeArrInfo($infoSelectArr=0){
		
		// 读取时间相关的定向
		$directionalTimeArr = C('DIRECTIONAL_TIME_ARR');
		$directionalTimeArrInfo = $this->oneDimensionalArrayToTwoDimensionalArray($directionalTimeArr,$infoSelectArr);
		$this->assign("directionalTimeArrInfo",$directionalTimeArrInfo);		
	}
    
	public function planList(){
		$this->assign("title","计划列表");
		$this->display(index);
	}
	public function planAdd(){
	
		// 当前时间
		$startDateDefault = date("Y-m-d",time());
		$this->assign("startDateDefault",$startDateDefault);
		// 获取行业表中的行业相关的信息
		$industry = M('adPlanCategory');
		//dump($industry);
		
		$industryInfo = $industry->select();
		$this->assign("industryInfo",$industryInfo);
		
		$this->assign("title","新增计划");
		// 获取广告计划的计费形式
		$this->getAdPayTypeInfo();
		
		// 或取审核方式的相关信息
		$this->getAdPlanCheckInfo();
		
		// 获取广告的结算方式的信息
		$this->getAdClearingFormInfo();
		
		// 获取网站类型的定向相关的数据
		$this->getDirectionalSiteTypeArrInfo();
		
		// 获取时间定向的相关数据
		$this->getDirectionalTimeArrInfo();
		
		// 获取网站星期定向的相关信息
		$this->getDirectionalWeekArrInfo();
		$this->display();
	}
	
	public function doAdd(){
		// 调用函数处理传递过来的数据
		$this->dealAddSubmitData(C('SITE_URL')."?m=".$this->actionName.'&a=planAdd');
		
		// 创建数据库对象
		$this->AdPlan = D("AdPlan");
		if(!$this->AdPlan->create()){
			
			
			//echo $this->AdPlan->getError();
			$this->error($this->AdPlan->getError(),C('SITE_URL')."?m=".$this->actionName.'&a=planAdd');			
		}else{
			
			
			if($_FILES['plan_logo']['error'] === 0){	
				
				// 往服务器上传图片
				$info = $this->upload($this->actionName);
				
				// 保存相关的信息
				if($info['flag']==1){  // 说明文件上传成功保存图片的相关信息
				
					$this->AdPlan->plan_logo = $info['message'][0]['completionPath'];
					
				}else{
				
					// 提示图片上传失败
					$this->error("图片上传失败".$info['message'],C('SITE_URL')."?m=".$this->actionName.'&a=planAdd');
				}
			}
			
			//  往数据库中添加
			if($this->AdPlan->add()){
					
				
				$this->success('数据添加成功',C('SITE_URL')."?m=".$this->actionName.'&a=index');
			}else{
			
				
				$this->error("数据添加失败",C('SITE_URL')."?m=".$this->actionName.'&a=doAdd');
			}

		}
	}
	
	/**
	 * $errorUrl数据处理失败后跳转的URL地址
	 * 处理广告计划添加时用户提交过来的数据
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-21 下午3:00:54
	 * 
	 */
	private function dealAddSubmitData($errorUrl){
		
	
		$_POST['uid'] = $_SESSION[C('ADV_AUTH_KEY')];
		
		// 查询添加的广告主id是否存在
		$member = M('Member');
		if(!$member->where("user_type = 'adv'  and id = ".$_POST['uid'])->find()){
			
			// 广告主不存在跳转到添加页面
			$this->error("您填写的广告主不存在",$errorUrl);
		}
		
		
		// 必须传递plan_logo图片
		if(!$_FILES['plan_logo']){
			
			// 说明没有传递计划logo
			$this->error("您没有上传计划logo图",$errorUrl);
		}
		
		// 判断行业类别是否为已有的行业类别
		$adPlanCategory = M('AdPlanCategory');
		$_POST['category_id'] = intval($_POST['category_id']);		
		if(!$adPlanCategory->where("id = ".$_POST['category_id'])->find()){			
			$this->error("您选择的广告行业类别不存在",$errorUrl);
		}
		
		// 判断所选得费形式是否存在于数组中
		$adPayType = C('AD_PAY_TYPE');
		$_POST['pay_type'] = intval($_POST['pay_type']);
		if(!$adPayType[$_POST['pay_type']]){
			$this->error("您所选择的计费形式暂时不提供",$errorUrl);
		}

		// 处理结算周期
		$clearingForm = C('CLEARING_FORM');
		$_POST['clearing_form'] = intval($_POST['clearing_form']);
		if(!$clearingForm[$_POST['clearing_form']]){
			$this->error("您所选择的结算周期不存在",$errorUrl);
		}	

		// 处理计费周期
		// 把时间转化为时间戳格式
		$_POST['start_date'] = strtotime($_POST['start_date']);
		$_POST['end_date'] = strtotime($_POST['end_date']);
		if($_POST['end_date'] <= $_POST['start_date']){
			$this->error("计费周期结束时间必须大于开始时间",$errorUrl);
		}
		if($_POST['start_date']< $this->createDayStartTime()){
			$this->error("计费周期开始时间必须大于等于当前时间",$errorUrl);
		}
		
		// 处理价格
		//$_POST['price'] = intval($_POST['price']);
		if(!is_numeric($_POST['price'])){
			$this->error("每千次的价格必须为数值",$errorUrl);			
		}
		
		// 处理每日pv限额或每日点击限额 必须为整型数值
		if($_POST['pay_type'] == 1){
			
			$_POST['max_per_day'] = intval($_POST['max_per_day']);
			
			// 处理某个网站的pv或者点击限额
			$_POST['max_per_site'] = intval($_POST['max_per_site']);
			
		}elseif($_POST['pay_type'] == 2){
			$_POST['max_per_day'] = intval($_POST['max_per_day_c']);
				
			// 处理某个网站的pv或者点击限额
			$_POST['max_per_site'] = intval($_POST['max_per_site_c']);
		}
		
		// 处理描述的内容过滤特殊的字符
		$_POST['description'] = strip_tags($_POST['description']);
		
		// 处理网站类型定向
		$_POST['directional_site_type'] = intval($_POST['directional_site_type']);
		if($_POST['directional_site_type']){
			$_POST['directional_site_type_arr'] = json_encode($_POST['directional_site_type_arr']);			
		}
		
		// 处理星期定向
		$_POST['directional_week'] = intval($_POST['directional_week']);
		if ($_POST['directional_week']){
			$_POST['directional_week_arr'] = json_encode($_POST['directional_week_arr']);
		}
		
		// 处理时间定向
		$_POST['directional_time'] = intval($_POST['directional_time']);
		if($_POST['directional_time']){
			$_POST['directional_time_arr'] = json_encode($_POST['directional_time_arr']);
		}
		
		
	}
	
	private  function createDayStartTime(){
	
		// 获取当前的年
		$year = date("Y",time());
	
		// 获取当前的月
		$month = date("m",time());
	
		// 获取当前的天
		$day = date("d",time());
		return mktime(0,0,0,$month,$day,$year);
	}
	
	/**
	 * 
	 * 投放广告
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-27 上午10:49:14
	 */
	public function addAdv(){
		echo "vvvvvvvvv";
	}
	/**
	 * 
	 * 计划详情页面
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-27 上午9:58:43
	 */
	public function show(){
		
		// 获取id值查询相关的数据
		$this->AdPlan = D($this->actionName);
		$_GET['id'] = intval($_GET['id']);
		// 定义搜索条件
		$where = 'ad_plan_category.id = adplan.category_id and adplan.id = '.$_GET['id']." and adplan.uid = ".$_SESSION[C('ADV_AUTH_KEY')];
		$AdPlanInfo =$this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'))->where($where)->field('adplan.*,ad_plan_category.name')->find();
		
		// 处理数据
		$AdPlanInfo = $this->dealDataOne($AdPlanInfo);
		
		// 数据分配到前端
		$this->assign('AdPlanInfo',$AdPlanInfo);
		
		//dump($AdPlanInfo);
		// 显示相关的信息
		$this->display();
	}
	
	/**
	 * 计划修改
	 * (non-PHPdoc)
	 * @see CommonAction::edit()
	 */
	public function edit(){
		echo "bbbbbbbbbbbb";
	}
	/**
	 *
	 * 处理广告计划相关的数据（对于二维数组）
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-6 下午1:52:36
	 */
	private function dealDataArr($AdPlanInfo){
	
		foreach ($AdPlanInfo as $key=>$val){
				
			$AdPlanInfo[$key] = $this->dealData($AdPlanInfo[$key]);
				
		}
		return $AdPlanInfo;
	}
	
	/**
	 *
	 * 处理单条相关数据的方法（对于一维数组）
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-7 下午2:31:34
	 * @param unknown_type $AdPlanInfo
	 * @return string
	 */
	private function dealDataOne($AdPlanInfo){
	
		$AdPlanInfo = $this->dealData($AdPlanInfo);
		return $AdPlanInfo;
	}
	
	/**
	 *
	 * 处理相关的数据
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-7 下午2:21:45
	 */
	private function dealData($AdPlanInfoOne){
	
		// 处理计费方式
		$adPayType = C('AD_PAY_TYPE');
		$AdPlanInfoOne['pay_type'] = $adPayType[$AdPlanInfoOne['pay_type']];
		// 处理计费方式
		
			
		// 处理结算方式
		$clearingForm = C('CLEARING_FORM');
		$AdPlanInfoOne['clearing_form'] = $clearingForm[$AdPlanInfoOne['clearing_form']];
		
			
		// 处理审核方式
		$planCheck = C('AD_PLAN_CHECK');
		$AdPlanInfoOne['plan_check'] = $planCheck[$AdPlanInfoOne['plan_check']];
	
			
		// 处理审核的状态
		$adStatus = C('AD_PLAN_STATUS');
		$AdPlanInfoOne['plan_status_flag'] = $AdPlanInfoOne['plan_status'];
		$AdPlanInfoOne['plan_status'] = $adStatus[$AdPlanInfoOne['plan_status']];
	
	
		// 查询出计划分类所对应的分类名称
		// 处理开始日期和结束日期的显示方式
		$AdPlanInfoOne['start_date'] = date('Y-m-d',$AdPlanInfoOne['start_date']);
				$AdPlanInfoOne['end_date'] = date('Y-m-d',$AdPlanInfoOne['end_date']);
	
						// 处理网站定向类型的数据
						$directionalSiteTypeArr = C('DIRECTIONAL_SITE_TYPE_ARR');
		if($AdPlanInfoOne['directional_site_type_arr']){
		
			$AdPlanInfoOne['directional_site_type_arr'] = json_decode($AdPlanInfoOne['directional_site_type_arr']);
			foreach ($AdPlanInfoOne['directional_site_type_arr'] as $key=>$val){
				$AdPlanInfoOne['directional_site_type_arr'][$key] = $directionalSiteTypeArr[$val];
			}
		}
						// 处理星期定向的问题
						$directionalWeekArr = C('DIRECTIONAL_WEEK_ARR');
		if($AdPlanInfoOne['directional_week_arr']){
		
			$AdPlanInfoOne['directional_week_arr'] = json_decode($AdPlanInfoOne['directional_week_arr']);
			foreach ($AdPlanInfoOne['directional_week_arr'] as $key=>$val){
				$AdPlanInfoOne['directional_week_arr'][$key] = $directionalWeekArr[$val];
			}
							
		}
	
						// 处理时间定向问题
		$directionalTimeArr = C('DIRECTIONAL_TIME_ARR');
		if($AdPlanInfoOne['directional_time_arr']){								
					$AdPlanInfoOne['directional_time_arr'] = json_decode($AdPlanInfoOne['directional_time_arr']);
				foreach ($AdPlanInfoOne['directional_time_arr'] as $key=>$val){
					$AdPlanInfoOne['directional_time_arr'][$key] = $directionalTimeArr[$val];
				}
		}
		return $AdPlanInfoOne;
	}
}