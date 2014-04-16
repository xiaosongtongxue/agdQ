<?php

//仿widget写，暂时不知道ajax如何获取widget返回的模板内容
class TestAction extends Action{
	public function TestPost(){
		$action = $_POST ['action'];
		$name = $_POST ['name']; // 取出POST过来的值
		if ($action == 'insert') {
			$filename = "C:/trys.txt";
			if (! file_exists ( $filename )) {
				
				// 如果文件不存在,则创建文件
				@fopen ( $filename, "w" );
				if (is_writable ( $filename )) {
					
					// 打开文件以添加方式即"w"方式打开文件流
					if (! $handle = fopen ( $filename, "w" )) {
						$returnValue = json_encode(array('id' =>'0','value'=>"文件不可打开"));
						echo $returnValue;
						exit ();
					}
					if (! fwrite ( $handle, $name )) {
						$returnValue = json_encode (array(array('id'=>'0','value'=>"文件不可写")));
						echo $returnValue;
						exit ();
					}
					
					// 关闭文件流
					fclose ( $handle );
				}
			}
			
			$returnValue = json_encode(array(
					array('id'=>'1','value'=>"正确"),
					array('id'=>'1','value'=>"尝试多参数代码")));
			echo $returnValue;
		
		} else {
			$returnValue = json_encode(array('id'=>'error'));
			echo $returnValue;
		}
	}
	
	public function bs3(){
		dump($_SESSION);
	}
	public function path(){
		echo 'SITE_PATH:'.SITE_PATH.'(注意，SITE_PATH的最后一个字符是/)<br />';
		echo 'SITE_DOMAIN:'.SITE_DOMAIN.'<br />';
		echo 'SITE_URL:'.SITE_URL.'<br />';
		echo 'UPLOAD_PATH:'.UPLOAD_PATH.'<br />';
		echo 'UPLOAD_URL:'.UPLOAD_URL.'<br />';
	}
	//import('ORG.Util.RBAC');
	public function aaa(){
		$a = 'rt34';//0
		$b = '34rt';//34
		$c = '';//0
		$aa = intval('rt34');
		$bb = intval($b);
		$cc = intval($c);
		echo $a.'||'.$aa.'<br>';
		echo $b.'||'.$bb.'<br>';
		echo $c.'||'.$cc.'<br>';
		exit();
	}
	public function ccc($p){
		$order = 'publish_time desc,feed_id desc';
		$User = M('Feed'); // 实例化User对象
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$list = $User->order($order)->page($p.',5')->select();
		$this->assign('list',$list);// 赋值数据集
		import('ORG.Util.Page');// 导入分页类
		$count      = $User->count();// 查询满足要求的总记录数
		$Page       = new Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
		
	}
	public function bbb(){
		$map['test'] = 4;//未知字段不会影响到查询
		$map['feed_id'] = 39;
		$arr = D('Feed')->where($map)->find();
		dump($arr);
		$arr = D('Feed')->test($map);
		dump($arr);
		exit();
		//	$this->display();
	}
	public function widget(){
		$this->template = 'think';
		$c = W('PageBar', array('first'=>1,'cur'=>6,'last'=>8));
		echo $c;
	}
	public function count(){
		$count = M('User')->where(array('uid'=>0))->count();
		echo $count;
	}
	public function nullTest(){
		$t = null;//false
		$tt = '';//false
		$ttt = '44';//true
		$tttt = false;//empty
		$ttttt = true;//noEmpty
		$a = 0;//false
		$aa = '0';//false
		if($a)
			echo '111';
		else
			echo '222';
		if($aa)
			echo '333';
		else
			echo '444';	
		exit();	
		
		if($ttt)
			echo '111';
		else
			echo '000';
		if(empty($tttt))
			echo 'empty';
		else 
			echo 'noEmpty';
	}
	public function arr(){
		$arr = 3333;//foreach 不会输出
		$arr = array(11,22,33);
		echo $arr;//输出array
		foreach ($arr as $a){
			echo $a;
		}
		exit();
		//
	}
	public function avatar(){
		$this->display();
		//$avatar = D('Avatar')->getUserAvatar();
		//dump($avatar);
	}
	
	public function map(){
		$map = "nickname in ('icubit') AND uid!=2 AND `is_active`=1";
		$ulist = D('User')->where($map)->field('uid')->select();
		dump($ulist);
	}
	
	public function ahut(){
		/*$str = "alert('密码错误！！');";//有此学号
		$str2 = "alert('用户名不存在或未按照要求参加教学活动！！');";//无此学号*/
		import("@.ORG.HttpClient");
		$client = new HttpClient('211.70.149.135:88');
		$client->post('/Default3.aspx', array(
    		'__VIEWSTATE'=>'dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n',
			'TextBox1' => '119074175',//学号
   			'TextBox2' => '149656',//密码
			'ddl_js'=>mb_convert_encoding('学生','gb2312','utf-8'),//注意传递中文字符时要转码成gb2312
			'Button1'=>'',
		));
		/*echo $client->getStatus();
		$headers = $client->getHeaders();
		$session_id = $headers['set-cookie'];
		$pageContent = HttpClient::quickGet($url);
		exit();*/
		/*$headers = $client->getHeaders();
		$client->setCookies($headers['set-cookie']);
		$cookie = $client->getCookies();
		dump($cookie);
		exit();*/
		//xsgrxx.aspx?xh=119074175&xm=张帅&gnmkdm=N121501
		//if (!$client->get('/xskbcx.aspx?xh=119074175')) {
		if (!$client->get('/xsgrxx.aspx?xh=119074175')) {
   			die('An error occurred: '.$client->getError());
		}
		$pageContents = $client->getContent();
		$temp = mb_convert_encoding($pageContents,'utf-8','gb2312');
		echo $temp;
		exit();
		import("@.ORG.simple_html_dom");
		/*$html = str_get_html("<div>foo <b>bar</b></div>"); 
		$e = $html->find("div", 0);

		echo $e->tag; // Returns: " div"
		echo $e->outertext; // Returns: " <div>foo <b>bar</b></div>"
		echo $e->innertext; // Returns: " foo <b>bar</b>"
		echo $e->plaintext; // Returns: " foo bar"
		exit();*/
		$html = str_get_html($temp);
		$ret  = $html->find('#Label5',0);
		//dump($ret);
		$stuId = $ret->plaintext;
		echo $temp;
		exit();
		/*$pageContents = HttpClient::quickPost('http://211.70.149.135:88/Default3.aspx', array(
    	'__VIEWSTATE'=>'dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n',
		'TextBox1' => '119074175',//学号
   		'TextBox2' => '149656',//密码
		'ddl_js'=>mb_convert_encoding('学生','gb2312','utf-8'),//注意传递中文字符时要转码成gb2312
		'Button1'=>'',
		
		));*/
		$cookie = $client->getCookies();
		dump($cookie);
		exit();
		$str = array(
			'valid'		=>"alert('密码错误！！');",//有此学号
			'noValid'	=>"alert('用户名不存在或未按照要求参加教学活动！！');",//无此学号
		);
		//echo mb_detect_encoding($pageContents);
		//echo $pageContents;
		$temp = mb_convert_encoding($pageContents,'utf-8','gb2312');
		$len = strlen($temp);
		$len = strlen($temp.'dadfafdasdfa');
		if(substr_count($temp,$str['valid']))
			//return true;
			echo '11';
		if(substr_count($temp,$str['noValid'])){
			//$this->_error = L('PUBLIC_STU_ID_EXIST_WRONG');		//该学号非本校学号或不存在
			//return false;
			echo '00';
		}
	}
	public function ahut2(){
		$str = "window.open('xs_main.aspx?xh=";
		import("@.ORG.HttpClient");
		$pageContents = HttpClient::quickPost('http://211.70.149.135:88/Default3.aspx', array(
				'__VIEWSTATE'=>'dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n',
				'TextBox1' => '119074175',//学号
				'TextBox2' => '149656',//密码
				'ddl_js'=>mb_convert_encoding('学生','gb2312','utf-8'),//注意传递中文字符时要转码成gb2312
				'Button1'=>'',
		
		));
		
		//$pageContents = $client->getContent();
		echo  $pageContents;
		exit();
	}
	public function ahut3(){
		$ahut = D('Ahut');
		if($ahut->getAccess('119074175','149656')){
			 $ahut->loadSchedule();
			echo '1111';
			
		}else{
			echo '0000';
		}
	}
	public function ahut4(){
		$ahut = D('Ahut');
		$t = $ahut->getSchedule('119074175');
		dump($t);
		
	}
	public function ahut5(){
		$ahut = D('Ahut');
		$t = $ahut->getSchedule('119074175');
		$tt = $ahut->analyseSchedule($t);
		dump($tt);
		/*$ttt = $ahut->analyseTime('周一第1,2节{第1-14周};周五第3,4节{第1-14周}');
		dump($ttt);*/
	}
	public function preg(){
		//preg_match ("/\d(,[0-9]{1,2})*/",$v,$matches);
		//preg_match('/^-[0-9]{3}-/','124-987-785',$matches);
		preg_match ("/\d(,[0-9]{1,2})*/", "周一第1,2节{第1-14周}",$matches);
		//dump($matches);
		echo $matches[0];	
	}
	public function displaySchedule(){
		$ahut = D('Ahut');
		$t = $ahut->getSchedule('119074175');
		$tt = $ahut->analyseSchedule($t);
		
		$this->assign('sbox',$tt);
		$this->display();
	}
}
//				dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n
//__VIEWSTATE	dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n	64	
//__VIEWSTATE	dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n	64
//				dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n	
?>