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
class AdPlanAction extends Action{
	
	/**
	 * 
	 * 计划列表首页
	 * @author Yumao <815227173@qq.com>
	 * @CreateDate: 2013-12-4 上午11:26:12
	 */
	public function index(){
		echo "index";
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
		$AdPlan = D('AdPlan');
		if(!$AdPlan->create()){
			
			//echo $AdPlan->getError();
			$this->error($AdPlan->getError(),'AdPlan/add');			
		}else{
			
			//  往数据库中添加
			if($AdPlan->add()){
				$this->success('数据添加成功','AdPlan/index');
			}else{
				$this->error("数据添加失败",'AdPlan/add');
			}

		}
	}
}
