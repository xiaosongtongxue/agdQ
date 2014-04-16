<?php
/**
 * 注册模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com> 
 * @version agdQ1.0
 */
class RegisterModel extends Model {

	private $_user_model;				// 用户模型对象字段
	private $_user_check_model;				// 用户模型对象字段
	private $_error;					// 错误信息字段???icubit:Model已有$error成员变量，不需要重新定义
	private $_email_reg = '/[_a-zA-Z\d\-\.]+(@[_a-zA-Z\d\-\.]+\.[_a-zA-Z\d\-]+)+$/i';		// 邮箱正则规则
	
	/**
	 * 初始化操作，实例化用户模型对象 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->_user_model = M('User');
		$this->_user_check_model = M('User_check');
	}
	
	/**
	 * 验证邮箱内容的正确性
	 * @param string $email 输入邮箱的信息
	 * @return boolean 是否验证成功
	 */
	public function isValidEmail($email) {
		$res = preg_match($this->_email_reg, $email, $matches) !== 0;
		if(!$res) {
			$this->_error = L('PUBLIC_EMAIL_TIPS');			// 无效的Email地址
		}
		if($res && $this->_user_model->where('`email`="'.mysql_real_escape_string($email).'"')->find()) {
			$this->_error = L('PUBLIC_EMAIL_REGISTER');			// 该Email已被注册
			$res = false;
		}

		return (boolean)$res;
	}
	
	/**
	 * 验证邮箱是否存在
	 * @param string $email 输入邮箱的信息
	 * @return boolean 是否验证成功
	 */
	public function isValidEmailExist($email) {
		$res = preg_match($this->_email_reg, $email, $matches) !== 0;
		if(!$res) {
			$this->_error = L('PUBLIC_EMAIL_TIPS');			// 无效的Email地址
		}
		if($res && $this->_user_model->where('`email`="'.mysql_real_escape_string($email).'"')->find()) {
			return true;
		}else{
			$this->_error = L('PUBLIC_EMAIL_NOT_EXIST');			// 该Email在数据库中不存在
		}

		return false;
	}
	
	/**
	 * 验证昵称的唯一性
	 * @param string $nickname 输入昵称的信息
	 * @return boolean 是否验证成功
	 */
	public function isValidNickName($nickname){
		$res = true;
		// 管理员的昵称也是不能用的
		/*
		 +----------------------------------------------------------------
		 	TODO:验证昵称不包含管理员昵称的代码验证
		 +----------------------------------------------------------------
		 */
		
		if($res && $this->_user_model->where('`nickname`="'.$nickname.'"')->find()){
			$this->_error = L('PUBLIC_NICKNAME_EXIST');			//该昵称已存在
			$res = false;
		}
		
		return (boolean)$res;
	}
	
	/**
	 * 验证学号的存在性及激活性
	 * @param string $stu_id 输入学号的信息
	 * @return boolean 是否验证成功
	 */
	/*public function isValidStu_id($stu_id){
		$res = true;
		$exist = $this->_user_check_model->where('`stu_id`="'.$stu_id.'"')->find();
		$active = $this->_user_model->where('`stu_id`="'.$stu_id.'"')->find();
		if($res && $exist){
			if($active){
				$this->_error = L('PUBLIC_STU_ID_REGISTER'); 		//该学号已注册
				$res = false;
			}else{
				$res = true;
			}
		}else{
			$this->_error = L('PUBLIC_STU_ID_EXIST_WRONG');		//该学号非本校学号或不存在
			$res = false;
		}
		
		return (boolean)$res;
	}*/
	/**
	 * 验证学号的存在性及激活性,学号有效性检测通过教务系统反馈来确定
	 * @param string $stu_id 输入学号的信息
	 * @return boolean 是否验证成功
	 * @author icubit修改于2013-8-24
	 */
	public function isValidStu_id($stu_id){
		//检查数据合法性
		if(empty($stu_id)) {
			$this->_error = '学号不能为空';
			return false;
		}
		//过滤非数字字符,待完善
		//$stu_id = intval($stu_id);
		//判断是否已注册
		$exist = $this->_user_model->where('`stu_id`="'.$stu_id.'"')->find();
		if($exist){
			$this->_error = L('PUBLIC_STU_ID_REGISTER'); 		//该学号已注册
			return false;	
		}
		//如果未被注册,检查学号是否可用
		$str = array(
			'valid'		=>"alert('密码错误！！');",//有此学号
			'noValid'	=>"alert('用户名不存在或未按照要求参加教学活动！！');",//无此学号
		);
		import("@.ORG.HttpClient");
		$pageContents = HttpClient::quickPost('http://211.70.149.135:88/Default3.aspx', array(
    	'__VIEWSTATE'=>'dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n',
		'TextBox1' => $stu_id,//学号
   		'TextBox2' => '',//密码
		'ddl_js'=>mb_convert_encoding('学生','gb2312','utf-8'),//注意传递中文字符时要转码成gb2312
		'Button1'=>'',
		
		));
		$temp = mb_convert_encoding($pageContents,'utf-8','gb2312');
		if(substr_count($temp,$str['valid']))
			return true;
		if(substr_count($temp,$str['noValid'])){
			$this->_error = L('PUBLIC_STU_ID_EXIST_WRONG');		//该学号非本校学号或不存在
			return false;
		}
		$this->_error = '未知错误';		
		return false;
	}
	 
	 
	/**
	 * 给指定用户发送激活账户邮件
	 * @param integer $map 用户学号/uid
 	 * @param string $node 邮件模板类型
	 * @return boolean 是否发送成功
	 * icubit 2013-9-16 修改
	 */
	public function sendActivationEmail($map,$node ='register_active',$appname = 'public') {
		//$data['stu_id'] = $stu_id;
		$user_info = $this->_user_model->where($map)->find();

		//验证用户是否存在及存在就发送邮箱激活
		if(!$user_info) {
			$this->_error = L('PUBLI_USER_NOTEXSIT');			// 用户不存在
			return false;
		}else{
			$data['uid'] = $uid = $user_info['uid'];
			$data['node'] = $node;
			$data['email'] = $user_info['email']; // 收件人的Email
			$data['name'] = $user_info['nickname']; //收件人的姓名（用户名）
			$data['appname'] = $appname;  //应用名称
			$data['title'] = "用户激活邮件";// 主题
			$data['body'] = '<div style="text-align:center;height:18px;line-height:18px;color:#999;padding:6px 0;font-size:12px">"'.$user_info['nickname'].'",您好,欢迎加入安工大圈的一员,注册激活邮件,请点击<a href="http://'.$_SERVER['SERVER_NAME'].U('Register/activate?id='.$uid).'" style="text-decoration:none;color:#3366cc">"'.U('Register/activate').'"</a></div>';// 正文
			if($this->sendEmail($data)){
				$this->_error = L('PUBLIC_EMAIL_TO_SUCCESS');		// 发送邮箱成功 --> 系统已将一封激活邮件发送至您的邮箱，请立即查收邮件激活帐号
			}else{
				$this->_error = L('PUBLIC_EMAIL_TO_ERROR'); // 发送邮箱失败 --> 系统发送一封激活邮件失败
				return false;
				}
			return true;
		} 
	}
	
	/**
	 * 给指定用户发送获取密码账户邮件
	 * @param integer $email 用户邮箱
 	 * @param string $node 邮件模板类型
	 * @return boolean 是否发送成功
	 */
	public function sendForgetPwdEmail($email,$node ='forget_password',$appname = 'public') {
		$data['email'] = $email;
		$user_info = $this->_user_model->where($data)->find();

		//验证用户是否存在及存在就发送邮箱激活
		if(!$user_info) {
			$this->_error = L('PUBLI_USER_NOTEXSIT');			// 用户不存在
			return false;
		}else{
			//随机修改密码
			$randone = rand(100,999);
			$randtwo = rand(100,999);
			$string = "Lzhg";//自定义的---------
			$newPwd = $randone.$string.$randtwo;
			$save['pwd'] = md5($newPwd);
			if(!M('User')->where($data)->save($save)){
				$this->_error('修改密码失败,故不能发送邮箱,请重新输入邮箱');
				return false;
			}
			
			$data['uid'] = $uid = $user_info['uid'];
			$data['node'] = $node;
			$data['email'] = $user_info['email']; // 收件人的Email
			$data['name'] = $user_info['nickname']; //收件人的姓名（用户名）
			$data['appname'] = $appname;  //应用名称
			$data['title'] = "忘记的登录密码获取";// 主题
			$data['body'] = '<div style="text-align:center;height:18px;line-height:18px;color:#999;padding:6px 0;font-size:12px">"'.$user_info['nickname'].'",您好,你在安工大圈的新账号密码为：'.$newPwd.' 。请登录后修改密码,谢谢！</div>';// 正文
			if($this->sendEmail($data)){
				$this->_error = L('PUBLIC_EMAIL_TO_SUCCESS');		// 发送邮箱成功 --> 系统已将一封激活邮件发送至您的邮箱，请立即查收邮件激活帐号
			}else{
				$this->_error = L('PUBLIC_EMAIL_TO_ERROR'); // 发送邮箱失败 --> 系统发送一封激活邮件失败
				return false;
			}
			return true;
		} 
	}
	
	/**
	 * 发送邮件，添加到消息队列数据表中
	 * @param array $data 消息的相关数据
	 * @return mix 添加失败返回false，添加成功返回新数据的ID
	 */
	public function sendEmail($data) {
		if(empty($data['email'])) {
			// TODO:邮箱格式验证
			return false;
		} 
		// TODO:----------XXXXXXXXX(弃用-->用户隐私设置判断)
		$s['uid'] = intval($data['uid']);
		$s['node'] = $data['node'];
		$s['email'] = $data['email'];
		$s['name'] = $data['name'];
		$s['appname'] = $data['appname'];
		$s['is_send'] = 1;
		$s['sendtime'] = time();
		$s['title'] = $data['title'];
		$body = $data['body'];
		$s['body']= '<div style="width:540px;border:#0F8CA8 solid 2px;margin:0 auto"><div style="color:#bbb;background:#0f8ca8;padding:5px;overflow:hidden;zoom:1"><div style="float:right;height:15px;line-height:15px;padding:10px 0;display:none">2012年07月15日</div>
					<div style="float:left;overflow:hidden;position:relative"><a><img style="border:0 none" src="__PUBLIC__/Images/LOGO/yunfou.png"></a></div></div>
					<div style="background:#fff;padding:20px;min-height:300px;position:relative">		<div style="font-size:14px;">			
						            	<p style="padding:0 0 20px;margin:0;font-size:12px">'.$body.'</p>
						            </div></div><div style="background:#fff;">
			            <div style="text-align:center;height:18px;line-height:18px;color:#999;padding:6px 0;font-size:12px">此信为系统自动邮件,请不要直接回复。</a></div>
			            <div style="line-height:18px;text-align:center"><p style="color:#999;font-size:12px">&copy;2013 yunfou All Rights Reserved.</p></div>
			        </div></div>';
		$s['ctime'] = time();
		if(D('Mail')->send_email($s['email'],$s['name'],$s['title'],$s['body'])){
			return M('Notify_email')->add($s);
		}else{
			$this->_error = L('PUBLIC_EMAIL_TO_ERROR'); // 发送邮箱失败
			return false;
			}
	}
	
	/**
	 * 激活指定用户
	 * @param integer $uid 用户UID
	 * @return boolean 是否激活成功
	 */
	public function activate($uid) {
		$data['uid'] = $uid;
		$user_info = $this->_user_model->where($data)->find();

		if(!$user_info['is_active']) {
			$res = $this->_user_model->where($data)->save(array('is_active'=>1));
		}
		if($res) {
			$this->_error = L('PUBLIC_ACCOUNT_ACTIVATED_SUCCESSFULLY');		// 恭喜，帐号已成功激活
			return true;
		} else {
			$this->_error = L('PUBLIC_ACTIVATE_USER_FAIL');			// 激活用户失败
			return false;
		}
	}
	//
	public function register($data){
		
		//if(M('UserDetail')->add($data))
		
		//$data['email'] = $email;
		//$data['nickname'] = $nickname;
		//$data['stu_id'] = $stu_id;
		//$data['pwd'] = md5($pwd);
		//$data['sex'] = $sex;
		$data['reg_ip'] = get_client_ip();
		$data['reg_time'] = date("Y-m-d H:i:s",time());
		
		/*
		+---------------------------------------------------------------------------------------------------
			TODO:是否通过审核：0-未通过，1-已通过
		+---------------------------------------------------------------------------------------------------
		*/
		$data['is_audit'] = 1;	//默认通过
		
		//如果包含中文将中文翻译成拼音
		if ( preg_match('/[\x7f-\xff]+/', $data['nickname'] ) ){
			//昵称和呢称拼音保存到搜索字段
			$data['search_key'] = $data['nickname'].' '.D('PinYin')->Pinyin( $data['nickname'] );
		} else {
			$data['search_key'] = $data['nickname'];
		}
		
		//添加到数据库
/*		if($this->_user_reg_type){
			$result = $this->_user_model->where('`stu_id`="'.$data['stu_id'].'"')->save($data);
		}else{
			$result = $this->_user_model->add($data);
		}*/
		$result = $this->_user_model->add($data);
			
	}
	
	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
}
?>