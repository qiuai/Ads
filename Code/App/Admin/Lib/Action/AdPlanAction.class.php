<?php
/**
 * 
 * 广告计划所对应的控制器
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2013-12-3 下午5:29:47
 * @version 1.0
 */
class AdPlanAction extends CommonAction{
	
	
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
	
	/**
	 * 
	 * 计划列表首页
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-4 上午11:26:12
	 */
	public function index($pageNum = 5){
		
		// 查询获取所有的计划
		$this->AdPlan = D($this->actionName);
		
		// 获取广告计划的计费形式
		$this->getAdPayTypeInfo();
		
		// 查询相关的数据
		//$AdPlanInfo = $this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'))->field('adplan.*,ad_plan_category.name')->where('ad_plan_category.id = adplan.category_id')->select();
		$AdPlanInfo  = $this->memberLinkPage($this->AdPlan,'ad_plan_category.id = adplan.category_id',$pageNum,'id desc',array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'),'adplan.*,ad_plan_category.name as category_name');
		//dump($AdPlanInfo);
		
		// 处理数据
		$AdPlanInfo=$this->dealDataArr($AdPlanInfo);
		
		// 广告计划的状态信息列表
		$this->getAdPlanStatusInfo();
		
		$this->assign('AdPlanInfo',$AdPlanInfo);
	
		// 对数据进行处理
		$this->display();
	}
	
	/**
	 * 
	 * 搜索对应的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-23 上午11:19:09
	 */
	public function search($pageNum=''){
		$this->AdPlan = D($this->actionName);
		if($_GET['search_type'] == 'plan_id'){
			
			$_GET['id'] = intval($_GET['keyword']);
			unset($_GET['keyword']);
			unset($_GET['search_type']);
			
		}elseif($_GET['search_type'] == 'plan_name'){
			$_GET['plan_name'] =  strip_tags($_GET['keyword']);
			$_GET['plan_name'] = array("like","%".$_GET['plan_name']."%");
			unset($_GET['keyword']);
			unset($_GET['search_type']);
		}elseif($_GET['plan_status']===0){
		
			//$_GET['pay_type'] = intval($_GET['pay_type']);
			// 获取相关的数值如计划的状态值
			$_GET['plan_status'] = intval($_GET['plan_status']);
		}elseif($_GET['plan_status']){
			$_GET['plan_status'] = intval($_GET['plan_status']);
		}elseif($_GET['pay_type']){
			
			$_GET['pay_type']=intval($_GET['pay_type']);
		}
		
		$where= 'ad_plan_category.id = adplan.category_id';
		// 遍历$_GET组装查询的条件
		foreach ($_GET as $key=>$val){
			if($key=="p"){
				continue;
			}
			$where =$where." and adplan.".$key."=".$val;
		}
// 		echo $where;
		$AdPlanInfo  = $this->memberLinkPage($this->AdPlan,$where,$pageNum,'id desc',array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'),'adplan.*,ad_plan_category.name as category_name');
		//$AdPlanInfo  = $this->memberPage($this->AdPlan,$_GET,5,'id desc');
		
		// 处理数据
		$AdPlanInfo=$this->dealDataArr($AdPlanInfo);
// 		dump($AdPlanInfo);
		// 广告计划的状态信息列表
		$this->getAdPlanStatusInfo();
		
		$this->getAdPayTypeInfo();
		//$this->connectUrlArguments();
		
	
		$this->assign('AdPlanInfo',$AdPlanInfo);
		$this->display();
	} 
	
	/**
	 * 
	 * 组装url中参数
	 * 组装url参数是要把当前的这个搜索类型的参数去除
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-23 下午2:01:52
	 */
	private function connectUrlArguments(){
		
		// 定义变量保存url地址参数
		$urlArguments = "";
		// 遍历get提交过来的参数
		foreach($_GET as $key=>$val){

			$urlArguments = $urlArguments."&".$key."=".$val; 
			
		}
		
		// 去除两边的&值
		$urlArguments = trim($urlArguments,"&");
		dump($urlArguments);
		
	}
	
	/**
	 * 
	 * 审核对应的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-7 下午1:44:35
	 */
	public function plan_verify(){
		
		// 获取id值查询相关的数据
		$this->AdPlan = D($this->actionName);
		
		// 处理查询条件数据
		
		$AdPlanInfo =$this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'))->where('ad_plan_category.id = adplan.category_id and adplan.id = '.$_GET['id'])->field('adplan.*,ad_plan_category.name')->find();
		
		// 处理数据
		$AdPlanInfo = $this->dealDataOne($AdPlanInfo);
		
		
		// 数据分配到前端
		$this->assign('AdPlanInfo',$AdPlanInfo);
		
		
		//dump($AdPlanInfo);
		$this->display();
	}
	
	public function exportReport(){
		
		// 查询数据导出
		
		$t = array("计划LOGO","计划ID","计划名称","广告主ID","计划分类","计费方式","千次点击或展示价格","审核方式","结算方式","开始日期","结束日期","计划状态","计算业绩");
	}
	
	/**
	 * 
	 * 处理url或表单中提交过来的数据
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 上午11:51:27
	 */
	private function dealSubmitData(){
		
		// 如果有提交过来id值则把id值转化为整型
		if($_POST['id']){
			$_POST['id'] = intval($_POST['id']);
		}
		
		if($_GET['id']){
			$_GET['id'] = intval($_GET['id']);
		}
		
	}
	
	/**
	 * 
	 * 调用此方法通过审核
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-9 下午3:58:28
	 */
	public function plan_verify_do(){
		
		$this->AdPlan = M($this->actionName);
		$_POST["id"] = intval($_POST["id"]);
		
		$_POST['site_master_pay_price'] = intval($_POST['site_master_pay_price']);
		$_POST['site_master_display_price'] = intval($_POST['site_master_display_price']);
		// 处理数据
		if(!$_POST['site_master_pay_price']){
	
			// 审核失败跳转到审核的页面
			$this->error('审核失败必须填写网站主的结算价格',C('SITE_URL')."?m=".$this->actionName.'&a=plan_verify&id='.$_POST["id"]);
		}
		
		if(!$_POST['site_master_display_price']){	
			$this->error('审核失败必须填写网站主的显示价格',C('SITE_URL')."?m=".$this->actionName.'&a=plan_verify&id='.$_POST["id"]);	
		}
		// 获取id修改数据
		// 定义更改的数据
		
		$_POST['plan_status'] = 5;  // 状态改为待投放
		
		if($this->AdPlan->save($_POST)){
			
			// 审核成功调转到列表页面
			$this->success('审核成功',C('SITE_URL')."?m=".$this->actionName.'&a=index');
		}else{
			
			// 审核失败跳转到审核的页面
			$this->error('审核失败',C('SITE_URL')."?m=".$this->actionName.'&a=plan_verify');			
		}				
	}
	
	/**
	 * 
	 * 计划详情显示
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-9 下午10:07:45
	 */
	public function plan_detail(){
		
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
	 * 激活暂停计划对应的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-10 下午5:23:34
	 */
	public function plan_active(){
		
		
		$this->AdPlan = M($this->actionName);
		$_POST['plan_status'] = intval($_POST['plan_status']);
		/**
		 * 只有计划处于待投放状态或暂停状态才可以激活
		 */
		
		// 查询当前数据库中广告计划的状态 只有处于待投放的或暂停状态的广告才可以激活
		$adPlanInfo = $this->AdPlan->where("id = ".$_POST["id"])->find();
		if($_POST['plan_status']==2){ // 说明用户传递的命令是要激活这个广告计划
			if(!($adPlanInfo['plan_status']==5 || $adPlanInfo['plan_status']==3)){
				exit; // 广告计划没有通过审核或本来就处于激活状态或者已过期不可以激活
			}
		}
		
		if($_POST['plan_status']==3){ // 说明用户传递的命令是要暂停这个广告计划
			if(!($adPlanInfo['plan_status']==2)){
				exit; // 广告计划本来不是处于激活状态不可以暂停
			}
		}
		// 组装并修改数据库中的数据
		$data = array();
		// 获取提交过来的id值
		$data['id'] = $_POST['id'];
		$data['plan_status'] = $_POST['plan_status'];
		if($this->AdPlan->save($data)){
			echo "ok";
		}

		
	}
	
	/**
	 * 
	 * 删除计划对应的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-24 下午1:59:18
	 */
	public function plan_delete(){
		
		$AdPlan = M($this->actionName);
		$_POST['id'] = intval($_POST['id']);
		$AdPlanInfo =$AdPlan->field('plan_logo,plan_status')->where("id = ".$_POST['id'])->find();
		
		if($AdPlanInfo['plan_status']==2){
			exit; //	计划处于激活投放状态不可以删除
		}
		// 删除图片
		$this->delUpload($AdPlanInfo['plan_logo']);
		$AdPlan->where("id = ".$_POST['id'])->delete();
		echo "ok";
		
	}
	
	/**
	 * 
	 * 广告计划编辑
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-10 下午5:34:29
	 */
	public function plan_edit(){
		
				
		$this->AdPlan = M($this->actionName);
		
		// 查询相关的数据
		$AdPlanInfo =$this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'ad_plan_category'=>'ad_plan_category'))->where('ad_plan_category.id = adplan.category_id and adplan.id = '.$_GET['id'])->field('adplan.*,ad_plan_category.name')->find();
		if($AdPlanInfo['plan_status']==2){
			
			echo "<script>";
			echo "alert('计划正在运行中不能进行更该，如果要更改请先暂停计划');";			
			echo "window.location.href='".C('SITE_URL')."?m=".$this->actionName."&a=index';";
			echo "</script>";
			
		}else{
			
			// 查询的广告计划数据进行处理
			
						
			// 获取行业表中的行业相关的信息
			$industry = M('adPlanCategory');
		
			$industryInfo = $industry->select();
			$this->assign("industryInfo",$industryInfo);
						
			// 获取广告计划的计费形式
			$this->getAdPayTypeInfo();
			
			// 或取审核方式的相关信息
			$this->getAdPlanCheckInfo();
			
			// 获取广告的结算方式的信息
			$this->getAdClearingFormInfo();
			
			// 获取网站类型的定向相关的数据
			$this->getDirectionalSiteTypeArrInfo(json_decode($AdPlanInfo['directional_site_type_arr']));
			
			// 获取时间定向的相关数据
			$this->getDirectionalTimeArrInfo(json_decode($AdPlanInfo['directional_time_arr']));
			
			// 获取网站星期定向的相关信息
			$this->getDirectionalWeekArrInfo(json_decode($AdPlanInfo['directional_week_arr']));
			
			
			// 处理时间
			$AdPlanInfo['start_date'] = date('Y-m-d',$AdPlanInfo['start_date']);
			$AdPlanInfo['end_date'] = date('Y-m-d',$AdPlanInfo['end_date']);
			
			//dump($AdPlanInfo);
			$this->assign('AdPlanInfo',$AdPlanInfo);
			$this->display();			
		}

	}
	
	/**
	 * 
	 * 广告计划编辑后修改
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-12 下午3:40:38
	 */
	public function plan_save(){
	
		// 调用函数处理传递过来的数据
		$this->dealAddSubmitData(C('SITE_URL')."?m=".$this->actionName.'&a=plan_edit&id='.intval($_POST['id']));
		
		
		// 创建数据库对象
		$this->AdPlan = D($this->actionName);
		
		// 把状态改成修改待审
		$_POST['plan_status'] = 1;
		$AdPlan = M($this->actionName);
		
		if(!$this->AdPlan->create()){
			
			//echo $this->AdPlan->getError();
			// 用jstiao'zhuan
			$this->error($this->AdPlan->getError(),C('SITE_URL')."?m=".$this->actionName.'&a=plan_edit&id='.$_POST['id']);			
		}else{
			
			// 如果有图片上传删除原来图片
			if($_FILES['plan_logo']['error'] === 0){
				
				// 查询相关的数据
				$AdPlanInfo =$AdPlan->field('plan_logo')->where("id = ".$_POST['id'])->find();
				 $this->delUpload($AdPlanInfo['plan_logo']);
				
				// 往服务器上传图片
				$info = $this->upload($this->actionName);
				
			// 保存相关的信息
				if($info['flag']==1){  // 说明文件上传成功保存图片的相关信息
				
					$this->AdPlan->plan_logo = $info['message'][0]['completionPath'];
					
				}else{
				
					// 提示图片上传失败
					$this->error("图片上传失败".$info['message'],C('SITE_URL')."?m=".$this->actionName.'&a=plan_edit&id='.$_POST['id']);
				}				
			}
			
			if($this->AdPlan->save()){
				
				
				
				$this->success('数据修改成功',C('SITE_URL')."?m=".$this->actionName.'&a=index');
			}else{
				
				//echo $this->AdPlan->getLastSql()."<br/>";
				//exit;
				$this->error('数据修改失败',C('SITE_URL')."?m=".$this->actionName.'&a=plan_edit&id='.$_POST['id']);
			}
			
		}
		
	}
	
	/**
	 * 
	 * 添加广告
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-24 下午6:34:56
	 */
	/*public function addAdv(){
		
		// 只有审核通过并且处于激活状态中的广告计划才可以添加广告
		$_GET['id'] = intval($_GET['id']);
		$adPlan = M("AdPlan");
		$adPlanInfo = $adPlan->where("id = ".$_GET['id'])->find(); 
		if($adPlanInfo['plan_status'] != 2 ){ // 说明计划不再激活状态 不可以添加广告
			
			$this->error('广告计划不在激活状态中不可以添加广告',C('SITE_URL')."?m=".$this->actionName.'&a=index');
		}else{
			$adManage = A('AdManage');
			$adManage->add();
		}
	}*/
	
	//public function 
	
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
		/*switch($AdPlanInfoOne['pay_type']){
			case 1:
				$AdPlanInfoOne['pay_type'] = 'cpm';
				break;
			case 2:
				$AdPlanInfoOne['pay_type'] = 'cpc';
				break;
			
		}*/
			
		// 处理结算方式
		$clearingForm = C('CLEARING_FORM');
		$AdPlanInfoOne['clearing_form'] = $clearingForm[$AdPlanInfoOne['clearing_form']];
		/*switch($AdPlanInfoOne['clearing_form']){
			case 1:
				$AdPlanInfoOne['clearing_form'] = '日结';
				break;
			case 2:
				$AdPlanInfoOne['clearing_form'] = '周结';
				break;
			case 3:
				$AdPlanInfoOne['clearing_form'] = '月结';
				break;
		}*/
			
		// 处理审核方式
		$planCheck = C('AD_PLAN_CHECK');
		$AdPlanInfoOne['plan_check'] = $planCheck[$AdPlanInfoOne['plan_check']];
		/*switch ($AdPlanInfoOne['plan_check']){
			case 0:
				$AdPlanInfoOne['plan_check'] = '自动审核';
				break;
			case 1:
				$AdPlanInfoOne['plan_check'] = '手动审核';
				break;
		
		}*/
			
		// 处理审核的状态
		$adStatus = C('AD_PLAN_STATUS');
		$AdPlanInfoOne['plan_status_flag'] = $AdPlanInfoOne['plan_status']; 
		$AdPlanInfoOne['plan_status'] = $adStatus[$AdPlanInfoOne['plan_status']];
		
		/*switch ($AdPlanInfoOne['plan_status']){
			case 0:
				$AdPlanInfoOne['plan_status'] = '待审核';
				break;
			case 1:
				$AdPlanInfoOne['plan_status'] = '待投放';
				break;
			case 2:
				$AdPlanInfoOne['plan_status'] = '投放中';
				break;
			case 3:
				$AdPlanInfoOne['plan_status'] = '已停止';
				break;
			case 4:
				$AdPlanInfoOne['plan_status'] = '已过期';
				break;
		
		}*/
		
		// 处理数据返回机制
		/*switch ($AdPlanInfoOne['cps_data_return']){			
			case 1:
				$AdPlanInfoOne['cps_data_return'] = '实时返回';
				break;
			case 2:
				$AdPlanInfoOne['cps_data_return'] = '延时返回';
				break;			
		}*/
		
		// 查询出计划分类所对应的分类名称
		/*$adPlanCategory = M('AdPlanCategory');
		$adPlanCategoryInfo = $adPlanCategory->where("id = ".$AdPlanInfoOne['category_id'])->find();
		
		$AdPlanInfoOne['category_name'] = $adPlanCategoryInfo['name'];*/
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
	 * 广告计划的添加
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-3 下午5:31:16
	 */
	public function add(){
		
		// 当前时间
		$startDateDefault = date("Y-m-d",time());
		$this->assign("startDateDefault",$startDateDefault);
		// 获取行业表中的行业相关的信息
		$industry = M('adPlanCategory');
		//dump($industry);
		
		$industryInfo = $industry->select();
		$this->assign("industryInfo",$industryInfo);
		
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
	 * 广告添加检查
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-4 上午10:27:24
	 */
	public function addCheck(){
			
		// 调用函数处理传递过来的数据
		$this->dealAddSubmitData(C('SITE_URL')."?m=".$this->actionName.'&a=add');
		
		// 创建数据库对象
		$this->AdPlan = D($this->actionName);
		if(!$this->AdPlan->create()){
			
			//echo $this->AdPlan->getError();
			$this->error($this->AdPlan->getError(),C('SITE_URL')."?m=".$this->actionName.'&a=add');			
		}else{
			
			
			if($_FILES['plan_logo']['error'] === 0){	
				
				// 往服务器上传图片
				$info = $this->upload($this->actionName);
				
				// 保存相关的信息
				if($info['flag']==1){  // 说明文件上传成功保存图片的相关信息
				
					$this->AdPlan->plan_logo = $info['message'][0]['completionPath'];
					
				}else{
				
					// 提示图片上传失败
					$this->error("图片上传失败".$info['message'],C('SITE_URL')."?m=".$this->actionName.'&a=add');
				}
			}
			
			//  往数据库中添加
			if($this->AdPlan->add()){
							
				$this->success('数据添加成功',C('SITE_URL')."?m=".$this->actionName.'&a=index');
			}else{
				$this->error("数据添加失败",C('SITE_URL')."?m=".$this->actionName.'&a=add');
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
		
	
		$_POST['uid'] = intval($_POST['uid']);
		
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
	

}
