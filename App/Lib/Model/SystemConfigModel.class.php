<?php

class SystemConfigModel extends Model{
	//得到系统配置信息
	public function getSystemConfig($key){
	//	$SystemConfig = M('SystemConfig');
		$condision['key'] = $key;
		$value = $this->where($condision)->getField('value');
		return $value;
	}
	//更新站点信息
	public function saveSiteConfig($arr){
		$bResult = false;
		foreach($arr as $key => $value){
			if($this->where(array('key'=>$key))->setField('value',$value))
				$bResult = true;
		}
		return $bResult;
	}
}

?>