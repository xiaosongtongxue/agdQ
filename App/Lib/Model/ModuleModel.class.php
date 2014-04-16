<?php
//自动获取所有node
/*
 * CREATE TABLE `module` (
 * `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 * `name` varchar(45) DEFAULT NULL COMMENT '名称',
 * `app` varchar(45) DEFAULT NULL COMMENT '项目',
 * `group` varchar(45) DEFAULT NULL COMMENT '分组',
 * `module` varchar(45) DEFAULT NULL COMMENT '模块',
 * `function` varchar(45) DEFAULT NULL COMMENT '方法',
 * `status` varchar(45) DEFAULT NULL COMMENT '状态',
 * PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8$$
 * 
 * 
 */
class ModuleModel/*  extends Model */{
	//生成模块结构信息 app/分组/模块/方法
	public function fetch_module(){
		//$M = M('Module');
		//$M->query("truncate table module");
		$app = $this->getAppName();
		$groups = $this->getGroup();
		$n=0;
		foreach ($groups as $group) {
			$modules = $this->getModule($group);
			foreach ($modules as $module) {
				$module_name=$app.'://'.$group.'/'.$module;
				$functions = $this->getFunction($module_name);
				foreach ($functions as $function) {
					$data[$n]['app'] = $app;
					$data[$n]['group'] = $group;
					$data[$n]['module'] = $module;
					$data[$n]['function'] = $function;
					++$n;                               
				}
			}
		}
		//$M->addAll($data);
		//$this->success('所有分组/模块/方法已成功读取到module表中.');
		return $data;
	}
	protected function getAppName(){
		return APP_NAME;
	}
	protected function getGroup(){
		$result = explode(',',C('APP_GROUP_LIST'));
		return $result;
	}
	protected function getModule($group){
		if(empty($group))return null;
		
		if(C('APP_GROUP_MODE') == 1){
			$group_path=APP_PATH.C('APP_GROUP_PATH').'/'.$group.'/Action';
		}else{
			$group_path = LIB_PATH . 'Action/' . $group;
		}
		
		if(!is_dir($group_path))return null;
		$group_path.='/*.class.php';
		$ary_files = glob($group_path);
		foreach ($ary_files as $file) {
			if (is_dir($file)) {
				continue;
			}else {
				$files[] = basename($file,'Action.class.php');
			}
		}
		return $files;
	}
	protected function getFunction($module){
		if(empty($module))return null;
		$action=A($module);
		$functions=get_class_methods($action);
		$inherents_functions = array(
				'_initialize','__construct','getActionName','isAjax','display','show','fetch',
				'buildHtml','assign','__set','get','__get','__isset',
				'__call','error','success','ajaxReturn','redirect','__destruct'
		);
		foreach ($functions as $func){
			if(!in_array($func, $inherents_functions)){
				$customer_functions[]=$func;
			}
		}
		return $customer_functions;
	}
	
}
?>