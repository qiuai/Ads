<?php

/**
 * 
 * 广告联盟 图片广告代码
 * @copyright (C)2013 ZHTS Inc.
 * @project CHAXUNLE.COM
 * @author Yumao <815227173@qq.com>
 * @CreateDate: 2014-1-2 上午10:21:56
 * @version 1.0
 */
class AdImageAction extends AdServiceAction{
	
	/**
	 * 生成图片广告代码
	 * (non-PHPdoc)
	 * @see AdServiceAction::createCode()
	 */
	public function createCode($adManageInfo){
		
		// 产生随机的唯一session值
		$sessionFlag = md5(time().rand(1,100000).$this->zoneId);
		
		// 为sessionFlag产生随机的唯一值
		$sessionFlagValue =  md5(time().rand(1,100000).$this->zoneId);
		
		// 往验证数据表中添加一条数据 (以后这里绝对要放到内存缓存)
		$adShowVerify = M("adShowVerify");
		$data['session_flag'] = $sessionFlag; 
		$data['session_flag_value'] = $sessionFlagValue;
		$adShowVerify->add($data);
	
		
		$this->assign("sessionFlagValue",$sessionFlagValue);
		$this->assign("sessionFlag",$sessionFlag);
		$this->assign("zoneId",$this->zoneId);
		$this->assign("aid",$this->aid);
		// 组装URL
		$jumpUrl = C('SITE_URL').'?m=AdService&a=clickAdJump&zoneId='.$this->zoneId.'&aid='.$adManageInfo['aid'];
		
		$this->assign('jumpUrl',$jumpUrl);
		
		$this->assign('adManageInfo',$adManageInfo);
		
		
		$rs = $this->view->fetch('adImage');
		
		$code = "document.write(\"". $this->jsformat($rs) . "\");";
		
		echo $code;
	}
	
	/**
	 * 	广告的展示
	 * (non-PHPdoc)
	 * @see AdServiceAction::adShow()
	 */
	public function adShow($id){
		

		$this->zoneId = $id;	// 广告位ID
		
		if($zoneInfo = ($this->checkAdExsit())){
				
			$this->sizeId = $zoneInfo['size'];
		
			if($adManageInfo = $this->getAdManageInfo()){		// 服务器端开始计录本次访问
				
				// 调用进行过滤所用的函数 比如有些代码的代码位没有
				
				$this->createCode($adManageInfo);
		
			}else{
				echo '/**没有合适的广告*/';
			}
		}else{
			echo "当前代码位有误 或未启用";
			exit;
		}		
	}
	
	public function jishu(){
		
		
	}
}