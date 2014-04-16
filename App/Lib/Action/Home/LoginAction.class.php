<?php
/**
 * LoginAction 登录模块
 * @author  xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class LoginAction extends Action{
	private $_user_model;  //用户模型字段
	private $_passport_model;  //登录模型字段
	public function test(){
		dump($_SESSION);
		dump(cookie());
		exit();
	}
	/**
	 * 模块初始化，获取用户模型对象、登录模型对象
	 * @return void
	 */
	protected function _initialize(){
		$this->_user_model = M('User');
		$this->_passport_model = D('Passport');
	}

	/**
	 * 登录页面
	 */
	public function login(){
		//检测是否记住登录-----------------
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			layout('Layout/layout_login');
			//TODO:首页背景图片
			/*$this->assign('SITE_URL',SITE_URL);*/
			$this->assign('title','安工大圈-连接你我他');
            $this->display();
        }else{
            $this->redirect('Index/index');
        }
	}

	/**
	 * 检查用户是否登录
	 */
    protected function checkUser() {
        if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            redirect(U('Login/login'));
        }
    }

	/**
	 * 处理登录提交的表单
	 */
	public function doLogin(){
		$user = t($_POST['identity']);  //登录账号--学号或邮箱登录
		$pwd = t($_POST['credential']);  //密码

		//$is_remember_me = false;
		$is_remember_me = t($_POST['rememberme']);    ////是否记住下次登录

		if($this->_passport_model->loginLocal($user,$pwd,$is_remember_me)){
			//redirect(U('Index/index')); //登录成功后转到首页
			$this->ajaxReturn('','',1);
		}else{
			//$this->error($this->_passport_model->getLastError());  // 账号输入错误
			$this->ajaxReturn('',$this->_passport_model->getLastError(),0);
		}
		//redirect(U('Login/login'));
	}
	
	/**
	 * 忘记密码处理页面
	 * @return void
	 */
	public function forgetPwd(){
		layout('Layout/layout_login');
		//TODO:首页背景图片
		//$this->assign('backgroudImages',D('Template')->getBackgroudImageName());
		
		//标题
		$this->assign('title',"找回密码");
		$this->display();
	}
	
	//验证学号及教务系统密码
	public function getAccess(){
		//
		unset($_SESSION['temp']);
		$stu_id = $_POST['stu_id'];
		$pwd = $_POST['pwd'];

		$return = array();
		//验证学号是否已被注册
		$exist = D('User')->where(array('stu_id'=>$stu_id))->find();
		if($exist){
			$return['status']=0;
			$return['info']=L('PUBLIC_STU_ID_REGISTER'); 		//该学号已注册
			exit(json_encode($return));
		}
		//未被注册，继续

		$ahut = D('Ahut');
		if($ahut->getAccess($stu_id,$pwd)){
			$return['status']=1;
			$return['info']='验证成功';
			//session保存临时学号和密码信息，以备后用
			$_SESSION['temp']['stu_id'] = $stu_id;
			//$_SESSION['temp']['md5_pwd'] = md5($pwd);
			$_SESSION['temp']['user_info'] = $ahut->getUserInfo();

		}else{
			$return['status']=0;
			$return['info']=$ahut->getLastError();
		}
		exit(json_encode($return));
	}

	/**
	 * ajax处理、验证注册表单提交的信息并保存到数据库
	 *
	 */
	public function doReg(){
		$return = array();
		if(empty($_SESSION['temp']['user_info'])){
			$return['status']=0;
			$return['info']='太长时间未操作，数据已过期';
			exit(json_encode($return));
		}

		$nickname = t($_POST['nickname']);
		$email = t($_POST['email']);
		$pwd = trim($_POST['password']);

		//验证邮箱是否已被使用
		if(!D('Register')->isValidEmail($email)){
			$return['status']=-1;
			$return['info']=D('Register')->getLastError();
			exit(json_encode($return));
		}

		//验证用户名是否唯一
		if(!D('Register')->isValidNickName($nickname)){
			$return['status']=-2;
			$return['info']=D('Register')->getLastError();
			exit(json_encode($return));
		}

		//默认个人用户类型
		$data['type'] = 'person';
		$data['param'] = $_SESSION['temp']['user_info']['stu_id'];
		$data['email'] = $email;
		$data['nickname'] = $nickname;
		$data['pwd'] = md5($pwd);
		$data['reg_ip'] = get_client_ip();
		$data['reg_time'] = date("Y-m-d H:i:s",time());

		$data['is_active'] = 0;
		$data['is_audit'] = 1;	//默认通过

		//如果包含中文将中文翻译成拼音
		if ( preg_match('/[\x7f-\xff]+/', $data['nickname'] ) ){
			//昵称和呢称拼音保存到搜索字段
			$data['search_key'] = $data['nickname'].' '.D('PinYin')->Pinyin( $data['nickname'] );
		} else {
			$data['search_key'] = $data['nickname'];
		}

		$result = D('User')->add($data);

		//验证用户是否注册成功
		if($result){
			//是否发送邮箱成功
			if(D('Register')->sendActivationEmail(array('uid'=>$result))){
				$return['status']=-4;
				$return['info']='用户导入信息失败';
				//导入用户基本信息
				$_SESSION['temp']['user_info']['uid'] = $result;
				$isSuccess = M('UserDetail')->add($_SESSION['temp']['user_info']);
				if($isSuccess){
					$return['status']=1;
					$return['info']='注册成功';
				}

			}else{
				$return['status']=-3;
				$return['info']=D('Register')->getLastError();

			}
		}else{
			$return['status']=-5;
			$return['info']='注册失败';
			//exit(json_encode($return));
		}
		exit(json_encode($return));
	}

	/**
	 * 注销本地登录
	 * @return void
	 */
	public function doLogout() {
		$this->checkUser();
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			if($_COOKIE[session_name()]){
				cookie('AGDQ_LOGGED_USER', NULL);	// 注销cookie
				setcookie(session_name(),'',time()-1,'/');
			}
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION);
            session_destroy();
			redirect(U('Login/login'));
        }else {
            $this->error('已经退出！');
        }
	}
}
?>