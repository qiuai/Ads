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
	public function index(){
		
		// 查询获取所有的计划
		$this->AdPlan = D($this->actionName);
		
		// 查询相关的数据
		$AdPlanInfo = $this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'industry'=>'industry'))->field('adplan.*,industry.name')->where('industry.id = adplan.category_id')->select();
		
		//dump( $this->AdPlan->getLastSql());
// 		echo ACTION_NAME."<br>";
// 		echo APP_NAME."<br/>";
// 		echo APP_PATH."<br/>";
// 		echo __APP__."<br/>";
// 		echo __ACTION__."<br/>";
// 		echo __CLASS__."<br/>";
// 		echo $this->getActionName()."<br/>";
// 		echo __METHOD__."<br/>";
	;
		// 处理数据
		$AdPlanInfo=$this->dealDataArr($AdPlanInfo);
		
		$this->assign('AdPlanInfo',$AdPlanInfo);
		// 对数据进行处理
		$this->display();
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
		$AdPlanInfo =$this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'industry'=>'industry'))->where($_GET and 'industry.id = adplan.category_id')->find();
		
		// 处理数据
		$AdPlanInfo = $this->dealDataOne($AdPlanInfo);
		
		// 数据分配到前端
		$this->assign('AdPlanInfo',$AdPlanInfo);
		
		
		//dump($AdPlanInfo);
		$this->display();
	}
	
	/**
	 * 
	 * 调用此方法通过审核
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-9 下午3:58:28
	 */
	public function plan_verify_do(){
		
		$this->AdPlan = M($this->actionName);
		
		// 获取id修改数据
		// 定义更改的数据
		$_POST['auditflag'] = 1;
		
		if($this->AdPlan->save($_POST)){
			
			// 审核成功调转到列表页面
			$this->success('审核成功',$this->actionName.'/index');
		}else{
			
			// 审核失败跳转到审核的页面
			$this->error('审核失败',$this->actionName.'/plan_verify');
			
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
		$AdPlanInfo =$this->AdPlan->table(array($this->table_pre.'ad_plan'=> 'adplan',$this->table_pre.'industry'=>'industry'))->where($_GET and 'industry.id = adplan.category_id')->find();
		
		// 处理数据
		$AdPlanInfo = $this->dealDataOne($AdPlanInfo);
		
		// 数据分配到前端
		$this->assign('AdPlanInfo',$AdPlanInfo);
		
		// 显示相关的信息
		$this->display();
	}
	/**
	 * 
	 * 处理广告计划相关的数据
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
	 * 处理单条相关数据的方法
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
		switch($AdPlanInfoOne['pay_type']){
			case 1:
				$AdPlanInfoOne['pay_type'] = 'cps';
				break;
			case 2:
				$AdPlanInfoOne['pay_type'] = 'cpa';
				break;
			case 3:
				$AdPlanInfoOne['pay_type'] = 'cpc';
				break;
			case 4:
				$AdPlanInfoOne['pay_type'] = 'cpm';
				break;
		}
			
		// 处理结算方式
		switch($AdPlanInfoOne['clearing_form']){
			case 1:
				$AdPlanInfoOne['clearing_form'] = '日结';
				break;
			case 2:
				$AdPlanInfoOne['clearing_form'] = '周结';
				break;
			case 3:
				$AdPlanInfoOne['clearing_form'] = '月结';
				break;
		}
			
		// 处理审核方式
		switch ($AdPlanInfoOne['plan_check']){
			case 0:
				$AdPlanInfoOne['plan_check'] = '自动审核';
				break;
			case 1:
				$AdPlanInfoOne['plan_check'] = '手动审核';
				break;
		
		}
			
		// 处理审核的状态
		switch ($AdPlanInfoOne['plan_status']){
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
		
		}
		
		// 处理数据返回机制
		switch ($AdPlanInfoOne['cps_data_return']){			
			case 1:
				$AdPlanInfoOne['cps_data_return'] = '实时返回';
				break;
			case 2:
				$AdPlanInfoOne['cps_data_return'] = '延时返回';
				break;			
		}
			
		// 处理开始日期和结束日期的显示方式
		$AdPlanInfoOne['start_date'] = date('Y-m-d',$AdPlanInfoOne['start_date']);
		$AdPlanInfoOne['end_date'] = date('Y-m-d',$AdPlanInfoOne['end_date']);
		return $AdPlanInfoOne;
	}
	/**
	 * 
	 * 广告计划的添加
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-3 下午5:31:16
	 */
	public function add(){
		
		
		// 获取行业表中的行业相关的信息
		$industry = M('Industry');
		//dump($industry);
		
		$industryInfo = $industry->select();
		$this->assign("industryInfo",$industryInfo);
		$this->display();
		
		
	}
	
	/**
	 * 
	 * 广告添加检查
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-4 上午10:27:24
	 */
	public function addCheck(){
		
		
		// 创建数据库对象
		$this->AdPlan = D($this->actionName);
		if(!$this->AdPlan->create()){
			
			//echo $this->AdPlan->getError();
			$this->error($this->AdPlan->getError(),$this->actionName.'/add');			
		}else{
			
			
			if($_FILES['plan_logo']['error'] == 0){	
				
				// 往服务器上传图片
				$info = $this->upload($this->actionName);
				
				// 保存相关的信息
				if($info['flag']==1){  // 说明文件上传成功保存图片的相关信息
				
					$this->AdPlan->plan_logo = $info['message'][0]['completionPath'];
					
				}else{
				
					// 提示图片上传失败
					$this->error("图片上传失败".$info['message'],$this->actionName.'/add');
				}
			}
			
			//  往数据库中添加
			if($this->AdPlan->add()){
							
				$this->success('数据添加成功',$this->actionName.'/index');
			}else{
				$this->error("数据添加失败",$this->actionName.'/add');
			}

		}
	}
	
	/**
	 * $uploadPathDir 上传文件所保存的文件夹名
	 * 上传文件对应的方法
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-5 上午9:43:40
	 */
	private function upload($uploadPathDir){
		
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
}
