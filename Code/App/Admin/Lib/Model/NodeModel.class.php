<?php
// 节点模型
class NodeModel extends CommonModel {
    protected $_validate	=	array(
		array('module_name','require','权限模块名称不能为空'),	//默认情况下用正则进行验证
		array('module','require','父模块不能为空'),
		array('action','require','操作不能为空'),
		array('action','checkNode','节点已经存在',0,'callback'),
        );

    public function checkNode() {
        $map['module']	 =	 $_POST['module'];
        $map['pid']	=	isset($_POST['pid'])?$_POST['pid']:0;
        $map['status'] = 1;
        if(!empty($_POST['id'])) {
            $map['id']	=	array('neq',$_POST['id']);
        }
        $result	=	$this->where($map)->field('id')->find();
        if($result) {
            return false;
        }else{
            return true;
        }
    }
}