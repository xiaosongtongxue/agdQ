<?php
/**
 * 账号体系设置控制器
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class AccountAction extends CommonAction{

	private $settingMenu = array(
			array('menu'=>'baseInfo',      'name'=>'基本信息', 'href'=>'__URL__/baseInfo'),
			array('menu'=>'avatarSetting', 'name'=>'头像设置', 'href'=>'__URL__/avatarSetting'),
			array('menu'=>'modifyPassword','name'=>'修改密码', 'href'=>'__URL__/modifyPassword'),
			array('menu'=>'domain',        'name'=>'个性域名', 'href'=>'__URL__/domain'),
			array('menu'=>'privacySetting','name'=>'隐私设置', 'href'=>'__URL__/privacySetting'),
			array('menu'=>'notifySetting', 'name'=>'通知设置', 'href'=>'__URL__/notifySetting'),
			array('menu'=>'blacklist',     'name'=>'黑名单',   'href'=>'__URL__/blacklist'),
	);
	
	/**
	 * 基本设置页面
	 */
	public function baseInfo(){
		//layout('User/layout');
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','baseInfo');
		$this->setTitle('基本信息')->display();
	}
	
	/**
	 * 头像设置页面
	 */
	public function avatarSetting(){
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','avatarSetting');
		$this->setTitle('头像设置')->display();
	}
	
	/**
	 * 修改登录用户账号密码页面
	 */
    public function modifyPassword() {
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','modifyPassword');
		$this->setTitle('修改密码')->display();
    }
	
	/**
	 * 个性域名页面
	 */
	public function domain(){
		//layout('User/layout');
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','domain');
		$this->setTitle('个性域名')->display();
	}
	
	/**
	 * 隐私设置页面
	 */
	public function privacySetting(){
		//layout('User/layout');
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','privacySetting');
		$this->setTitle('隐私设置')->display();
	}
	
	/**
	 * 通知设置页面
	 */
	public function notifySetting(){
		//layout('User/layout');
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','notifySetting');
		$this->setTitle('通知设置')->display();
	}
	
	/**
	 * 黑名单页面
	 */
	public function blacklist(){
		//layout('User/layout');
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','blacklist');
		$this->setTitle('黑名单')->display();
	}

	/**
	 * 处理基本设置操作
	 */
	public function doBaseInfo() {
	}
	
	/**
	 * 处理修改登录用户账号密码操作
	 * @return json 返回操作后的JSON信息数据
	 */
    public function doModifyPassword() {
		$oldPwd = t($_POST['oldPwd']);
		$newPwd = t($_POST['newPwd']);
		$confirmPwd = t($_POST['confirmPwd']);
		if(empty($oldPwd) || empty($newPwd) || empty($confirmPwd)){
			$this->error('密码不能为空，请重新输入！');
		}
		if($newPwd != $confirmPwd){
			$this->error('新密码和确认密码不一致，请重新输入！');
		}
		$where['uid'] = $this->uid;
		$userInfo = M('User')->where($where)->find();
		if(!$userInfo){
			$this->error('对不起，你的账号有误，请和管理员联系，谢谢！');
		}else{
			if($userInfo['pwd'] != md5($oldPwd)){
				$this->error('对不起，你输入的旧密码错误，请重新输入！');
			}else{
				$save['pwd'] = md5($newPwd);
				$user = M('User')->where($where)->save($save);
				if($user === false){
					$this->error('对不起，修改密码失败，请重新试一次！');
				}else{
					$this->success('修改密码成功！');
				}
			}
		}
    }
	
	/**
	 * 处理个性域名操作
	 */
	public function doDomain(){
	}
	
	/**
	 * 处理隐私设置操作
	 */
	public function doPrivacySetting(){
	}
	
	/**
	 * 处理通知设置操作
	 */
	public function doNotifySetting(){
	}
		
	/**
	 * 处理黑名单操作
	 */
	public function doBlacklist(){
	}
	
	//上传头像
	public function uploadImg(){
		//完整的头像路径
		$path = D('Avatar')->getAvatarPath().'/';
		$url = D('Avatar')->getAvatarUrl().'/';
		
		//临时处理，后面在进行规范化
		$system_default['attach_path_rule'] = 'Y/md/H/'; //三级路径，年/月日/时
		$default_options['custom_path'] = date($system_default['attach_path_rule']); //定义的上传目录规则
		$default_options['save_path'] = ATTACH_PATH.'/'.$default_options['custom_path'];	
		$temp_path = $default_options['save_path'];
		$temp_url = ATTACH_URL.'/'.$default_options['custom_path'];
		
		D('Avatar')->_createFolder($temp_path);
		//若为第二次上传头像，则删除上次传的头像
		//$file_array = glob($path.'original.*');
		//isset($file_array[0]) && @unlink($path.$file_array[0]);
		
		import('App.ORG.UploadFile');
		$upload = new UploadFile();						// 实例化上传类
		$upload->maxSize = 2*1024*1024;					//设置上传图片的大小
		$upload->allowExts = array('jpg','png','gif');	//设置上传图片的后缀
		$upload->uploadReplace = true;					//同名则替换
		$upload->saveRule = time().$this->uid;					//时间戳+uid

		$upload->savePath = $temp_path;
		
		if(!$upload->upload()) {						// 上传错误提示错误信息
			$this->ajaxReturn('',$upload->getErrorMsg(),0,'json');
		}else{											// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			/*$temp_size = getimagesize($path.$info['savename']);
			if($temp_size[0] < 100 || $temp_size[1] < 100){//判断宽和高是否符合头像要求
				$this->ajaxReturn(0,'图片宽或高不得小于100px！',0,'json');
			}*/
			
			$this->ajaxReturn($temp_url.$info[0]['savename'],$info,1,'json');
		}
	}

	/**
	 * 处理头像设置操作
	 */
	public function doSaveAvatar() {
		//裁剪并保存用户头像
		//图片裁剪数据
		$params = $_POST;						//裁剪参数
		if(!isset($params) && empty($params)){
			return;
		}
	
		//头像目录地址
		$avatar_path = D('Avatar')->getAvatarPath();
	
		/* //若为第二次上传头像，则删除上次传的头像
			$file_array = glob($avatar_path.'/*.*');
		//isset($file_array[0]) && @unlink($path.$file_array[0]);
		if(isset($file_array)){
		foreach($file_array as $fileName){
		@unlink($avatar_path.'/'.$file_Name);
		}
		} */
		//保存前先清空
		clear_dir($avatar_path);
	
		//原图的图片路径
		//$original_path = $avatar_path.'/original.jpg';
		$src_path = D('AVATAR')->convertUrlToPath($_POST['src']);
		if(!$src_path) return ;
		//要保存裁剪原图之后的图片路径
		$original_path = $avatar_path.'/original.jpg';
		//$big_path = $avatar_path.'/big.jpg';
	
		import('ORG.Util.Image.ThinkImage');
		$Think_img = new ThinkImage(THINKIMAGE_GD);
		//裁剪原图
		$Think_img->open($src_path)->crop($params['w'],$params['h'],$params['x'],$params['y'])->save($original_path);
		//生成缩略图
		$Think_img->open($original_path)->thumb(200,200, 1)->save($avatar_path.'/big.jpg');
		$Think_img->open($original_path)->thumb(100,100, 1)->save($avatar_path.'/middle.jpg');
		$Think_img->open($original_path)->thumb(64,64, 1)->save($avatar_path.'/small.jpg');
		$Think_img->open($original_path)->thumb(32,32, 1)->save($avatar_path.'/tiny.jpg');
		$this->success('上传头像成功',U('baseInfo'));
	}
	
	/**
     * 附件上传
     * @return array 上传的附件的信息
     */
	public function uploadAttach(){
			$data['upload_type'] == 'Attach';

			$input_options['app_name'] = t($_GET['app_name']);//'app_name'    => t($input_options['app_name']),
			$input_options['table'] = t($_GET['table']);//'table'       => t($input_options['table']),
			//========$input_options['row_id'] = '';//'row_id'      => t($input_options['row_id']),
			$input_options['attach_type'] = 'image';//'attach_type' => t($input_options['attach_type']),
			//========$input_options['uid'] = '';//'uid'         => (int) $input_options['uid'] ? $input_options['uid'] : $_SESSION[C('USER_AUTH_KEY')],
			//========$input_options['private'] = '';//'private'     => $input_options['private'] > 0 ? 1 : 0,
			//========$input_options['from'] = '';//'from'
			
		    $info = D('Attach')->upload($data, $input_options);
			$this->ajaxReturn($info['info'][0]['name'], $info['info'], $info['status']+2);
		}
	/**
     * 图片上传
     * @return array 上传的附件的信息
     */
	public function uploadImage(){
			$data['upload_type'] == 'image';
			$xdata = D('Xdata')->get('admin_Config:image');
			$xdata['attach_allow_extension'] = explode(',', $xdata['attach_allow_extension']);
			$input_options['max_size'] = floatval($xdata['attach_max_size']) * 1024 * 1024;//单位为：兆
			$input_options['allow_exts'] = $xdata['attach_allow_extension'];
 
			$input_options['app_name'] = t($_GET['app_name']);//'app_name'    => t($input_options['app_name']),
			$input_options['table'] = t($_GET['table']);//'table'       => t($input_options['table']),
			//========$input_options['row_id'] = '';//'row_id'      => t($input_options['row_id']),
			$input_options['attach_type'] = 'image';//'attach_type' => t($input_options['attach_type']),
			//========$input_options['uid'] = '';//'uid'         => (int) $input_options['uid'] ? $input_options['uid'] : $_SESSION[C('USER_AUTH_KEY')],
			//========$input_options['private'] = '';//'private'     => $input_options['private'] > 0 ? 1 : 0,
			//========$input_options['from'] = '';//'from'
			
		    $info = D('Attach')->upload($data, $input_options);
			D('Cache')->set('ee', $info);
			$this->ajaxReturn(ATTACH_URL.'/'.$info['info'][0]['save_path'].$info['info'][0]['save_name'], $info['info'], $info['status']);
		}
/*	public function doSaveAttach(){
		$data['attach_type'] = t($_REQUEST['attach_type']);
		$data['upload_type'] = isset($_REQUEST['upload_type']) ? t($_REQUEST['upload_type']) : 'file';
		
		$thumb = intval($_REQUEST['thumb']);
		$whith = intval($_REQUEST['width']);
		$height = intval($_REQUEST['height']);
		$cut = intval($_REQUEST['cut']);
		
		$options['attach_type'] = $data['attach_type'];
		
		//关联别的表名的相关的信息
		$options['app_name'] = '';
		$options['table'] = '';
		$options['row_id'] = '';
		
		$info = D('Attach')->upload($data, $options);
		
		if($info['status']){
			$data = $info['info'];
			if($thumb == 1){
				$data['src'] = getImageUrl($data['save_path'].$data['save_name'],$whith,$height,$cut);
			}else{
				$data['src'] = ATTACH_URL.'/'.$data['save_path'].$data['save_name'];
			}
			
			$data['extension'] = strtolower($data['extension']);
		//	$return = array('status' => 1, 'data' => $data);
			$this->ajaxReturn('操作成功',$data,1);
		}else{
//			$return  = array('status' => 0, 'info' => $info['info']);
			$this->ajaxReturn('操作失败',$info['info'],0);
		}
	//	$this->ajaxReturn($return);
	}*/
	
	
	/**
	 * 获取登录用户的信息
	 * @return 登录用户的信息
	 */
	private function _getUserInfo() {
		return D('User')->getUserInfo($this->uid);//这个uid是在CommonAction类初始化的uid  ====================
	}
	
    /**
     * 注销账号
     * @return bool 操作是否成功
     */	
    public function delAccount(){
    }
}
?>