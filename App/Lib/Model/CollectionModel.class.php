
<?php
/**
 * 收藏模型 - 数据对象模型
 * @author icubit <icubit@qq.com>
 * @version agdQ1.0
 */
class CollectionModel extends Model {
	/**
	 * 添加收藏记录
	 * @param array $data 收藏相关数据
	 * @return boolean 是否收藏成功
	 */
	public function addCollection($data) {
    	// 验证数据
		if(empty($data['source_id']) || empty($data['source_table_name'])){
			$this->error = L('PUBLIC_RESOURCE_ERROR');			// 资源ID,资源所在表名,资源所在应用不能为空
			return false;
		}
		// 判断是否已收藏
		//$isExist = $this->getCollection($data['source_id'], $data['source_table_name']);
		$isExist = $this->where(array('source_id'=>$data['source_id'],'uid'=>$_SESSION[C(USER_AUTH_KEY)]))->count();
		if(!empty($isExist)) {
			$this->error = L('PUBLIC_FAVORITE_ALREADY');		// 您已经收藏过了
			return false;
		}

		$data['uid'] = !$data['uid'] ? $_SESSION[C(USER_AUTH_KEY)] : $data['uid'];
		$data['source_id'] = intval($data['source_id']);
		$data['source_table_name'] = t($data['source_table_name']);
		//$data['source_app'] = t($data['source_app']);
		$data['ctime'] = time();
		if($data['collection_id'] = $this->add($data)) {
			// 收藏数加1
			D('UserData')->updateKey('favorite_count', 1);
			D('Feed')->where(array('feed_id'=>$data['source_id']))->setInc('collection_count',1);
			return true;
		}else{
			$this->error = L('PUBLIC_FAVORITE_FAIL');		// 收藏失败,您可能已经收藏此资源
			return false;
		}
    }

	/**
	 * 取消收藏
	 * @param integer $sid 资源ID
	 * @param string $source_table_name 资源表名称
	 * @param integer $uid 用户UID
	 * @return boolean 是否取消收藏成功
	 */
	public function delCollection($source_id, $source_table_name='feed', $uid = '') {
		// 验证数据
		if(empty($source_id) || empty($source_table_name)) {
			$this->error = L('PUBLIC_WRONG_DATA');		// 错误的参数
			return false;
		}

		$uid = empty($uid) ?$_SESSION[C(USER_AUTH_KEY)] : $uid;
		$map['uid'] = $uid;
		$map['source_id'] = $source_id;
		$map['source_table_name'] = $source_table_name;
		// 取消收藏操作
		if($this->where( $map )->delete()){
			// 设置缓存
			//model('Cache')->set('collect_'.$uid.'_'.$stable.'_'.$sid, '');
			//model('Cache')->rm('coll_count_'.$stable.'_'.$sid);
			// 收藏数减1
			D('UserData')->updateKey('favorite_count', -1);
			return true;
		} else {
			$this->error = L('PUBLIC_CANCEL_FAVORITE_FAIL');		// 取消失败,您可能已经取消了该信息的收藏
			return false;
		}
	}
}
?>