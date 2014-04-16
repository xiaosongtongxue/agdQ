<?php
/**
 * 标签应用模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class TagAppModel extends Model{

	private $_app_name = null;					// 所属应用
	private $_table = null;						// 所属资源表
	private $_table_prefix = null;				// 表前缀
	private $_error = null;   					// 错误信息

	/**
	 * 初始化方法
	 * @return void
	 */
	public function _initialize() {
		$this->_table_prefix = C('DB_PREFIX');	//表前缀为'q_'
	}

	/**
	 * 设置所属应用
	 * @param string $app 应用名称
	 * @return object 标签对象
	 */
	public function setAppName($app_name){
		$this->_app_name = $app_name;
		return $this;
	}

	/**
	 * 设置相关内容所存储的表格
	 * @param string $table 数据表名
	 */
	public function setTable($table){
		$this->_table = $table;
		return $this;
	}

	/**
	 * 通过指定的应用资源ID，获取应用的内容标签
	 * @param array|integer $row_ids 所属应用的内容编号或用户编号
	 * @return array 标签内容列表
	 * @example D('TagApp')->setTable('user')->getAppTags(array(1,2));
	 */
	public function getAppTags($row_ids){
		$where['row_id'] = is_array($row_ids) ? array('IN', $row_ids) : $row_ids;
		$where['table'] = $this->_table;
		$app_tags = $this->where($where)->select();	
		// 获取标签名称
		$names = D('Tag')->getTagNames(getSubByKey($app_tags, 'tag_id'));
		// 重组结果
		$result = array();
		foreach($app_tags as $k => $v){
			$result[$v['row_id']][$v['tag_id']] = $names[$v['tag_id']];
			unset($app_tags[$k]);
		}
		return $result;
	}

	/**
	 * 设置指定应用下的应用内容标签
	 * @param integer $row_id 应用内容编号
	 * @param array $tags 标签
	 * @param integer $max 最多标签数量
	 * @return boolean 是否设置成功
	 * @example D('TagApp')->setAppName('public')->setTable('user')->setAppTags(2, array('小妹', '哈sada哈哈'));
	 */
	public function setAppTags($row_id, $tags, $max = 9){
		$row_id = intval($row_id);
		if(!$this->_app_name || !$this->_table){
			$this->_error = L('PUBLIC_WRONG_DATA');			// 错误的参数
			return false;
		}else if(!$row_id){
			$this->_error = L('PUBLIC_WRONG_DATA');			// 错误的参数
			return false;
		}
		
		//标签
		if(!$tags){
			$tags = array();
		}else if(is_string($tags)){
			$tags = explode(',', preg_replace('/[，,]+/u', ',', $tags));
		}else if(!is_array($tags)){
			$this->_error = L('PUBLIC_WRONG_DATA');			// 错误的参数
			return false;
		}
		
		//限制最大标签数
		$tags = array_slice($tags, 0, intval($max));
		//删除历史设置
		$del_where['table'] = $this->_table;
		$del_where['row_id'] = $row_id;
		$res = $this->where($del_where)->delete();
		//添加新设置
		$data = array();
		$add['app_name'] = $this->_app_name;
		$add['table'] = $this->_table;
		$add['row_id'] = $row_id;
		foreach($tags as $v){
			$add['tag_id'] = D('Tag')->getTagId($v);
			$data[] = $add;
		}
		false !== $res && $res = $this->addAll($data);
		return $res;
	}

	/**
	 * 一次添加多个应用内容的标签
	 * @param integer $row_id 应用内容编号
	 * @param string $tags 标签
	 * @return boolean 是否添加成功,成功是返回所有的标签ID和名称的数组,失败时返回false
	 * @example D('TagApp')->setAppName('[应用内容或用户所在的应用名称,比如public]')->setTable('[应用内容或用户所在的表名]')->addAppTag('[应用内容ID或用户ID]',array('标签一','标签二'));
	 */
	public function addAppTags($row_id, $tags){
		if(!is_array($tags)){
			$tags = explode(',',preg_replace('/[，,]+/u', ',', $tags));
		}
		//获取结果集
		$r = array();
		foreach($tags as $t){
			if(empty($t)){
				continue;
			}
			if($tag = $this->addAppTag($row_id, $t)){
				$r[] = $tag;
			}
		}
		
		return $r;
	}

	/**
	 * 添加应用内容的标签
	 * @param integer $row_id 应用内容编号
	 * @param string $tag 标签
	 * @return boolean 是否添加成功,成功返回该标签的array('tag_id' => '[ID]','name' => '[$tag]'),失败返回false
	 * @example D('TagApp')->setAppName('[应用内容或用户所在的应用名称,比如public]')->setTable('[应用内容或用户所在的表名]')->addAppTag('[应用内容ID或用户ID]','[标签名]');
	 */
	public function addAppTag($row_id, $tag){
		$data['app_name'] = $this->_app_name;
		$data['table'] = $this->_table;
		$data['row_id'] = intval($row_id);
		$data['tag_id'] = D('Tag')->getTagId($tag);
		
		if($data['tag_id'] && 0 == ($result = $this->where($data)->count())){
			$result = $this->add($data);
			if($result){
				$this->_error = L('PUBLIC_TAG').L('PUBLIC_ADD_SUCCESS');		// 标签，添加成功
				return array('tag_id' => $data['tag_id'], 'name' => $tag);
			}else{
				$this->_error =  L('PUBLIC_TAG').L('PUBLIC_ADD_FAIL');		// 标签，添加失败
				return false;
			}
		}else{
			$this->_error = L('PUBLIC_TAG_EXIST');			// 标签已经存在
			return false;
		}
	}

	/**
	 * 删除应用内容的标签
	 * @param integer $row_id 应用内容编号
	 * @param integer $tag_id 标签编号
	 * @return boolean 是否删除成功
	 * @example D('TagApp')->setTable('[应用内容或用户所在的表名]')->deleteAppTag('[应用内容ID或用户ID]','[标签ID]');
	 */
	public function deleteAppTag($row_id, $tag_id){
		$where['table'] = $this->_table;
		$where['row_id'] = intval($row_id);
		$where['tag_id'] = intval($tag_id);
		if(empty($where['row_id'])){
			$ids = D('Cache')->get('temp_'.$where['table'].$_SESSION[C('USER_AUTH_KEY')]);
			if(!empty($ids)){
				if(in_array($where['tag_id'], $ids)){
					unset($ids[array_search($hwere['tag_id'], $ids)]);
					D('Cache')->set('temp_'.$where['table'].$_SESSION[C('USER_AUTH_KEY')], $ids);
				}
			}
			$this->_error = L('PUBLIC_DELETE_FAIL');				// 删除失败
			return false; 
		}
		
		if(false !== $this->where($where)->delete()){
			$this->_error = L('PUBLIC_TAG').L('PUBLIC_DELETE_SUCCESS');			// 删除成功
			return true;
		} else {
			$this->_error = L('PUBLIC_TAG').L('PUBLIC_DELETE_FAIL');				// 删除失败
			return false;
		}
	}
	
	/**
	 * 删除应用指定资源的标签信息
	 * @param integer $row_id 应用内容编号
	 * @return integer 0表示删除失败，1表示删除成功
	 * @example D('TagApp')->setAppName('[应用内容或用户所在的应用名称,比如public]')->setTable('[应用内容或用户所在的表名]')->deleteSourceTag('[应用内容ID或用户ID]');
	 */
	public function deleteSourceTag($row_id) {
		$where['app_name'] = $this->_app_name;
		$where['table'] = $this->_table;
		$where['row_id'] = intval($row_id);
		$res = $this->where($where)->delete();
		return $res;
	}

	/**
	 * 获取应用标签列表
	 * @param array $where 查询条件
	 * @param integer $limit 结果集数目，默认为无数个
	 * @return array 应用列表标签列表
	 */
/*	public function getAppTagList($where, $limit = null) {
		//$table = $this->tablePrefix.'app_tag AS a LEFT JOIN '.$this->tablePrefix.'tag AS b ON a.tag_id = b.tag_id';
		$result = $this->Table(array($this->tablePrefix.'tag_app' => 'tag_id', $this->tablePrefix.'tag' => 'tag_id'))->where($where)->limit($limit)->select();
		return $result;
	}*/

	/**
	 * 获取热门标签 
	 * @param integer $limit 结果集数目，默认为20
	 * @param integer $expire 缓存时间，默认为3600
	 * @return array 热门标签列表
	 * @example D('TagApp')->setAppName('[应用内容或用户所在的应用名称,比如public]')->setTable('[应用内容或用户所在的表名]')->getHotTags('[结果集数目]','[缓存时间]');
	 */
	public function getHotTags($limit = 20, $expire = 3600){
		$hot_tag_list = array();
		$cache_id = $this->_app_name.$this->_table.'_hot_tag';
		if(($hot_tag_list = S($cache_id)) === false){
			$limit = is_numeric($limit) ? $limit : 20 ;
			$hot_tag_ids = $this->field("`tag_id`, COUNT('tag_id') AS `count`")
							->group("`tag_id`")->order('`count` DESC')
							->limit($limit)->select();
			//获得标签文字
			$hot_names = D('Tag')->getTagNames(getSubByKey($hot_tag_ids, 'tag_id'));
			foreach($hot_tag_ids as $v){
				$hot_tag_list[$v['tag_id']] = array(
												'name'  => $hot_names[$v['tag_id']],
												'count' => $v['count']
											);
			}
			S($cache_id, $hot_tag_list, $expire);
			unset($hot_tag_ids);
		}
		return $hot_tag_list;
	}

	/**
	 * @param integer $row_id 资源ID
	 * @param array $tagIds 标签ID数组
	 * @return void
	 * @example D('TagApp')->setAppName('[应用内容或用户所在的应用名称,比如public]')->setTable('[应用内容或用户所在的表名]')->updateTagData('[应用内容ID或用户ID]','[标签数组ID]');
	 */
	public function updateTagData($row_id, $tagIds){
		$where['table'] = $this->_table;
		$where['row_id'] = $row_id;
		// 删除原有数据
		$res = $this->where($where)->delete();
		
		$data['app_name'] = $this->_app_name;
		$data['table'] = $this->_table;
		$data['row_id'] = $row_id;
		
		$tags = array();
		if(!empty($tagIds) && is_array($tagIds)) {
			foreach($tagIds as $v) {
				$data['tag_id'] = $v;
				$tags[] = $data;
			}
			// 添加新的标签信息
			false !== $res && $res = $this->addAll($data);
			$this->_error = L('PUBLIC_TAG').L('PUBLIC_UPDATE_SUCCESS');		//标签，更新成功
			return $res;
		}
		$this->_error = L('PUBLIC_TAG').L('PUBLIC_UPDATE_FAIL');	// 标签，更新失败
		return false;
	}
	
	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
}