<?php
/**
 * 邮件模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com> 
 * @version agdQ1.0
 */
class MailModel {

	// 允许发送邮件的类型
	public static $allowed = array('Register','unAudit','resetPass','resetPassOk','invateOpen','invate','atme','comment','reply');
	public $message;
	
	//邮箱相关信息自定义
/*	protected $_mail_list = array(
		'email_sendtype'		=> 'smtp',        			    //一般为smtp
		'email_host'			=> 'smtp.qq.com',       	    // sets GMAIL as the SMTP server(SMTP服务器)
		'email_port'			=> '25',             			// set the SMTP port(SMTP服务器端口)
		'email_ssl'				=> 'ssl',          				// sets the prefix to the servier  tls,ssl 一般为ssl
		'email_account'			=> 'xiaoluo@angongda.com',       // SMTP username(SMTP服务器的用户邮箱)
		'email_password'		=> 'yf2013115',    			    // SMTP password(SMTP服务器的用户密码)
		'email_sender_name'		=> '云否团队',                   //发件人姓名
		'email_sender_email'	=> 'xiaoluo@angongda.com',       //发件人Email地址-->用于发送给用户的邮箱
		'email_reply_account'	=> 'xiaoluo@angongda.com'        //发件人Email地址-->用于用户回复的邮箱
	);*/
	protected $_mail_list = null;

	/**
	 * 初始化方法，加载phpmailer，初始化默认参数
	 * @return void
	 */
	public function __construct() {
		import('ORG.phpmailer.phpmailer','','.php');
		import('ORG.phpmailer.pop3','','.php');
		import('ORG.phpmailer.smtp','','.php');
		set_time_limit(0);
		$this->_mail_list = D('Xdata')->get('admin_Config:mail');
		$this->option = array(
			'email_sendtype'		=> $this->_mail_list['email_sendtype'],     //一般为smtp
			'email_host'			=> $this->_mail_list['email_host'],         // sets GMAIL as the SMTP server
			'email_port'			=> $this->_mail_list['email_port'],         // set the SMTP port
			'email_ssl'				=> $this->_mail_list['email_ssl'],          // sets the prefix to the servier  tls,ssl 一般为ssl
			'email_account'			=> $this->_mail_list['email_account'],      // SMTP username
			'email_password'		=> $this->_mail_list['email_password'],     // SMTP password
			'email_sender_name'		=> $this->_mail_list['email_sender_name'],  //发件人姓名
			'email_sender_email'	=> $this->_mail_list['email_sender_email'], //发件人Email地址
			'email_reply_account'	=> $this->_mail_list['email_sender_email']  //发件人Email地址
		);
		/*dump($this->option);
		exit;*/
	}

	/**
	 * 发送邮件
	 * @param string $sendto_email 收件人的Email
	 * @param string $name 收件人的姓名（或用户名）
	 * @param string $subject 主题
	 * @param string $body 正文
	 * @param array $senderInfo 发件人信息 array('email_sender_name'=>'发件人姓名', 'email_account'=>'发件人Email地址')
	 * @return boolean 是否发送邮件成功
	 */
	public function send_email( $sendto_email, $name, $subject, $body, $senderInfo = '') {
		$mail= new PHPMailer();
		
		if(empty($senderInfo)) {
			$sender_name = $this->option['email_sender_name'];
			$sender_email = empty($this->option['email_sender_email']) ? $this->option['email_account'] : $this->option['email_sender_email'];
		} else {
			$sender_name = $senderInfo['email_sender_name'];
			$sender_email = $senderInfo['email_sender_email'];
		}
		
		$mail->IsSMTP();
		$mail->Host = $this->option['email_host'];            // SMTP 服务器  
		$mail->SMTPAuth = true;                  // 打开SMTP 认证  
		$mail->Username = $this->option['email_account'];   // 用户名
		$mail->Password = $this->option['email_password'];          // 密码  		
	
		$mail->SetFrom($sender_email,$sender_name);
		$mail->AddReplyTo($sender_email,$sender_name);
		
		$mail->AddAddress($sendto_email, $name);
		
		$mail->Subject = "=?UTF-8?B?".base64_encode($subject)."?=";  // 邮件主题
		
		$mail->AltBody = "查看信息,请使用一个HTML兼容的电子邮件查看器！";
		$mail->MsgHTML($body); // 邮件内容
		
		$mail->IsHTML(true);  //是否是HTML邮件 
		
	   //$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
		
		$result = $mail->Send();  //发送邮箱
		
		if(!$result) {
			$this->setMessage($mail->ErrorInfo);
			echo "Mailer Error: " . $mail->ErrorInfo;
		}
		else{
			$this->setMessage($mail->ErrorInfo);
			return $result;
		}
	}

	public function setMessage ($message) {
		$this->message = $message;
	}
}