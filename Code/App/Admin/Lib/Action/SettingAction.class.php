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
class SettingAction extends CommonAction {
    public function index(){
    	$this->display();
	}
	/**
	 * 更新系统设置
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 上午9:39:11
	 */
	public function setConfig(){
		if($this->isPost()){
			$config = "setting";	// 配置文件变量名
			
			$data = "<?php\r\n";
			$data .= "\$$config = array(\r\n";
			
			$union = $this->varName();
			foreach($union as $key=>$value){
				foreach ($_POST as $k=>$v){
					if($key == $k){
						$value = $v;
					}
				}
				$data .= "\t'" . $key . "'\t\t\t=>\t\t'".	$value . "',\r\n";
			}
			
			$data .= ");\r\n";
			$data .= "return \$$config;\r\n";
			$data .= "?>";
			
			$filePath = ROOT_PATH . '/Conf/config.setting.php';
			file_put_contents($filePath, $data);	
		}
	}
	/**
	 * 读取系统配置
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 上午9:43:35
	 */
	public function getConfig(){
		$filePath = ROOT_PATH . '/Conf/config.setting.php';
		if(file_exists($filePath)){
			require $filePath;
		}
		$this->assign("config",$setting);
	}
	/**
	 * 变量配置
	 *
	 * @author Vonwey <VonweyWang@gmail.com>
	 * @CreateDate: 2013-11-29 上午10:02:14
	 */
	public function varName(){
		// 基本设置
		$data['union_name'] = '';	// 联盟名称
		$data['union_flag'] = '';	// 广告标识
		
		// 提现设置
		
		return $data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}