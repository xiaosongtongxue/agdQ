<?php
/**
 * 此类用于测试使用
 **/

	class PublicTplAction extends Action{	
		public function jsTest(){
			
			$this->display();
		}
		
		public function jsPostTest(){
			$array = array('data'=>'这里是什么啊', 'info'=>'成功了啊', 'status'=>1);
			echo json_encode($array);
		}
		public function test(){
			echo '<br />';
			echo '<br />';
			echo '<br />';
			echo SITE_URL;
			$this->display();
		}
		public function doBeautifyPic(){
			//die ( $_FILES['Filedata'] );
			if (!$_FILES['Filedata']) {
				die ( 'Image data not detected!' );
			}
			if ($_FILES['Filedata']['error'] > 0) {
				switch ($_FILES ['Filedata'] ['error']) {
					case 1 :
						$error_log = 'The file is bigger than this PHP installation allows';
						break;
					case 2 :
						$error_log = 'The file is bigger than this form allows';
						break;
					case 3 :
						$error_log = 'Only part of the file was uploaded';
						break;
					case 4 :
						$error_log = 'No file was uploaded';
						break;
					default :
						break;
				}
				die ( 'upload error:' . $error_log );
			} else {
				$img_data = $_FILES['Filedata']['tmp_name'];
				$size = getimagesize($img_data);
				$file_type = $size['mime'];
				if (!in_array($file_type, array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'))) {
					$error_log = 'only allow jpg,png,gif';
					die ( 'upload error:' . $error_log );
				}
				switch($file_type) {
					case 'image/jpg' :
					case 'image/jpeg' :
					case 'image/pjpeg' :
						$extension = 'jpg';
						break;
					case 'image/png' :
						$extension = 'png';
						break;
					case 'image/gif' :
						$extension = 'gif';
						break;
				}	
			}
			if (!is_file($img_data)) {
				die ( 'Image upload error!' );
			}
			//图片保存路径,默认保存在该代码所在目录(可根据实际需求修改保存路径)
			$save_path = dirname( __FILE__ );
			$uinqid = uniqid();
			$filename = $save_path . '/' . $uinqid . '.' . $extension;
			$result = move_uploaded_file( $img_data, $filename );
			if ( ! $result || ! is_file( $filename ) ) {
				die ( 'Image upload error!' );
			}
			//echo 'Image data save successed,file:' . $filename;
			echo $uinqid . '.' . $extension;
			exit ();
		}
		public function tp(){
		//	static_cache('xiaoluo','xxxxxccccc');
			/*$test = static_cache('testcache');
			echo $test.'-----';*/
			/*$uid = $_GET['uid'] ? $_GET['uid'] : 0;
			$pwd = $_GET['pwd'] ? $_GET['pwd'] : 0;
			$this->assign('uid',$uid)->assign('pwd',$pwd);*/
			$this->display();
		}
		
		public function tpl(){
			dump($ActInfo = D('Activity')->getLoginUserActivityInfo());
			
			//dump(D('UserCircle')->getCircleAdminInfo(12));
			
			//dump(D('UserCircleLink')->getUserInfoByCircleId(12));
			
			//dump($circle_info = D('UserCircle')->getUserCircle(12));
			//dump(D('UserCircle')->getUserCircleByType(1));
			
			/*echo '<br /><br />';
			$str = 123;
			$true = ereg("^[0-9]+$",$str);
			dump($true);
			dump($data = D('Attach')->getAttachById(10));*/
			
			/*$a = 1;
			dump(D('UserCircle')->getUserCircleByType($a));
			dump(D('Cache')->get('AllUserCircleType'.$a));
			*/
			//dump(D('Cache')->get('createCircle'));
			
			//dump(D('Cache')->get('ee'));
			
			/*//$arr = array(
//				"attach_max_size"=>2,
//				"attach_allow_extension"=>"gif,png,jpeg,bmp,jpg"
//			);
//			dump(D('Xdsata')->get('admin_Config:image'));*/
	 		//dump(D('Cache')->get('ee'));
			
			//$this->assign('url', '__APP__/PublicTpl/uploadAttach');
			
			
			
			
			
			
			
			
			
			
			/*$var['actInfo'] = D('Activity')->getActivityById(2);
			dump($var['actInfo']);
			dump($var['actInfo']['poster']);
			$var['posterInfos'] = D('Attach')->getAttachByIds($var['actInfo']['poster']);
			dump($var);*/
			
			
	/*		$getAid = D('Cache')->get('activityPosterIdPre'.$_SESSION[C('USER_AUTH_KEY')]);
			//unset($getAid[0]);
			
			dump($getAid);
			
			$where['attach_id'] = is_array($getAid) ? array('IN', $getAid) : $getAid;
//			$where['row_id'] ='null';
			//dump(M('Attach')->where($where)->select());
			$attach_id = getSubByKey(M('Attach')->where($where)->select(), 'attach_id');
			$attach_id[] = '20';
			dump($attach_id);
			
			dump(D('Attach')->doEditAttach($getAid, 'deleteAttach'));*/
			
			
			//$ayy = array_diff($attach_id, $getAid);
			//dump($ayy);
			
			/*$activityId = 12;
			$ActivityTags = '小罗1111，小尹1111';
			!is_array($ActivityTags) && $ActivityTags = explode(',',preg_replace('/[，,]+/u', ',', $ActivityTags));
			D('TagApp')->setAppName('public')->setTable('activity')->setAppTags($activityId, $ActivityTags);*/
			
			/*$add['user_circle_name']   = "校圈123";
			$add['uid']                = $_SESSION[C('USER_AUTH_KEY')];
			$add['user_circle_intro']  = "将啊哈哈大家速度哈师大";
			$add['user_circle_icon']   = 'circle.jpg' ;
			$add['user_circle_type']   = 0;
			$add['app_name']           = 'public' ;
			$add['is_authenticate']    = 0;
			$a = array(6,7,8);
			dump(D('UserCircle')->getUserCircleIcon(6));
*/			
			/*$update['user_circle_id'] = 6;
			$update['user_circle_name'] = "xiaoluo";
			
			$update['user_circle_icon'] = 'dasa';
			$update['user_circle_type'] = 2;
			$update['is_authenticate'] = 1;
			dump($r = D('UserCircle')->updateUserCircle($update));
			if(!$r){
				echo "hahah";
			}*/
			
			
			/*$str = array();
			if(!isset($str)){
				echo 'KONG<br />';
			}else{
				echo 'BUSHI';
			}
*/	/*		dump(D('TagApp')->setAppName('public')->setTable('user')->deleteSourceTag(5));
			dump(D('TagApp')->setAppName('user')->setTable('user')->getHotTags());
			dump(D('TagApp')->getAppTagList());
			$arrs = array(
					'小啊啊啊啊啊啊啊啊',
					'阿达发送给收到货后到风景哥',
					'到供电公司帝国时代',
				);
			$tagAppss = D('TagApp');
			dump($tagAppss->setAppName('xiaoluo')->setTable('luozi')->addAppTags(4,$arrs));
			dump($tagAppss->getLastError());
			dump(M('TagApp')->where(array('app_name'=>'public', 'table' => 'user', 'row_id' => 3))->delete());
			dump(D('TagApp')->setAppName('publicTpl')->setTable('user')->setAppTags(2, array('小妹', '哈sada哈哈', 'whoarsdaeyou')));
			$where['tag_id'] = array('IN', array(1,2));
			dump(M('TagApp')->where($where)->select());
			dump(D('Tag')->getTagNames(array(1,2)));
			dump(D('TagApp')->setTable('user')->getAppTags(array(1,2)));
			$ids = array(2);
			$tagApp = D('TagApp');
			$tagApp->setTable('user');
			dump($tagApp->getAppTags($ids));
			
			dump(D('Tag')->getTagList());
			$tags = array(
				'小罗',
				'$xiaoluo',
				'$hahaah'
				);
			dump(D('Tag')->addTags($tags));
			dump($tags);
			dump(array_filter(array('小罗', 'xiaoluo', '2' => array('asd', '1231'))));
			//dump(D('Tag')->getTagList());
			//dump(D('Tag')->_parseOptions(array('xiaoluo' => 'da哈哈')));
			dump(D('Tag')->where(array('name'=>'小罗'))->find());
			dump(D('Tag')->getTagId('小ssdass罗'));
			
			$str = '<b>1</b>2314dd小小罗啊哈哈哈';
			$str = t($str);
			echo $str.'<br />';
			preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);
			dump($match);
			echo getShort($str, 12);*/
			/*echo $_SESSION[C('USER_AUTH_KEY')].'===';
			echo $JS['UID'].'hahaha';*/
			/*$toUid = array(2,3);
			dump(D('User')->getUserInfoByUids($toUid));
			dump(D('User')->getUserList());*/
		/*		$arr = array(
					  0 => 
						array(
						  'id' => '1',
						  'node' => 'register_active'
						  ),
					1 => 
						array(
						  'id' => '2',
						  'node' => 'active'
						  )
				);
				foreach($arr as $s => $v){
					dump($v);
				}*/
			
			//dump(D('Notify')->getNodeList());
			/*if(!D('Notify')->setRead(2,'public')){
				echo D('Notify')->getUnreadCount(3);
				dump(D('Notify')->getMessageDetail('public', 2));
			}*/
			//$num = array(0,1,2,3);
			/*D('Cache')->set('xiaoluo0','hahha',60);
			D('Cache')->set('xiaoluo1','hahha',60);
			D('Cache')->set('xiaoluo2','hahha',30);
			D('Cache')->set('xiaoluo3','hahha',30);*/
			//dump(D('Cache')->getList('xiaoluo', $num));
		//	echo D('Cache')->get('xiaoluo');
			//echo 'acas'.static_cache('xiaoluo');		
			
			/*
			$mail = array(
					'email_sendtype' => 'smtp',
					'email_host' => 'smtp.exmail.qq.com',
					'email_ssl' => '0',
					'email_port' => '25',
					'email_account' => '',
					'email_password' => '',
					'email_sender_name' => 'ThinkSNS',
					'email_sender_email' => '',
					'email_test' => '',
				);
				
			$_mail_list = array(
					'email_sendtype'		=> 'smtp',        			    //一般为smtp
					'email_host'			=> 'smtp.qq.com',       	    // sets GMAIL as the SMTP server(SMTP服务器)
					'email_port'			=> '25',             			// set the SMTP port(SMTP服务器端口)
					'email_ssl'				=> 'ssl',          				// sets the prefix to the servier  tls,ssl 一般为ssl
					'email_account'			=> 'xiaoluo@angongda.com',       // SMTP username(SMTP服务器的用户邮箱)
					'email_password'		=> 'yf2013115',    			    // SMTP password(SMTP服务器的用户密码)
					'email_sender_name'		=> '云否团队',                   //发件人姓名
					'email_sender_email'	=> 'xiaoluo@angongda.com',       //发件人Email地址-->用于发送给用户的邮箱
					'email_reply_account'	=> 'xiaoluo@angongda.com'        //发件人Email地址-->用于用户回复的邮箱
				);
			
			$site = array(
						'site_closed'              => '0',//0为不关闭站点,1为关闭站点
						'site_name'                => '安工大圈',
						'site_slogan'              => '为广大大学生提供优质服务的社交化网站',
						'site_closed_reason'       => '本站正在升级中...请敬请期待啊...',
						'sys_domain'               => 'admin',
						'sys_nickname'             => '管理员,超级管理员',
						'sys_email'                => 'xiaoluo@angongda.com',
					);*/
			/*$cache = '127.0.0.1:11211';
			D('Xdata')->put('cache_Config:cachesetting',$cache);
			$xdata = D('Xdata')->get('cache_Config:cachesetting');
			echo $xdata;*/
			//$xdata = D('Xdata')->get('cache_Config:cachesetting');
			//$xdata = D('Xdata')->get('admin_Config:mail');
			/*echo '<br />';
			echo '<br />';
			echo $xdata;
			echo C('DB_PREFIX');
			dump(D('Avatar')->getUserAvatar());
			echo D('AVATAR')->getSaveAvatarPath();
			echo D('AVATAR')->getUploadAvatarPath();
			$ok = unserialize(N);
			dump($ok);
			
			dump($xdata);
			
			dump(D('Attach')->getAttachByIds(array(23,24,25)));
			dump(D('Attach')->getAttachById(23));
			
			
			
			
			dump(pathinfo(IMAGE_PATH.'/yf_pic.png'));
			
			
/*			if(file_exists(IMAGE_PATH.'/yf_pic.png')){
				unlink(IMAGE_PATH.'/yf_pic.png');
				echo "file_exists";
			}else{
				echo "NO_file_exists";
			}
			
			echo IMAGE_URL.'<br />';
			echo IMAGE_PATH.'<br />';
			
			//echo date('Y/md/H/');
			
			echo IS_HTTPS.'<br />';
			echo SCRIPT_NAME.'<br />';
			echo $_SERVER["SCRIPT_NAME"].'<br />';
			echo IS_CGI.'<br />';
			echo PHP_SAPI.'<br />';
			echo _PHP_FILE_.'<br />';
			echo $_SERVER["PHP_SELF"];
			dump($_SERVER["PHP_SELF"]);
			
			//echo SITE_URL;
			//echo SITE_PATH;
			$this->assign('path', IMAGE_PATH);
			$this->assign('url', IMAGE_URL);
			$array = array(
				'0' => array(
					'xiaoluo' => 'luo0',
					'xiaomei' => 'mei0',
					),
				'1' => array(
					'xiaoluo' => 'luo1',
					'xiaomei' => 'mei1',
					),
				'2' => array(
					'xiaoluo' => 'luo2',
					'xiaomei' => 'mei2',
					),
				);
			
			$con = array(
				'xiaoluo' => 'sa'
				);
			$arr = getSubByKey($array,'xiaoluo');
			dump($arr);
			
			
			
			//echo date('Y/md/H/');
			
		/*	$activityInfo = D('Activity')->getActivityById(1);
			dump($activityInfo);*/
			
/*			$arr = array(
				0 => array(
					'id' => '123',
					 0   => array(
						'row_id' => '654',
						),
					),
				1 => array(
					'id' => '456',
					 0   => array(
						'row_id' => '789',
						),
					),
				);
			$this->assign('list',$arr);*/
			
			
		/*	$filename = 'Public/Images/yf_pic.png';
			$info      = pathinfo($filename);
			if(file_exists($filename)){
				echo "ok";
			}else{
				echo "no";
			}
			dump($info);
			
			$file = __ROOT__.'/dasd/dsa/asf/sdf';
			$imageUrl = __ROOT__.'/'.ltrim(str_replace(__ROOT__, '', $file),'/');
			echo $imageUrl;
			$arr = array(
				'0'=>array(
					'1'=>array(
						'2'=>array(
							'3'=>array(
								'ok'=>'da',
								'oo'=>'ad',
								),
							),
						),
					),
				);
			dump($arr);
			echo __ROOT__;*/
	/*		
			$posterInfo = array();
		$posterInfo = $info;
		foreach($info as $num => $value){
			// 获取海报
			$poster = explode(',',preg_replace('/[，,]+/u', ',', $info[$num]['poster']));
			$posterInfo[$num] = D('Attach')->getAttachByIds($poster);
		}*/
			
/*			$arr = '15,16，17';
			$attach = D('Attach');
			$attachInfo = $attach->getAttachByIds($arr);
			dump($attachInfo);*/
			
			/*$activity = D('Activity');
			$activity->setUid('2');
			$infos = $activity->getUserActivityInfo();
			//$info = $activity->getActivityById(2);
			$info = M('Activity')->where(array('uid'=>'2'))->select();
			dump($infos);
			dump($info);*/
			
			/*$str = '12,23,34，45,56,67，7';
			$arr = explode(',',preg_replace('/[，,]+/u', ',', $str));
			dump($arr);*/
			
			//dump(M('Activity')->select());
			
			/*$url = '/Public/Uploads/Attach/2013/0808/14/';
			$this->_createFolder($url);*/
			/*if(mkdir('/agdQ/Public/Uploads/Attach/2013/0808/14/',0777,true)){
				$this->error('OK');
			}else{
				$this->error('NO');
			}*/
			//echo intval($_SESSION[C('USER_AUTH_KEY')]);//D('Avatar')->_uid;
			//echo D('Avatar')->getAvatarPath();
/*			echo SITE_URL;
			echo '<br />';
			echo __ROOT__;
			echo '<br />';
			echo $_SERVER['HTTP_HOST'];
			echo '<br />';
			dump(D('User')->getUserInfo(2));
			$jiami = jiami(2);
			echo $jiami;
			echo jiemi($jiami);*/
			
	/*		$array = array(
				0 => array(
					'a' => 'A',
					'b' => 'B',
					),
				1 => array(
					'c' => 'C',
					'd' => 'D',
					),
				2 => array(
					'e' => 'E',
					'f' => 'F',
					),
//				3 => array(
					'g' => 'G',
					'h' => 'H',
			);
			
			foreach($array as $a => $b){
				dump($a);
				dump($b);
			}
			
			$arr = getSubByKey($array);
			dump($arr);*/
			
/*			$data['upload_type'] = 'image';
			$options = array(
				'app_name' => 'app',
				'table' => 'user',
				'row_id' => '2',
				'attach_type' => 'attach_type',
				'uid' => 6,
				'ctime' => time(),
				'private' =>  0,
				'is_del' => 0,
				'from' => 0 , //暂时默认0为网页版，可以将0换成一个检测设备的方法
				'allow_types' => array('image/jpg','image/png'),
			);
			$attach = D('Attach');
			dump($attach->upload($data,$options));*/
			
			
			/*$filename = "xiaoluo/xiaoxiaoluo//haha";
			$filename = str_replace('//','/','/'.trim($filename));
			echo $filename;*/
			
			//$xdata = D('Xdata');
			
			/*$cloud = D('CloudImage');
			dump($cloud->getConfig());*/
			
			/*$system_default = 'jpg,gif,png,jpeg,bmp,zip,rar,doc,xls,ppt,docx,xlsx,pptx,pdf';
			$system_default = explode(',',$system_default);
			dump($system_default);
			
			$clouds = D('CloudAttach');
			dump($clouds->getUploadFileInfo());
			
			dump($clouds->getConfig());
			
			
			$cloud = D('Xdata');
			dump($cloud->get('admin_Config:attach'));
			
			$str = 'a:7:{s:17:"cloud_attach_open";s:1:"0";s:20:"cloud_attach_api_url";s:23:"http://v0.api.upyun.com";s:19:"cloud_attach_bucket";s:0:"";s:25:"cloud_attach_form_api_key";s:0:"";s:24:"cloud_attach_prefix_urls";s:0:"";s:18:"cloud_attach_admin";s:0:"";s:21:"cloud_attach_password";s:0:"";}';
			$s = unserialize($str);
			dump($s);*/
			/*
			$listName = "xiaoluo";
			$listData = array(
						'public' => 'pppuuu',
						'weiba' => 'wwwbbb',
						);
			
			if($xdata->lput($listName, $listData)){
				echo "ok";
				dump($xdata->lget($listName));
			}else{
				echo "no";
			}
			$key = 'haha';
			$value = 'hhhaaa';
			$xdata->put($key,$value);
			$xdata->get($key);
			*/
			
			
		/*	$keys = 'admin_Config:cloudimage';
			$values = array(
					'cloud_image_open' => '0',
					'cloud_image_api_url' => 'http://v0.api.upyun.com',
					'cloud_image_bucket' => '',
					'cloud_image_form_api_key' => '',
					'cloud_image_prefix_urls' => '',
					'cloud_image_admin' => '',
					'cloud_image_password' => ''
					);
					
			$xdata->put($keys,$values);
			dump($xdata->get($keys));
			*/
			
			/*$filename  = './Public/Uploads/Avatar/11.png';
		    $info      = pathinfo($filename);
			
			$info2 = getThumbImage($filename, 100, 100,true,true);
			
			dump($info);
			dump($info2);*/
			
			//echo UPLOAD_PATH;
			
			/*	$arr = array(
							'Peter'=> array(
									'Country'=>"0",
									'Age'=>20
									),
							'Li Ming'=> array(
									'Country'=>'CHINA',
									'Age'=>21
									)
							);
				$serialize_var = serialize($arr);
				echo $serialize_var;*/
				
				/*$serialize_var = 'a:7:{s:16:"cloud_image_open";s:1:"0";s:19:"cloud_image_api_url";s:23:"http://v0.api.upyun.com";s:18:"cloud_image_bucket";s:0:"";s:24:"cloud_image_form_api_key";s:0:"";s:23:"cloud_image_prefix_urls";s:0:"";s:17:"cloud_image_admin";s:0:"";s:20:"cloud_image_password";s:0:"";}';
				$unserialize_var = unserialize($serialize_var);
				dump($unserialize_var);
				$array = array(
							'cloud_image_open' => '0',
							'cloud_image_api_url' => 'http://v0.api.upyun.com',
							'cloud_image_bucket' => '',
							'cloud_image_form_api_key' => '',
							'cloud_image_prefix_urls' => '',
							'cloud_image_admin' => '',
							'cloud_image_password' => ''
							);
				dump($array);
				$serialize_var = serialize($array);
				echo $serialize_var;
				if(intval($array['cloud_image_open']) == 0)
					echo "ok";
				else
					echo "no";
*/				

			
			
			/*$method = "xiaoluo";
			$uri = "yunfou";
			$date = "2013-12-22";
			$length = 22;
			$sign = "{$method}&{$uri}&{$date}&{$length}";
			echo 'UpYun '.$this->username.':'.md5($sign);
			*/
			/*
			//引入图片处理库
			 import('ORG.Util.Image.ThinkImage'); 
			 //使用GD库来处理1.gif图片
			$img = new ThinkImage(THINKIMAGE_GD, './1.gif'); 
			 //将图片裁剪为440x440并保存为corp.gif
			$img->crop(220, 220)->save('./crop.gif');
			 //给裁剪后的图片添加图片水印，位置为右下角，保存为water.gif
			$img->water('./11.png', THINKIMAGE_WATER_SOUTHEAST)->save("water.gif");
			 //给原图添加水印并保存为water_o.gif（需要重新打开原图）
			$img->open('./1.gif')->water('./11.png', THINKIMAGE_WATER_SOUTHEAST)->save("water_o.gif");*/
			
			
			/*$uid = '119074155';
			$md5 = md5($uid);
			echo $md5."<br />";
			$sc = '/'.substr($md5, 0, 2).'/'.substr($md5, 2, 2).'/'.substr($md5, 4, 2);
			echo $sc."<br />";*/
			
			//$this->assign('name','xiaoluozhi');
			
			/*static_cache('testcache','xiaoluo');
			$test = static_cache('testcache');
			echo $test;*/
			
			/*$a = U('PublicTpl/tp',array('uid'=>'6','pwd'=>'sdfsdfs'));
			$this->assign('a',$a);
			redirect(U('PublicTpl/tp',array('uid'=>'6','pwd'=>'sdfsdfs')));*/
			//$user = M('User')->select();//->select();//
			/*$user = D('User')->getUserData(array('uid'=>'13'));
			dump($user);*/
			/*$test = M('User')->page(1,5)->select();
			dump($test);*/
			/*$user = array(
					'0' => '1',
					'1' => '2',
					'2' => '3',
					'3' => '5',
					'6' => '7',
					);*/
			/*dump($user);
			$user = serialize( $user );
			dump($user);
			dump(unserialize($user));
			$test = $this->getSubByKey($user,'nickname');
			dump($test);*/
			//echo $test;
			/*$array = array(
					'xiaoluo' => 'xiaoluozhi',
					'xiaomei' => 'xiaomeizhi',
					);
			$a = serialize($array);
			echo $a;
			unset($array);
			$a = unserialize($a);
			dump($a);*/
			//exit;
			
			
			//echo D('')->tablePrefix;
			//echo C('DB_PREFIX');
	/*	$matchuids = array(
					'0' => '11',
					'1' => '13',
					'2' => '11',
					'3' => '13' ,
					'4' => '11',
					'5' => '13' ,
					);	
		$suid = array();
		foreach($matchuids as $v => $k){
			!in_array($v, $suid) && $suid[] = (int)$v;
			echo $v."------";
			echo $k."<br />";
		}
		dump($suid);
		exit;*/
			
		/*	$user = M('User')->select();
			$user = $this->getSubByKey($user,'uid');
			dump($user);
			exit;*/
			/*$test = '/@(.+?)([\s|:]|$)/is';
			$content = 'you @are ok @as fds!';
			
			preg_match_all($test, $content, $matches);
			
			echo $matches;
			dump($matches);
			*/
/*			$data = array(
					'1' => 'aaaaaaa',
					'2' => 'xiaoluo',
					'3' => 'xiaoluozhi',
					'4' => 'xiaomeizhi',
					'5' => 'yaoying',
					'6' => '哈哈哈',
					'7' => '小罗',
					);
			
			S('Data',$data);
			$Data = S('Data');
			dump(S('Data'));
			
			foreach($Data as $k=>$v){
				echo $Data[$k]."====>".$v."<br />";
			}*/
			
			
		/*	echo jiami(fdsfs);
			echo jiemi(jiami(fdsfs));
			echo jiemi('9I-H6O8h0');*/
			//dump($config['LANG_LIST']);
			/*echo C('LANG_LIST');
			echo L('PUBLIC_EMAIL_REGISTER');
			exit;*/
			//echo PHP_FILE;
			//echo $_SERVER['REQUEST_TIME'];
			/*//随机修改密码
			$data['email'] = '584188065@qq.com';
			$randone = rand(100,999);
			$randtwo = rand(100,999);
			$string = "Lzhg";
			$newPwd = $randone.$string.$randtwo;
			$save['pwd'] = md5($newPwd);
			M('User')->where($data)->save($save);
			echo $newPwd;
			exit;
			//$this->assign('test',$_SERVER['SERVER_NAME']);
			
			$test = new RegisterAction();
			$test->activate_success('激活',U('Login/login'));
			$this->display('Register/jump');*/
			
			
			
			
			/*$config['activeurl'] = U('Register/activate', array('uid'=>$uid));
			$user_info['nickname'] = "小罗";
			$uid = 11;
			$body = '<div style="text-align:center;height:18px;line-height:18px;color:#999;padding:6px 0;font-size:12px">"'.$user_info['nickname'].'",您好,欢迎加入安工大圈的一员,注册激活邮件,请点击<a href="http://'.$_SERVER['SERVER_NAME'].U('Register/activate?id='.$uid).'" style="text-decoration:none;color:#3366cc">"'.U('Register/activate?id='.$uid).'"</a></div>';
			
			
			$s['body']= '<div style="width:540px;border:#0F8CA8 solid 2px;margin:0 auto"><div style="color:#bbb;background:#0f8ca8;padding:5px;overflow:hidden;zoom:1"><div style="float:right;height:15px;line-height:15px;padding:10px 0;display:none">2012年07月15日</div>
					<div style="float:left;overflow:hidden;position:relative"><a><img style="border:0 none" src="__PUBLIC__/Images/LOGO/yunfou.png"></a></div></div>
					<div style="background:#fff;padding:20px;min-height:300px;position:relative">		<div style="font-size:14px;">			
						            	<p style="padding:0 0 20px;margin:0;font-size:12px">'.$body.'</p>
						            </div></div><div style="background:#fff;">
			            <div style="text-align:center;height:18px;line-height:18px;color:#999;padding:6px 0;font-size:12px">若不想再收到此类邮件，请点击<a href="'.U('public/Account/notify').'" style="text-decoration:none;color:#3366cc">设置</a></div>
			            <div style="line-height:18px;text-align:center"><p style="color:#999;font-size:12px">&copy;2013 xiaoluo All Rights Reserved.</p></div>
			        </div></div>';
			D('Mail')->send_email('584188065@qq.com','小罗','test',$s['body']);
			//D('Mail')->mailto('小罗纸','584188065@qq.com','4','aaaaasssss',$s['body']);
			$this->assign('test',$s['body']);*/
			$this->display();
		}
	/*	public function localUpload(){
			import('ORG.Net.UploadFile');
			// 实例化上传类
			$upload = new UploadFile();
			// 设置附件上传大小
			$upload->maxSize  = 5 * 1024 * 1024 ;
			// 设置附件上传类型,允许上传的文件后缀（留空为不限制），使用数组设置，默认为空数组
			$upload->allowExts  = array('png');
			//允许上传的文件类型（留空为不限制），使用数组设置，默认为空数组 
			$upload->allowTypes = array('image/png'); 
			// 设置上传路径
			$upload->savePath = ATTACH_PATH.'/xiaoluo/';
			// 启用子目录
			$upload->autoSub = false;
			// 默认文件名规则
			$upload->saveRule = uniqid;
			// 是否缩略图
			$upload->thumb = false;
	
			// 创建目录
			mkdir($upload->savePath, 0777, true);
			
			// 执行上传操作
			if(!$upload->upload()) {
				echo "no";
			} else {
				$uploadList = $upload->getUploadFileInfo();
				dump($uploadList);
			}
		}*/
		
		//上传头像
/*		public function upload(){
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();						// 实例化上传类
			$upload->maxSize = 2*1024*1024;					//设置上传图片的大小
			$upload->allowExts = array('jpg','png','gif');	//设置上传图片的后缀
			$upload->uploadReplace = true;					//同名则替换
//			$upload->saveRule = '';					//设置上传头像命名规则
			//完整的头像路径
			$path = D('Avatar')->init('26')->getAvatarPath();
			$upload->savePath = $path;
			if(!$upload->upload()) {						// 上传错误提示错误信息
				$this->ajaxReturn('',$upload->getErrorMsg(),0,'json');
			}else{											// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				$temp_size = getimagesize($path.'/temp.jpg');
				if($temp_size[0] < 100 || $temp_size[1] < 100){//判断宽和高是否符合头像要求
					$this->ajaxReturn(0,'图片宽或高不得小于100px！',0,'json');
				}
				$this->ajaxReturn($path.'/temp.jpg',$info,1,'json');
			}
		}
		*/
		/*private function _createFolder($path){
			if(!is_dir($path)){
				$this->_createFolder(dirname($path));
				mkdir($path, 0777);
			}
		}
		
		public function mkdirs($dir){
			if(!is_dir($dir)){
				if(!mkdirs(dirname($dir))){
					return false;
				}
				if(!mkdir($dir,0777)){
					return false;
				}
			}
			return true;
		}  */
		
		/*public function uploadAttach(){
			$data['upload_type'] == 'image';
			
	 		//========$input_options['custom_path'] = date($system_default['attach_path_rule']); //定义的上传目录规则
			$input_options['max_size'] = floatval(2) * 1024 * 1024;//单位为：兆
			//$input_options['allow_exts'] = $system_default['attach_allow_extension'];
			$input_options['allow_types'] = array();//允许上传的文件类型（留空为不限制），使用数组设置，默认为空数组 
			//========$input_options['save_path'] = ATTACH_PATH.'/'.$default_options['custom_path'];
			//========$input_options['save_name'] =	''; //指定保存的附件名.默认系统自动生成
			//========$input_options['save_to_db'] = true;
			$input_options['app_name'] = 'aaaa';//'app_name'    => t($input_options['app_name']),
			$input_options['table'] = 'ssssss';//'table'       => t($input_options['table']),
			//========$input_options['row_id'] = '';//'row_id'      => t($input_options['row_id']),
			$input_options['attach_type'] = 'dddddd';//'attach_type' => t($input_options['attach_type']),
			//========$input_options['uid'] = '';//'uid'         => (int) $input_options['uid'] ? $input_options['uid'] : $_SESSION[C('USER_AUTH_KEY')],
			//========$input_options['private'] = '';//'private'     => $input_options['private'] > 0 ? 1 : 0,
			//========$input_options['from'] = '';//'from'
			
		    $info = D('Attach')->upload($data, $input_options);
			D('Cache')->set('ee', $info);
			$this->ajaxReturn(ATTACH_URL.'/'.$info['info'][0]['save_path'].$info['info'][0]['save_name'], $info['info'], $info['status']);
		}*/
}
?>