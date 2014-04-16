<?php
/**
 * Key-Value存储引擎模型 - 数据对象模型
 * Key-value存储引擎，用MySQL模拟memcache等key-value数据库写法
 * 以后可以切换到其它成熟数据库或amazon云计算平台
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class XconfigModel extends Model {

	protected $tableName = 'system_config';
	protected $fields = array(
						0			=> 'id',
						1			=> 'uid',
						2			=> 'list',
						3			=> 'key',
						4			=> 'value',
						5			=> 'mtime',
						'_autoinc'	=> true,
						'_pk'		=> 'id');

	protected $listName = 'global';			// 默认列表名
	
	//键值白名单，主要用于获取和设置配置文件某个
	protected $whiteList = array(
						'site'		=> '');

	/**
	 * 写入参数列表
	 * @param string $listName 参数列表list
	 * @param array $listData 存入的数据，形式为key=>value
	 * @return boolean 是否写入成功
	 */
	public function pageKey_lput($listName = '', $listData = array()){
		// 初始化listName
		$listName = $this->_strip_key($listName);
		$result = false;
		//格式化数据
		if(is_array($listData)){
			$insert_sql = "REPLACE INTO __TABLE__ (`list`, `key`, `value`, `mtime`) VALUES ";
			foreach($listData as $key => $data){
				$insert_sql .= " ('$listName','$key','".serialize($data)."','".date('Y-m-d H:i:s')."') ,";
			}
			$insert_sql = rtrim($insert_sql,',');
			//插入数据列表
			$result = $this->execute($insert_sql);
		}
		$cache_id = '_systen_config_lget_'.$listName;
		F($cache_id,null);	//删除缓存，不要使用主动创建发方式、因为listData不一定是listName的全部值
		
		return $result;
	}

	/**
	 * 读取数据list:key
	 * @param string $list_and_key 要获取的某个参数list:key；如果没有:则认为，只有list没有key
	 * @return string 相应的list中的key值数据
	 */
	public function pagekey_get($list_and_key) {
		$list_and_key = $this->_strip_key($list_and_key);	
		$keys = explode(':', $list_and_key);
		static $_res = array();
		if(isset($_res[$list_and_key])) {
			return $_res[$list_and_key];
		}
		$list = $this->pagekey_lget($keys[0]);
		return $list ? $list[$keys[1]] : '';
	}

	/**
	 * 读取参数列表
	 * @param string $listName 参数列表list
	 * @param boolean $nostatic 是否不使用静态缓存，默认为false
	 * @return array 参数列表
	 */
	public function pagekey_lget($listName = '', $nostatic = false) {

		$listName = $this->_strip_key($listName);

		static $_res = array();
		if(isset($_res[$listName]) && !$nostatic) {
			return $_res[$listName];
		}
		
		$cache_id = '_system_config_lget_'.$listName;
		
		$data = F($cache_id);
		if($data === false || $data =='') {
			$data = array();
			$where['list'] = $listName;
			
			$result	= $this->order('id ASC')->where($where)->select();	
			if($result) {
				foreach($result as $v) {
					$data[$v['key']] = unserialize($v['value']);
				}	
			}
			F($cache_id, $data);
		}
		$_res[$listName] = $data;
		return $_res[$listName];
	}

	/**
	 * 过滤key值
	 * @param string $key 只允许格式，数字字母下划线，list:key不允许出现html代码和这些符号 ' " & * % ^ $ ? ->
	 * @return string 过滤后的key值
	 */
	protected function _strip_key($key = '') {
		if($key == '') {
			return $this->listName;
		} else {
			return preg_replace('/([^0-9a-zA-Z\_\:])/', '', $key);
		}
	}
}