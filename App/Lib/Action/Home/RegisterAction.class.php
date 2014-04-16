<?php
/**
 * RegisterAction 注册模块
 * @author  xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class RegisterAction extends Action{
	
	private $_register_model;			// 注册模型字段
	private $_user_model;				// 用户模型字段
	private $_user_reg_type = true;			// 用户注册类型-->true为用户必须用学号来注册，false为用户满足条件即可注册（无需学号）
	
	/**
	 * 模块初始化，获取用户模型对象、注册模型对象
	 * @return void
	 */
	protected function _initialize(){
		$this->_user_model = M('User');
		$this->_register_model = D('Register');
	}
	
	/**
	 * 注册页面
	 * @return void
	 */
	public function register(){
		layout('Layout/layout_login');
		
		//TODO:首页背景图片
		$this->assign('backgroudImages',D('Template')->getBackgroudImageName());
		//标题
		$this->assign('title','注册页面');
		$this->display();
	}
	
	/**
	 * 处理忘记密码页面提交的信息并保存到数据库
	 */
	public function doForgetPwd(){
		$email = t($_POST['email']);
		//验证邮箱是否存在
		if(!$this->_register_model->isValidEmailExist($email)){
			$this->error($this->_register_model->getLastError());
		}
		
		if($this->_register_model->sendForgetPwdEmail($email)){
			//redirect(U('Login/login'));
			$this->ajaxReturn('','密码已发送到邮箱',1);
		}else{
			//$this->error($this->_register_model->getLastError());
			//redirect(U('Register/forgetPwd'));
			$this->ajaxReturn('',$this->_register_model->getLastError(),0);
		}
	}
	
	/**
	 * 处理、验证注册表单提交的信息并保存到数据库
	 */
	public function doReg(){
		$nickname = t($_POST['username']);
		$stu_id = t($_POST['stuNumber']);
		//$sex = $_POST['sex'];
		$email = t($_POST['email']);
		$pwd = trim($_POST['password']);
		
		//验证邮箱是否已被使用
		if(!$this->_register_model->isValidEmail($email)){
			$this->error($this->_register_model->getLastError());
		}
		
		//验证用户名是否唯一
		if(!$this->_register_model->isValidNickName($nickname)){
			$this->error($this->_register_model->getLastError());
		}
		
		//验证学号是否已存在及激活
		if($this->_user_reg_type && !$this->_register_model->isValidStu_id($stu_id)){
			$this->error($this->_register_model->getLastError());
		}
		
		$data['email'] = $email;
		$data['nickname'] = $nickname;
		$data['stu_id'] = $stu_id;
		$data['pwd'] = md5($pwd);
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
		
		//验证用户是否注册成功
		if($result){
			//是否发送邮箱成功
			if($this->_register_model->sendActivationEmail($stu_id)){
				redirect(U('Register/waitActivation'));
			}else{
				$this->error($this->_register_model->getLastError());
			}
		}else{
			$this->error(L('PUBLIC_REGISTER_FAIL'));  		//注册失败
		}
		
	}
	
	public function doStep2(){
		$this->assign('title','上传头像')->display();
	}
	
	/**
	 * 激活指定用户
	 * @param integer $uid 用户UID
	 * @return void
	 */
	public function activate() {
		$uid = $_GET['id'];
		if($this->_register_model->activate($uid)){
			$this->redirect('Login/login');  //邮箱激活成功后跳转到登录页面
		}else{
			$this->error($this->_register_model->getLastError());
		}
	}
	
	/**
	 * 跳转页面（可用于激活邮箱跳转和其他需要跳转的页面）
	 **/
	public function jump(){
		$this->display();
	}
	
	/**
	 * 验证邮箱是否已被使用
	 */
	public function isEmailAvailable($email) {
		$result = $this->_register_model->isValidEmail($email);
		$this->ajaxReturn(null, $this->_register_model->getLastError(), $result);
	}
	/**
	 * 验证用户名是否唯一
	 */
	public function isNickNameAvailable($nickname) {
		$result = $this->_register_model->isValidNickName($nickname);
		$this->ajaxReturn(null,$this->_register_model->getLastError(),$result);
	}
	/**
	 * 验证学号是否已存在及激活
	 */
	public function isStu_idAvailable($stu_id) {
		$result = $this->_register_model->isValidStu_id($stu_id);
		$this->ajaxReturn(null,$this->_register_model->getLastError(),$result);
	}
	
	/**
     * 操作激活跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    public function activate_success($message,$jumpUrl='',$ajax=false) {
        $this->activateJump($message,1,$jumpUrl,$ajax);
    }
	
	/**
     * 操作激活跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function activate_error($message,$jumpUrl='',$ajax=false) {
        $this->activateJump($message,0,$jumpUrl,$ajax);
    }
	
	/**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     * @param string $message 提示信息
     * @param Boolean $status 状态
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @access private
     * @return void
     */
    private function activateJump($message,$status=1,$jumpUrl='') {
        if(is_int($ajax)) $this->assign('waitSecond',$ajax);
        if(!empty($jumpUrl)) $this->assign('jumpUrl',$jumpUrl);
        // 提示标题
        $this->assign('msgTitle',$status? L('_OPERATION_ACTIVATE_SUCCESS_') : L('_OPERATION_ACTIVATE_FAIL_'));
        $this->assign('status',$status);   // 状态
        //保证输出不受静态缓存影响
        C('HTML_CACHE_ON',false);
        if($status) { //发送成功信息
            $this->assign('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','3');
            // 默认操作成功自动返回操作前页面
            if(!isset($this->jumpUrl)) 
				$this->assign("jumpUrl",$jumpUrl);
            $this->display(U('Register/jump'));
		}else{
            $this->assign('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','3');
            // 默认发生错误的话自动返回上页
            if(!isset($this->jumpUrl)) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(U('Register/jump'));
            // 中止执行  避免出错后继续执行
            exit ;
        }
    }
}
?>