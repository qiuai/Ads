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
class PlanWebAction extends CommonAction {	
	
	// 表前缀
	private  $table_pre;
	
	/**
	 * 初始化方法
	 * (non-PHPdoc)
	 * @see CommonAction::_initialize()
	 */
	function _initialize(){
		
		// 先调用父类的初始化方法
		 parent::_initialize();
		 
		 // 初始化数据库句柄
		 //$this->AdPlan = 
		//$this->actionName = $this->getActionName();
		$this->table_pre = C('DB_PREFIX');
	}
	
    public function index(){ 
		$this->assign("title","广告活动列表");
		$this->getAdPayTypeInfo();
		
		// 获取行业表中的行业相关的信息
		$industry = M('AdPlanCategory');
		//dump($industry);
		$industryInfo = $industry->select();
		$this->assign("industryInfo",$industryInfo);
		
		// 获取计划审核方式的相关信息
		$this -> getAdPlanCheckInfo();
		
		// 广告计划的状态信息列表
		$this -> getAdPlanStatusInfo();
		
		// 查询广告广告计划的相关的信息
		// 查询相关的数据
		$adPlan = M('AdPlan');
		
		//$AdPlanInfo = $this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'))->field('adplan.*,ad_plan_category.name')->where('ad_plan_category.id = adplan.category_id')->select();
		// 组装查询的条件
		$where = "ad_plan_category.id = adplan.category_id and adplan.plan_status=2";
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
		//echo $where;
		
		$AdPlanInfo  = $this->memberLinkPage($adPlan,$where,5,'id desc',array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'),'adplan.*,ad_plan_category.name as category_name');
		//dump($AdPlanInfo);
		
		// 处理数据
		$AdPlanInfo=$this->dealDataArr($AdPlanInfo);
		$this->assign('AdPlanInfo',$AdPlanInfo);
		//$adPlan = A('Admin://AdPlan');
		//$adPlan->getAdPayTypeInfo();
		$this->display();
    }
    
    /**
     * 
     * 查看广告计划
     * (non-PHPdoc)
     * @see Action::show()
     */
    public function show(){
    	
    	// 获取id值查询相关的数据
		$this->AdPlan = D($this->actionName);
		$AdPlanInfo =$this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'))->where('ad_plan_category.id = adplan.category_id and adplan.id = '.$_GET['id'])->field('adplan.*,ad_plan_category.name')->find();
		
		// 处理数据
		$AdPlanInfo = $this->dealDataOne($AdPlanInfo);
		
		// 数据分配到前端
		$this->assign('AdPlanInfo',$AdPlanInfo);
		
		//dump($AdPlanInfo);
		// 显示相关的信息
		$this->display();
    }
    
    /**
     * 
     * 广告的展示
     * @author Yumao <815227173@qq.com>
     * @CreateDate: 2013-12-26 上午11:01:42
     */
    public function showAdv(){
    	
    	if($_GET['pid']){
    		$_GET['pid'] = intval($_GET['pid']);
    	
    	}else{
    	
    		// 如果没有得到广告计划id则直接退出
    		echo "必须传递计划id值";
    		exit;
    	}
    	// 查询计划相关的信息
    	$adPlan = M('AdPlan');
    	
    	$AdPlanInfo =$adPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'))->where('ad_plan_category.id = adplan.category_id and adplan.id = '.$_GET['pid'])->field('adplan.*,ad_plan_category.name')->find();
    	$AdPlanInfo = $this->dealDataOne($AdPlanInfo);
    	$this->assign("AdPlanInfo",$AdPlanInfo);
    	//dump($AdPlanInfo);
    	$AdManage = M("AdManage");
    	$where = 'admanage.show_type = adsize.id and admanage.status = 2';
    	$where = $where." and admanage.pid =".$_GET['pid'];
    	
    	
    	// 查询出所有的信息
    	 $AdManageInfo = $this->memberLinkPage($AdManage,$where,5,'id desc',array($this->table_pre.'ad_manage'=>'admanage',$this->table_pre.'ad_size'=>'adsize'),'admanage.*,adsize.size_type');
    	 
    	 // 处理数据添加
    	 $AdManageInfo = $this->dealDataArrAdv($AdManageInfo);
    	 //dump($AdManageInfo);
    	 
    	 
    	 // 把数据分配到前台模版
    	 $this->assign('AdManageInfo',$AdManageInfo);
    	//dump($AdManageInfo);
    	// 根据计划id查询当前计划的广告   	
    	$this->display();
    }
	public function plan_list(){
		$this->assign("title","广告活动列表");
		$this->display(index);
	}
	public function plan_mine(){
		$this->assign("title","我的申请活动");
		$this->display();
	}
	public function plan_coupon_my(){
		$this->assign("title","我的优惠券");
		$this->display();
	}
	
	public function plan_coupon_list(){
		$this->assign("title","申请优惠券");
		$this->display();
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
	
	
		// 处理数据返回机制
		
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
	
	/**
	 *
	 * 处理查询出的数据转化为相应的格式（主要用于二维数组的数据）
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-16 下午1:54:01
	 */
	private function dealDataArrAdv($AdManageInfo){
	
		// 创建数据库连接句柄
		foreach ($AdManageInfo as $key=>$val){
			$AdManageInfo[$key] = $this->dealDataAdv($AdManageInfo[$key]);
		}
	
		return $AdManageInfo;
	}
	
	/**
	 *
	 * 处理查询出的数据转化为相应的格式 （主要对于一维数组的数据）
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-16 下午1:57:11
	 */
	private function dealDataOneAdv($AdManageInfo){
	
		$AdManageInfo = $this->dealData($AdManageInfo);
		return $AdManageInfo;
	}
	
	/**
	 *
	 * 具体处理某一条数据的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-16 下午1:59:55
	 */
	private function dealDataAdv($AdManageInfo){
	
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
}