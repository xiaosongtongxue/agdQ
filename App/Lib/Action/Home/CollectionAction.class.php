<?php
class CollectionAction extends CommonAction{
	/**
	 * 添加收藏记录
	 * @return array 收藏状态和成功提示
	 */
	public function addCollection(){
		$return  = array('status'=>0,'data'=>L('PUBLIC_FAVORITE_FAIL'));
		if(empty($_POST['source_id'])){
			exit(json_encode(array('status'=>0,'data'=>'','info'=>'收藏失败')));
		}
		//$data['source_table_name'] = t($_POST['stable']);
		$data['source_id'] 	= intval($_POST['source_id']);
		$data['source_table_name'] = isset($_POST['source_table_name']) ? t($_POST['source_table_name']) : 'feed'; 
		//$data['source_app'] = t($_POST['sapp']);

		// 验证资源是否已经被删除
		/*$key = $data['source_table_name'].'_id';
		$map[$key] = $data['source_id'];
		$map['is_del'] = 0;
		$isExist = model(ucfirst($data['source_table_name']))->where($map)->count();*/
		$isExist = M('Feed')->where(array('feed_id'=>$data['source_id']))->count();
		if(empty($isExist)) {
			$return = array('status'=>0, 'data'=>'','info'=>'内容已被删除，收藏失败');
			exit(json_encode($return));
		}
				
		if(D('Collection')->addCollection($data)) {
			$return = array('status'=>1,'data'=>'','info'=>L('PUBLIC_FAVORITE_SUCCESS'));
		} else {
			$return['data'] = D('Collection')->getError();
			empty($return['data']) && $return['data'] = L('PUBLIC_FAVORITE_FAIL');
		}
		exit(json_encode($return));	
	}
	
	/**
	 * 取消收藏
	 * @return array 成功取消的状态及错误提示
	 */
	public function delCollection(){
		$return  = array('status'=>0,'data'=>'');
		if(empty($_POST['source_id']) ||empty($_POST['source_table_name']) ){
			$return['data'] = L('PUBLIC_RESOURCE_ERROR');
			exit(json_encode($return));
		}
		if( D('Collection')->delCollection(intval($_POST['source_id']),t($_POST['source_table_name']))){
			$return = array('status'=>1,'info'=> L('PUBLIC_CANCEL_ERROR'));
		}else{
			$return['info'] = D('Collection')->getError();
		}
		exit(json_encode($return));
	}
}
?>