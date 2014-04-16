<?php
/**
 * 标签模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class TagModel extends Model{
	
	private $_error = null;    // 错误信息
	private $_table_prefix = null;		// 表前缀
		
	/**
	 * 初始化方法
	 * @return void
	 */
	public function _initialize() {
		$this->_table_prefix = C('DB_PREFIX');	//表前缀为'q_'
	}
	
	/**
	 * 通过标签名称，获取标签编号
	 * @param string $name 标签
	 * @return integer 标签编号
	 */
	public function getTagId($tag_name){
		$name = getShort(t($tag_name), 15);	//十五个中文或三十个字符
		if(!$name || !is_string($name)){
			$this->_error = L('PUBLIC_TAG_EMPTY'); 	//标签名为空
			return false;
		}
		$where['name'] = $name;
		$result = $this->where($where)->find();
		if(!$result){
			//return false;
			// 没有该标签则创建,可是觉得这样不太好
			$add['name'] = $where['name'];
			$tag_id = $this->add($add);
			$result['tag_id'] = $tag_id;
			$result['name'] = $name;
		}
		
		return $result['tag_id'];
	}

	/**
	 * 通过标签编号，获取标签内容
	 * @param array $tag_ids 标签ID数组
	 * @return array 标签内容列表
	 */
	public function getTagNames($tag_ids){
		if(!is_array($tag_ids)){
			$tag_ids = explode(',', $tag_ids);
		}
		if(is_array($tag_ids)){
			$where['tag_id'] = array('IN', $tag_ids);
		}else if(intval($tag_ids) > 0){
			$where['tag_id'] = $tag_ids;
		}else{
			return '';
		}
		$result = $this->where($where)->select();
		$_result = array();
		foreach($result as $v => $s){
			$_result[$s['tag_id']] = $s['name'];
			unset($result[$k]);
		}
		
		return $_result;
	}

	/**
	 * 获取全局标签列表
	 * @param array $where 查询条件
	 * @param string $field 显示字段名称，多个用","分割
	 * @param string $order 排序条件，默认tag_id DESC
	 * @param integer $limit 结果集数目，默认为全部
	 * @return array 全局标签列表
	 */
	public function getTagList($where = null, $field = null, $order = 'tag_id DESC', $limit = null) {
		$result = $this->field($field)->where($where)->order($order)->limit($limit)->select();
		return $result;
	}


	/**
	 * 添加全局标签
	 * @param string $tags 标签
	 * @return boolean 是否添加成功
	 */
	public function addTags($tags){
		if(empty($tags)){
			$this->_error = L('PUBLIC_TAG_NOEMPTY');			// 标签不能为空 
			return false;
		}
		!is_array($tags) && $tags = explode(',',preg_replace('/[，,]+/u', ',', $tags));
		$tags = array_filter($tags);	//不知道此函数有何用，没有任何效果
		foreach($tags as $k => $v) {
			$tags[$k] = mysql_escape_string(t(preg_replace('/^[\s　]+|[\s　]+$/', '', $tags[$k])));
			if (!$tags[$k]) {
				unset($tags[$k]);
			}
		}
		if(empty($tags)) {
			$this->error = L('PUBLIC_TAG_NOEMPTY');			// 标签不能为空
			return false;
		}
		// 检测已有标签
		$where['name'] = array('IN', $tags);
		$existing_tags = $this->where($where)->select();
		$existing_tags = getSubByKey($existing_tags, 'name');
		// 过滤已有标签
		$tags = array_diff($tags, $existing_tags);
		if(empty($tags)) {
			$this->_error = L('PUBLIC_TAG_EXIST');			// 标签已经存在
			return false;
		}
		
		$sql = 'INSERT INTO '.$this->tablePrefix.'tag(`name`) VALUES (\''.implode("'),('", $tags).'\')';
		if(false !== $this->execute($sql)) {
			$this->error = L('PUBLIC_TAG').L('PUBLIC_SAVE_SUCCESS');		// 标签保存成功
			return true;
		} else {
			$this->error = L('PUBLIC_TAG').L('PUBLIC_SAVE_FAIL');			// 标签保存失败
			return false;
		}
	}
	
	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
}