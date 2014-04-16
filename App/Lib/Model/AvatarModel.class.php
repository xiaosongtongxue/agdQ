<?php 
/**
 * 头像模型 - 业务逻辑模型
 * @author xiaoluo <584188065@qq.com> 
 * @version agdQ1.0
 */
class AvatarModel {
	public $_uid;		//用户UID字段
	protected $avatarPath; //头像路径
	
	public function __construct(){
		//$this->avatarPath = __ROOT__.'/Public/Uploads/Images/Avatar';
		$this->avatarPath = UPLOAD_PATH.'/Images/Avatar';
		$this->_uid = intval($_SESSION [C('USER_AUTH_KEY')]);
	}

	/**
	 * 初始化$uid
	 * @param integer $uid 用户UID
	 * @return object
	 */
	public function init($uid){
		$this->_uid = intval($uid);
		return $this;
	}
	
	/**
	 * 判断用户是否上传头像
	 * @return array 
	 */
/*	public function hasAvatar(){
		$original_file_name = '/Avatar'.$this->convertUidToPath($this->_uid).'/original.jpg';
		
		//头像云储存
		$cloud = D('CloudImage');
		if($cloud->isOpen){
			$original_file_info = $cloud->getFileInfo($original_file_name);
			if($original_file_info){
				$filemtime = @intval($original_file_info['date']);
				$avatar = getImageUrl($original_file_name).'!small.avatar.jpg?v'.$filemtime;
			}
		}elseif(file_exists(UPLOAD_PATH.$original_file_name)){
			$filemtime = @filemtime(UPLOAD_PATH.$original_file_name);
			$avatar = getImageUrl($original_file_name,50,50).'?v'.$filemtime;
		}
		return $avatar;
	}*/
	
	/**
	 * 获取用户头像
	 * @return array 用户的头像链接
	 */
	public function getUserAvatar(){
		$empty_url = AVATAR_URL.'/NoAvatar';
		$avatar_url = array(
						'avatar_original'   => $empty_url.'/original.jpg',
						'avatar_big'   => $empty_url.'/big.jpg',
						'avatar_middle'   => $empty_url.'/middle.jpg',
						'avatar_small'   => $empty_url.'/small.jpg',
						'avatar_tiny'   => $empty_url.'/tiny.jpg',
						/*
						 +------------------------------------------------------------------
						 	TODP:添加该头像的拥有者的个人主页的路径
							'user_url' => '',//加入此行代码->已添加，未验证
						 +------------------------------------------------------------------
						 */
						 'user_url' => SITE_URL.'/index.php/User/userProfile?uid='.$this->_uid,
					);
		
		$original_file_path = AVATAR_PATH.$this->convertUidToPath($this->_uid);
		$original_file = $original_file_path.'/big.*';
		
		$file_array = glob($original_file);
		if(isset($file_array[0])){
			$file_info = pathinfo($original_file_path.'/'.$file_array[0]);
			$original_file_name = $original_file_path.'/'.$file_info['basename'];
		
			if(file_exists($original_file_name)){
				//获取头像大中小和微小的图片路径URL，问题是后缀名很多，如何选取-->已解决
				$file_path = AVATAR_URL.$this->convertUidToPath($this->_uid);
				$avatar_url = array(
							'avatar_original'   => $file_path.'/original.jpg',
							'avatar_big'   => $file_path.'/big.jpg',
							'avatar_middle'   => $file_path.'/middle.jpg',
							'avatar_small'   => $file_path.'/small.jpg',
							'avatar_tiny'   => $file_path.'/tiny.jpg',
							/*
							 +------------------------------------------------------------------
								TODP:添加该头像的拥有者的个人主页的路径
								'user_url' => '',//加入此行代码->已添加，未验证
							 +------------------------------------------------------------------
							 */
							'user_url' => SITE_URL.'/index.php/User/userProfile?uid='.$this->_uid,
						);
				return $avatar_url;
			}else{
				return $avatar_url;
			}
		}else{
			return $avatar_url;
		}
	}
	
	/**
	 * 将用户的UID转换为三级路径
	 * @param integer $uid 用户UID
	 * @return string 用户路径
	 */
	public function convertUidToPath($uid) {
		$md5 = md5($uid);
		$sc = '/'.substr($md5, 0, 2).'/'.substr($md5, 2, 2).'/'.substr($md5, 4, 2);
		return $sc;
	}
	
	/**
	 * 获取保存的avatar的路径,
	 * @param integer $uid 用户UID
	 * @return string 用户路径
	 */
	public function getAvatarPath() {
		//return $this->avatarPath.$this->convertUidToPath($this->_uid);
		return AVATAR_PATH.$this->convertUidToPath($this->_uid);
	}
	
	/**
	 * 获取上传的avatar的路径
	 * @param integer $uid 用户UID
	 * @return string 用户路径
	 */
	public function getAvatarUrl() {
		$url = AVATAR_URL.$this->convertUidToPath($this->_uid);
		$url = ltrim($url, './');
		$url = ltrim($url, '/');
		$url = ltrim($url, '//');
		//$path = str_replace('agdQ/','',$path);
		return $url;
	}

	/**
	 * 创建多级文件目录
	 * @param string $path 路径名称
	 * @return void
	 */
	public function _createFolder($path){
		if(!is_dir($path)){
			$this->_createFolder(dirname($path));
			mkdir($path, 0777, true);
		}
	}
	/*
	 * 将URL转化为PATH
	 * 
	 */
	public function convertUrlToPath($url){
		//URL有效性检查
		if(substr_count($url,SITE_URL)){
			return str_replace(SITE_URL,SITE_PATH,$url);
		}
		return false;
	}
}