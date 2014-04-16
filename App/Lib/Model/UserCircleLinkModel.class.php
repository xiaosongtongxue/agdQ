<?php
/**
 * 用户圈关联模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class UserCircleLinkModel extends Model {

/*	protected $tableName = 'user_circle_link';
	protected $fields =	array(
							0 =>'id',
							1=>'uid',
							2=>'user_circle_id'
						);*/
	
	/**
	 * 转移用户的用户圈
	 * @param string $uids 用户UID，多个用","分割
	 * @param string $user_circle_id 用户组ID，多个用","分割
	 * @return boolean 是否转移成功
	 */
	public function domoveUserCircle($uids, $user_circle_ids) {
		// 验证数据
		if(empty($uids) && empty($user_circle_ids)) {
			$this->error = L('PUBLIC_USER_CIRCLE_OR_USER_EMPTY');			// 用户圈或用户不能为空
			return false;
		}
		$uids = explode(',', $uids);
		$user_circle_ids = explode(',', $user_circle_ids);
		$uids = array_unique(array_filter($uids));
		$user_circle_ids = array_unique(array_filter($user_circle_ids));
		if(!$uids || !$user_circle_ids) {
			return false;
		}
		$where['uid'] = array('IN', $uids);
		$this->where($where)->delete();
		foreach($uids as $v) {
			$save = array();
			$save['uid'] = $v;
			foreach($user_circle_ids as $gv){
				$save['user_group_id'] = $gv;
				$this->add($save);
			}
			// 清除权限缓存
			D('Cache')->rm('perm_user_'.$v);
			D('Cache')->rm('user_group_'.$v);
		}
		D('User')->cleanCache($uids);

		return true;
	}

	/**
	 * 获取用户的用户圈信息
	 * @param array $uids 用户UID数组
	 * @return array 用户的用户圈信息
	 */
	public function getUserCircle($uids) {
		$uids = !is_array($uids) ? explode(',', $uids) : $uids;
		$uids = array_unique(array_filter($uids));
		if(!$uids) 
			return false;
	
		$return = array();
		foreach ($uids as $uid){
			$return[$uid] = D('Cache')->get('user_circle_'.$uid);
			if($return[$uid] == false){
				$where['uid'] = $uid;
				$list = $this->where($where)->select();
				$return[$uid] = getSubByKey($list, 'user_circle_id');
				D('Cache')->set('user_group_'.$uid, $return[$uid]);
			}
		}
		return $return;
	}	
	
	/**
	 * 获取用户圈所在的所有用户详细信息
	 * @param array $user_circle_id 圈子ID
	 * @return array 用户圈所在的所有用户详细信息
	 */
	public function getAllUserInfoByCircleId($user_circle_id){
		$where['user_circle_id'] = $user_circle_id;
		$uids = $this->where($where)->select();
		$uids = getSubByKey($uids, 'uid');
		return D('User')->getUserInfoByUids($uids);
	}
	
	/**
	 * 获取用户圈所在的除去创建者以外的所有用户详细信息
	 * @param array $user_circle_id 圈子ID
	 * @return array 用户圈所在的除去创建者以外的所有用户详细信息
	 */
	public function getUserInfoByCircleId($user_circle_id){
		$where['user_circle_id'] = $user_circle_id;
		$uids = $this->where($where)->select();
		$uids = getSubByKey($uids, 'uid');
		$exUid = D('UserCircle')->getCircleAdminInfo($user_circle_id);
		$exUids = array_diff($uids, $exUid);
		return D('User')->getUserInfoByUids($exUids);
	}
	
	/**
	 * 获取用户所在用户圈详细信息
	 * @param array $uids 用户UID数组
	 * @return array 用户的用户圈详细信息
	 */
	/*public function getUserCircleInfo($uids){
		$uids = !is_array($uids) ? explode(',', $uids) : $uids;
		$uids = array_unique(array_filter($uids));
		if(!$uids) 
			return false;
			
		$userCids = $this->getUserCircle($uids);

		$result = array();
		foreach ( $userCids as $ug){
			if ( $result ){
				$ug && $result = array_merge( $result , $ug );
			} else {
				$result = $ug;
			}
		}
		//把所有用户组信息查询出来
		$ucresult = D('UserCircle')->getUserCircleByCids(array_unique($result));
		$circleresult = array();
		foreach ( $ucresult as $ur ){
			$circleresult[$ur['user_circle_id']] = $ur;
		}
		foreach($userCids as $k=>$v){
			$ucircle = array();
			foreach ( $userCids[$k] as $userc){
				$ucircle[] = $circleresult[$userc];
			}
			$userCircleData[$k] = $ucircle;
			foreach($userCircleData[$k] as $key => $value) {
				if(isset($value['user_circle_icon']) && $value['user_circle_icon'] == -1) {
					unset($userCircleData[$k][$key]);
					continue;
				}
				$userCircleData[$k][$key]['user_circle_icon_url'] = THEME_PUBLIC_URL.'/image/usergroup/'.$value['user_circle_icon'];
			}
		}
		return $userCircleData;
	}*/
}
