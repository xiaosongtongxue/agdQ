<?php
/*
 * 教务系统抓包逻辑业务类
 * @author icubit<icubit@qq.com>
 */
class AhutModel {
	//教务系统主机
	private $_host = '211.70.149.135:88';
	private $_client = null;
	private $_type = array('学生','部门','教师','访客');
	private $_stuId = '';
	//private $_pwd = '';
	private $_uid = '';
	private $_error = '';
	private $_classes = array();
	/**
	 * 初始化方法，设置默认用户信息，初始化用户统计数据模型
	 * @return void
	 */
	public function __construct() {
		import("@.ORG.simple_html_dom");
		import("@.ORG.HttpClient");
		$this->_uid = intval($_SESSION[C('USER_AUTH_KEY')]);
		$this->_client = new HttpClient($this->_host);
	}
	/**
	 * 验证学号的存在性及激活性,学号有效性检测通过教务系统反馈来确定
	 * @param string $stu_id 输入学号的信息
	 * @return boolean 是否验证成功
	 * @author icubit修改于2013-8-24
	 */
	public function isValidStuId($stu_id){
		//检查数据合法性
		if(empty($stu_id)) {
			$this->_error = '学号不能为空';
			return false;
		}
		//过滤非数字字符,待完善
		//$stu_id = intval($stu_id);
		//判断是否已注册
		$exist = $this->D('User')->where('`stu_id`="'.$stu_id.'"')->find();
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
	
	public function getAccess($stu_id,$pwd/*,$type='学生'*/){
		/*if(!in_array($type,$this->_type)){
			$this->_error = '类型不对';
			return false;
		}*/
		
		$str = array(
			'valid'		=>"alert('密码错误！！');",//有此学号
			'noValid'	=>"alert('用户名不存在或未按照要求参加教学活动！！');",//无此学号
			'isLogin'	=>"window.open('xs_main.aspx?xh="//登入成功
		);
		
		//import("@.ORG.HttpClient");
		//$client = new HttpClient('211.70.149.135:88');
		$temp = $this->_client->quickPost('http://211.70.149.135:88/Default3.aspx', array(
				'__VIEWSTATE'=>'dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n',
				'TextBox1' => $stu_id,//学号
				'TextBox2' => $pwd,//密码
				'ddl_js'=>mb_convert_encoding('学生','gb2312','utf-8'),//注意传递中文字符时要转码成gb2312
				'Button1'=>'',
		));
		$pageContents = mb_convert_encoding($temp,'utf-8','gb2312');
		
		if(substr_count($pageContents,$str['isLogin'])){
			//学号密码正确,正式登入
			$this->_stuId = $stu_id;
			 $this->_client->post('/Default3.aspx', array(
					'__VIEWSTATE'=>'dDwtMTM2MTgxNTk4OTs7PnL1r+7LZbCnX4r1psASp5IYgc/n',
					'TextBox1' => $stu_id,//学号
					'TextBox2' => $pwd,//密码
					'ddl_js'=>mb_convert_encoding('学生','gb2312','utf-8'),//注意传递中文字符时要转码成gb2312
					'Button1'=>'',
			)); 
			
			return true;
		}
		
		
		if(substr_count($pageContents,$str['valid'])){
			$this->_error = '密码错误';
			return false;
		}
		if(substr_count($pageContents,$str['noValid'])){
			$this->_error = L('PUBLIC_STU_ID_EXIST_WRONG');		//该学号非本校学号或不存在
			return false;
		}
		$this->_error = '未知错误';		
		return false;
	}
	/*
	<table id="Table1" class="blacktab" bordercolor="Black" border="0" width="100%">
	<tbody><tr>
		<td colspan="2" rowspan="1" width="2%">时间</td><td align="Center" width="14%">星期一</td><td align="Center" width="14%">星期二</td><td align="Center" width="14%">星期三</td><td align="Center" width="14%">星期四</td><td align="Center" width="14%">星期五</td><td class="noprint" align="Center" width="14%">星期六</td><td class="noprint" align="Center" width="14%">星期日</td>
	</tr><tr>
		<td colspan="2">早晨</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td rowspan="4" width="1%">上午</td><td width="1%">第1节</td><td align="Center" rowspan="2" width="7%">操作系统A<br>周一第1,2节{第1-14周}<br>郭玉华<br>东教一阶204</td><td align="Center" rowspan="2" width="7%">汇编语言<br>周二第1,2节{第1-14周}<br>纪平<br>东教一阶104</td><td align="Center" rowspan="2" width="7%">计算机通信网络技术<br>周三第1,2节{第1-16周}<br>袁志祥<br>东教一阶203</td><td align="Center" width="7%">&nbsp;</td><td align="Center" width="7%">&nbsp;</td><td class="noprint" align="Center" width="7%">&nbsp;</td><td class="noprint" align="Center" width="7%">&nbsp;</td>
	</tr><tr>
		<td>第2节</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td>第3节</td><td align="Center" rowspan="2">计算机通信网络技术<br>周一第3,4节{第1-16周}<br>袁志祥<br>东教一阶203</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center" rowspan="2">汇编语言<br>周四第3,4节{第1-14周}<br>纪平<br>东教一阶104</td><td align="Center" rowspan="2">操作系统A<br>周五第3,4节{第1-14周}<br>郭玉华<br>东教一阶204</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td>第4节</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td rowspan="4" width="1%">下午</td><td>第5节</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center" rowspan="2">数据库概论A<br>周日第5,6节{第1-16周}<br>戴小平<br>东教一南206</td>
	</tr><tr>
		<td>第6节</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td>第7节</td><td align="Center">&nbsp;</td><td align="Center" rowspan="2">JAVA程序设计<br>周二第7,8节{第1-18周}<br>胡宏智<br>东教一南102</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center" rowspan="2">数据库概论A<br>周日第7,8节{第1-16周}<br>戴小平<br>东教一南206</td>
	</tr><tr>
		<td>第8节</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td rowspan="4" width="1%">晚上</td><td>第9节</td><td align="Center" rowspan="3">.NET平台和C#开发<br>周一第9,10,11节{第1-18周}<br>王广正<br>东教一南104</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center" rowspan="3">多媒体技术<br>周五第9,10,11节{第4-15周}<br>许精明<br>东教一北303</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td>第10节</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td>第11节</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr><tr>
		<td>第12节</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td><td class="noprint" align="Center">&nbsp;</td>
	</tr>
</tbody></table>
	*/
	/* 
	<table class="datelist " cellspacing="0" cellpadding="3" border="0" id="DBGrid" width="98%">
	<tbody>
	<tr class="datelisthead">
	<td>选课课号</td><td>课程名称</td><td>课程性质</td><td>是否选课</td><td>教师姓名</td><td>学分</td><td>周学时</td><td>上课时间</td><td>上课地点</td><td>教材</td><td>重修标记</td><td>授课计划上传次数</td><td>授课计划最近上传时间</td><td>授课计划上传文件名</td><td>授课计划下载</td>
	</tr><tr>
	<td>(2013-2014-1)-31001202-3150-1</td><td><a href="#">百部名片观赏(二)</a></td><td>&nbsp;</td><td>是</td><td><a href="#">谢黎旭</a></td><td>1.0</td><td>3.0-0.0</td><td>周五第9,10,11节{第4-11周}</td><td>东教D107</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="">下载</a></td>
	</tr><tr class="alt">
	<td>(2013-2014-1)-07464003-0306-1</td><td><a href="#">计算机通信网络技术</a></td><td>必修课</td><td>否</td><td><a href="#">袁志祥</a></td><td>4</td><td>4.0-2.0</td><td title="周一第3,4节{第1-16周};周三第1,2节{第1-16周}">周一第3,4节{第1-16周};周三第1</td><td>东教一阶203;东教一阶203</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="">下载</a></td>
	</tr><tr>
	<td>(2013-2014-1)-07454004-0334-1</td><td><a href="#">数据库概论A</a></td><td>必修课</td><td>否</td><td><a href="#" onclick="window.open('jsxx.aspx?jszgh=0334&amp;xkkh=(2013-2014-1)-07454004-0334-1&amp;xh=119074175','jsxx','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600,left=120,top=60')">戴小平</a></td><td>4</td><td>4.0-2.0</td><td title="周日第5,6节{第1-16周};周日第7,8节{第1-16周}">周日第5,6节{第1-16周};周日第7</td><td>东教一南206;东教一南206</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="javascript:__doPostBack('DBGrid$_ctl4$_ctl0','')">下载</a></td>
	</tr><tr class="alt">
	<td>(2013-2014-1)-07454006-2824-1</td><td><a href="#">操作系统A</a></td><td>必修课</td><td>否</td><td><a href="#" onclick="window.open('jsxx.aspx?jszgh=2824&amp;xkkh=(2013-2014-1)-07454006-2824-1&amp;xh=119074175','jsxx','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600,left=120,top=60')">郭玉华</a></td><td>3.5</td><td>4.0-2.0</td><td title="周一第1,2节{第1-14周};周五第3,4节{第1-14周}">周一第1,2节{第1-14周};周五第3</td><td>东教一阶204;东教一阶204</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="javascript:__doPostBack('DBGrid$_ctl5$_ctl0','')">下载</a></td>
	</tr><tr>
	<td>(2013-2014-1)-07454015-0307-1</td><td><a href="#">JAVA程序设计</a></td><td>必修课</td><td>否</td><td><a href="#" onclick="window.open('jsxx.aspx?jszgh=0307&amp;xkkh=(2013-2014-1)-07454015-0307-1&amp;xh=119074175','jsxx','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600,left=120,top=60')">胡宏智</a></td><td>3.5</td><td>2.0-0.0</td><td>周二第7,8节{第1-18周}</td><td>东教一南102</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="javascript:__doPostBack('DBGrid$_ctl6$_ctl0','')">下载</a></td>
	</tr><tr class="alt">
	<td>(2013-2014-1)-07454802-0334-1</td><td><a href="#">数据库概论课程设计</a></td><td>必修课</td><td>否</td><td><a href="#" onclick="window.open('jsxx.aspx?jszgh=0334&amp;xkkh=(2013-2014-1)-07454802-0334-1&amp;xh=119074175','jsxx','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600,left=120,top=60')">戴小平</a></td><td>1</td><td>+1</td><td>&nbsp;</td><td>&nbsp;</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="javascript:__doPostBack('DBGrid$_ctl7$_ctl0','')">下载</a></td>
	</tr><tr>
	<td>(2013-2014-1)-07444004-2239-1</td><td><a href="#">汇编语言</a></td><td>必修课</td><td>否</td><td><a href="#" onclick="window.open('jsxx.aspx?jszgh=2239&amp;xkkh=(2013-2014-1)-07444004-2239-1&amp;xh=119074175','jsxx','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600,left=120,top=60')">纪平</a></td><td>3.5</td><td>4.0-2.0</td><td title="周二第1,2节{第1-14周};周四第3,4节{第1-14周}">周二第1,2节{第1-14周};周四第3</td><td>东教一阶104;东教一阶104</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="javascript:__doPostBack('DBGrid$_ctl8$_ctl0','')">下载</a></td>
	</tr><tr class="alt">
	<td>(2013-2014-1)-07454925-0324-1</td><td><a href="#">多媒体技术</a></td><td>选修课</td><td>否</td><td><a href="#" onclick="window.open('jsxx.aspx?jszgh=0324&amp;xkkh=(2013-2014-1)-07454925-0324-1&amp;xh=119074175','jsxx','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600,left=120,top=60')">许精明</a></td><td>2</td><td>3.0-0.0</td><td>周五第9,10,11节{第4-15周}</td><td>东教一北303</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="javascript:__doPostBack('DBGrid$_ctl9$_ctl0','')">下载</a></td>
	</tr><tr>
	<td>(2013-2014-1)-07454711-2841-1</td><td><a href="#" onclick="window.open('kcxx.aspx?kcdm=07454711&amp;xh=119074175','kcxx','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=490,height=500,left=200,top=50')">.NET平台和C#开发</a></td><td>选修课</td><td>否</td><td><a href="#" onclick="window.open('jsxx.aspx?jszgh=2841&amp;xkkh=(2013-2014-1)-07454711-2841-1&amp;xh=119074175','jsxx','toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600,left=120,top=60')">王广正</a></td><td>3.5</td><td>3.0-0.0</td><td>周一第9,10,11节{第1-18周}</td><td>东教一南104</td><td>1</td><td></td><td>&nbsp;</td><td>&nbsp;</td><td>未上传</td><td><a href="javascript:__doPostBack('DBGrid$_ctl10$_ctl0','')">下载</a></td>
	</tr>
	</tbody></table>
	 */
	//导入课程表
	
	public function loadSchedule(){
		if(empty($this->_stuId)){
			$this->_error = '没有权限';
			return false;
		}
		if (!$this->_client->get('/xsxkqk.aspx?xh='.$this->_stuId)) {
			$this->_error = $this->_client->getError();
			return false;
		}
		$temp= $this->_client->getContent();
		$pageContents = mb_convert_encoding($temp,'utf-8','gb2312');
		//对页面进行解析
		$html = str_get_html($pageContents);
		//学生课表
		$class = array();
		$ids = array();
		$table = $html->find('table',0);
		$classes = $table->find('tr');
		foreach($table->find('tr') as $k=>$tr)
		{
			if($k == 0) continue;	
			$tds = $tr->find('td');
			$class['class_id'] = $tds[0]->innertext;//课程ID
			$class['name'] = $tds[1]->find('a',0)->innertext;//课程名称
			$class['teacher'] = $tds[4]->find('a',0)->innertext;//教师
			if(isset($tds[7]->title)) 
				$class['time'] = $tds[7]->title;//时间
			else
				$class['time'] = $tds[7]->innertext;//时间
			$class['location'] = $tds[8]->innertext;//地点
			if($class['time'] == '&nbsp;' || $class['location'] == '&nbsp;'){
				$class['time'] = $class['location'] ='';
				$class['display'] = 0;
			}else
				$class['display'] = 1;
			
			$isExist = M('Class')->where(array('class_id'=>$class['class_id']))->find();
			if(!empty($isExist))
				$id = $isExist['class_id'];
			else{
				$id = M('Class')->add($class);
			}
			$ids[] = $id;
		}
		$schedule = implode('-',$ids);
		M('UserDetail')->where(array('stu_id'=>$this->_stuId))->setField('schedule',$schedule);
		return true;
	}
	/*得到课程表信息
	 * @param $map 可以通过设置$map['uid']或者$map['stu_id']来得到
	 * @return array 返回课程记录
	 */
	public function getSchedule($map/* $stu_id = '' */){
		if(empty($map)){
			if(empty($_SESSION[C(USER_AUTH_KEY)]))
				return false;
			$map['uid'] = $_SESSION[C(USER_AUTH_KEY)]; 
		}
		
		$schedule = M('UserDetail')->where($map)->getField('schedule');
		$arr = explode('-',$schedule);
		$m['id']  = array('in',$arr);
		//$map['display'] = array('eq',1);
		$records = M('Class')->where($m)->select();
		return $records;
	}
	//分析课表返回课表矩形数组，用作页面显示
	public function analyseSchedule($schedule){
		//7*11数组
		$sBox = array(
		
		'1'=>array('1'=>'0','2'=>'0','3'=>'0','4'=>'0','5'=>'0','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0',),//星期一
		'2'=>array('1'=>'0','2'=>'0','3'=>'0','4'=>'0','5'=>'0','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0',),//星期二
		'3'=>array('1'=>'0','2'=>'0','3'=>'0','4'=>'0','5'=>'0','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0',),//星期三
		'4'=>array('1'=>'0','2'=>'0','3'=>'0','4'=>'0','5'=>'0','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0',),//星期四
		'5'=>array('1'=>'0','2'=>'0','3'=>'0','4'=>'0','5'=>'0','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0',),//星期五
		'6'=>array('1'=>'0','2'=>'0','3'=>'0','4'=>'0','5'=>'0','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0',),//星期六
		'7'=>array('1'=>'0','2'=>'0','3'=>'0','4'=>'0','5'=>'0','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0',),//星期日
		);
		foreach($schedule as $v){
			if($v['display'] == 0)
				continue;
			
			$time = $this->analyseTime($v['time']);	
			$location = $this->analyseLocation($v['location']);
			foreach($time as $k=>$t){
				$sBox[$t['weekday']][$t['start']] = array(
				'name'=>$v['name'],
				'location'=>$location[$k],
				'teacher'=>$v['teacher'],
				'rowspan'=>$t['length'],
				'id'=>$v['id'],
				);
				for($i=$t['start']+1;$i<$t['start']+$t['length'];$i++){
					$sBox[$t['weekday']][$i] = 1;	
				}
			}
		}
		return $this->transportMatrix($sBox);
	}
	
	public function updateSchedule($map){
		if(isset($map['stu_id']) || isset($map['uid'])){
			$schedule = $this->getSchedule();
			$temp = $this->analyseSchedule($schedule);
			$schedule_data = serialize($temp);
			M('Use')->where($map)->setField('format_data');
		}
	}
	
	//转置矩阵,将7*11转换成11*7
	private function transportMatrix($matrix){
		$return = array();
		foreach ($matrix as $i=>$m){
			foreach ($m as $j=>$v){
				$return[$j][$i] = $v;
			}
		}
		return $return;
	}
	//分析课表上课时间，格式：周一第1,2节{第1-14周};周五第3,4节{第1-14周}
	private function analyseTime($time){
		
		$return = array();
		$arr = explode(';',$time);
		foreach($arr as $v){
			$weekday = $this->getWeekday($v);
			preg_match ("/\d(,[0-9]{1,2})*/",$v,$matches);
			$temp = explode(',',$matches[0]);
			$start = $temp[0];
			$length = count($temp);
			$return[] = array('weekday'=>$weekday,'start'=>$start,'length'=>$length); 
		}
		return $return;
	}
	//分析课表上课地点,格式：东教一阶203;东教一阶203
	private function analyseLocation($time){
		$arr = explode(';',$time);
		return $arr;
	}
	private function getWeekday($str){
		$w = array(
				'1'=>'周一',
				'2'=>'周二',
				'3'=>'周三',
				'4'=>'周四',
				'5'=>'周五',
				'6'=>'周六',
				'7'=>'周日',
		);
		foreach($w as $k=>$v){
			if(substr_count($str,$v)){
				return $k;
			}
				
		}
	}
	/*
	<table class="formlist" width="100%" align="center">
	<tbody><tr>
		<td class="trbg1"><span id="lbxsgrxx_xh">学号：</span></td>
		<td><span id="xh">119074175</span></td>
		<td class="trbg1"><span id="lbxsgrxx_xszh">学生证号：</span></td>
		<td colspan="2"><span id="lbl_xszh"></span></td>
		<td class="trbg1"><span id="lbxsgrxx_sjlx">手机类型：</span></td>
		<td width="165"><span id="lbl_TELLX"></span></td>
		<td rowspan="6"><img id="xszp" src="readimagexs.aspx?xh=119074175" alt="照片" align="AbsMiddle" border="0" height="144" width="112"><br>
			<span id="Label5"><b><font color="Red">建议文件大小：9-18K，分辨率：96*128，文件类型：jpg、jpeg</font></b></span></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_xm">姓名：</span></td>
		<td><span id="xm">张帅</span></td>
		<td class="trbg1"><span id="lbxsgrxx_pyfx">培养方向：</span></td>
		<td colspan="2"><span id="lbl_pyfx">080902</span></td>
		<td class="trbg1"><span id="lbxsgrxx_sjhm">手机号码：</span></td>
		<td width="165"><span id="lbl_TELNUMBER">18355559961</span></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_cym">曾用名：</span></td>
		<td>
			<p></p>
			<p><span id="lbl_zym"></span></p>
		</td>
		<td class="trbg1"><span id="lbxsgrxx_zyfx">专业方向：</span></td>
		<td colspan="2"><span id="lbl_zyfx"></span></td>
		<td class="trbg1"><span id="lbxsgrxx_jtyb">家庭邮编：</span></td>
		<td width="165"><input name="jtyb" type="text" value="246542" id="jtyb"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_xb">性别：</span></td>
		<td><span id="lbl_xb">男</span></td>
		<td class="trbg1"><span id="lbxsgrxx_rxrq">入学日期：</span></td>
		<td colspan="2"><span id="lbl_rxrq">20110901</span></td>
		<td class="trbg1"><span id="lbxsgrxx_jtdh">家庭电话：</span></td>
		<td width="165"><input name="jtdh" type="text" value="05567595565" id="jtdh"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_csrq">出生日期：</span></td>
		<td><span id="lbl_csrq">19930304</span></td>
		<td class="trbg1"><span id="lbxsgrxx_byzx">毕业中学：</span></td>
		<td colspan="2"><span id="lbl_byzx"></span></td>
		<td class="trbg1"><span id="lbxsgrxx_fqxm">父亲姓名：</span></td>
		<td width="165"><input name="fqxm" type="text" value="张松涛" id="fqxm"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_mz">民族：</span></td>
		<td><span id="lbl_mz">汉族</span></td>
		<td class="trbg1"><span id="lbxsgrxx_ssh">宿舍号：</span></td>
		<td colspan="2"><span id="lbl_ssh"></span></td>
		<td class="trbg1"><span id="lbxsgrxx_fqdw">父亲单位：</span></td>
		<td width="165"><input name="fqdw" type="text" id="fqdw"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_jg">籍贯：</span></td>
		<td><span style="BORDER-RIGHT: red 0px solid; BORDER-TOP: red 0px solid; BORDER-LEFT: red 0px solid; WIDTH: 18px; BORDER-BOTTOM: red 0px solid">
			</span><span id="lbl_jg">安徽</span></td>
		<td class="trbg1"><span id="lbxsgrxx_dzyx">电子邮箱：</span></td>
		<td colspan="2"><span id="lbl_dzyxdz">819089692@qq.com</span></td>
		<td class="trbg1"><span id="lbxsgrxx_fqdwyb">父亲单位邮编：</span></td>
		<td width="165"><input name="fqdwyb" type="text" value="246542" id="fqdwyb"></td>
		<td><input name="File1" id="File1" type="file" class="text_nor" onclick="return check();" size="8"></td>
	</tr>
	<tr>
		<td class="trbg1" height="7"><span id="lbxsgrxx_zzmm">政治面貌：</span></td>
		<td height="7"><span id="lbl_zzmm">共青团员</span></td>
		<td class="trbg1" height="7"><span id="lbxsgrxx_lxdh">联系电话：</span></td>
		<td colspan="2" height="7"><span id="lbl_lxdh">18355559961</span></td>
		<td class="trbg1" height="7"><span id="lbxsgrxx_mqxm">母亲姓名：</span></td>
		<td width="165" height="7"><input name="mqxm" type="text" id="mqxm"></td>
		<td align="center" height="7"><input type="submit" name="Button3" value="上传照片" id="Button3" disabled="disabled" class="button"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_lydq">来源地区：</span></td>
		<td><span id="lbl_lydq">安徽</span></td>
		<td class="trbg1"><span id="lbxsgrxx_yzbm">邮政编码：</span></td>
		<td colspan="2"><span id="lbl_yzbm"></span></td>
		<td class="trbg1"><span id="lbxsgrxx_mqdw">母亲单位：</span></td>
		<td width="165"><input name="mqdw" type="text" id="mqdw"></td>
		<td align="center"><input type="submit" name="Button4" value="清除照片" id="Button4" disabled="disabled" class="button"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_lys">来源省：</span></td>
		<td><span id="lbl_lys">安徽</span></td>
		<td class="trbg1"><span id="lbxsgrxx_zkzh">准考证号：</span></td>
		<td colspan="2"><span id="lbl_zkzh"></span></td>
		<td class="trbg1"><span id="lbxsgrxx_mqdwyb">母亲单位邮编：</span></td>
		<td width="165"><input name="mqdwyb" type="text" id="mqdwyb"></td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_csd">出生地：</span></td>
		<td><span id="lbl_csd"></span></td>
		<td class="trbg1"><span id="lbxsgrxx_sfzh">身份证号：</span></td>
		<td colspan="2"><span id="lbl_sfzh">340826199303040832</span></td>
		<td class="trbg1" width="241" colspan="2"><span id="lbxsgrxx_fqdwdhhsj">父亲单位电话或手机：</span></td>
		<td><input name="fqdwdh" type="text" value="05567595565" id="fqdwdh"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_jkzk">健康状况：</span></td>
		<td><span id="lbl_jkzk"></span></td>
		<td class="trbg1"><span id="lbxsgrxx_xlcc">学历层次：</span></td>
		<td colspan="2"><span id="lbl_CC">本科</span></td>
		<td class="trbg1" width="241" colspan="2"><span id="lbxsgrxx_mqdwdhhsj">母亲单位电话或手机：</span></td>
		<td><input name="mqdwdh" type="text" id="mqdwdh"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_xy">学院：</span></td>
		<td><span id="lbl_xy">计算机学院</span></td>
		<td class="trbg1"><span id="lbxsgrxx_gatm">港澳台码：</span></td>
		<td colspan="2"><span id="lbl_gatm"></span></td>
		<td class="trbg1" width="241" colspan="2"><span id="lbxsgrxx_jtdz">家庭地址：</span></td>
		<td><input name="jtdz" type="text" value="皖宿松县汇口镇曹湖村西街组" id="jtdz"></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_x">系：</span></td>
		<td><span id="lbl_xi">软件工程</span></td>
		<td class="trbg1"><span id="lbxsgrxx_bdh">报到号：</span></td>
		<td colspan="2"><span id="lbl_bdh"></span></td>
		<td class="trbg1" width="241" colspan="2"><span id="lbxsgrxx_jtszd">家庭所在地（/省/县）：</span></td>
		<td><span id="lbl_jtszd">安徽省安庆宿松县</span></td>
	</tr>
	<tr>
		<td id="TDzymc" class="trbg1"><span id="lbxsgrxx_zymc">专业名称：</span></td>

		<td><span id="lbl_zymc">软件工程</span></td>
		<td class="trbg1" id="TDsfgspydy" colspan="3"><span id="lbxsgrxx_sfgspydy">是否高水平运动员：</span><span id="lbl_SFGSPYDY"></span></td>
		<td class="trbg1" valign="top" rowspan="4"><span id="lbxsgrxx_bz">备注：</span></td>
		<td colspan="2" rowspan="4"><span id="lbl_bz"></span></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_jxbmc">教学班名称：</span></td>
		<td><span id="lbl_JXBMC"></span></td>
		<td id="TDyydj" class="trbg1"><span id="lbxsgrxx_yydj">英语等级：</span></td>

		<td colspan="2"><span id="lbl_yydj"></span></td>
	</tr>
	<tr>
		<td class="trbg1"><span id="lbxsgrxx_xzb">行政班：</span></td>
		<td><span id="lbl_xzb">软111</span></td>
		<td id="TDyycj" class="trbg1"><span id="lbxsgrxx_yycj">英语成绩：</span></td>

		<td colspan="2"><span id="lbl_YYCJ"></span></td>
	</tr>
	<tr>
		<td id="TDxz" class="trbg1"><span id="lbxsgrxx_xz">学制：</span></td>

		<td><span id="lbl_xz">4</span></td>
		<td id="TDljbym" class="trbg1"><span id="lbxsgrxx_ljbym">录检表页码：</span></td>

		<td colspan="2"><span id="lbl_LJBYM"></span></td>
	</tr>
	<tr>
		<td class="trbg1" height="39"><span id="lbxsgrxx_xxnx">学习年限：</span></td>
		<td height="39"><span id="lbl_xxnx"></span></td>
		<td class="trbg1" height="39"><span id="lbxsgrxx_tc">特长：</span></td>
		<td colspan="2" height="39"><span id="lbl_tc"></span></td>
		<td class="trbg1" width="241" colspan="2" height="39"></td>
		<td height="39"></td>
	</tr>
	<tr>
		<td class="trbg1" height="39"><span id="lbxsgrxx_xjzt">学籍状态：</span></td>
		<td height="39"><span id="lbl_xjzt">有</span></td>
		<td class="trbg1" height="39"><span id="lbxsgrxx_rdsj">入党(团)时间：</span></td>
		<td colspan="2" height="39"><span id="lbl_RDSJ"></span></td>
		<td class="trbg1" width="241" colspan="2" height="39"></td>
		<td height="39"></td>
	</tr>
	<tr>
		<td class="trbg1" height="39"><span id="lbxsgrxx_dqszj">当前所在级：</span></td>
		<td height="39"><span id="lbl_dqszj">2011</span></td>
		<td id="TDccqj" class="trbg1" height="39"><span id="lbxsgrxx_hczdz">火车终点站：</span></td>

		<td colspan="2" height="39"><span id="lbl_ccqj"></span></td>
		<td class="trbg1" width="241" colspan="2" height="39"><span id="lbxsgrxx_zmr">证明人：</span></td>
		<td height="39"></td>
	</tr>
	<tr id="trksh">
		<td class="trbg1"><span id="lbxsgrxx_ksh">考生号：</span></td>
		<td><span id="lbl_ksh">11340826151897</span></td>
		<td class="trbg1"><span id="lbxsgrxx_xxxs">学习形式：</span></td>
		<td colspan="2"><span id="lbl_xxxs"></span></td>
		<td class="trbg1" width="241" colspan="2"></td>
		<td><font face="宋体"></font></td>
	</tr></tbody></table>
	*/
	public function getUserInfo(){
		if(empty($this->_stuId)){
			$this->_error = '没有权限';
			return false;
		}
		if (!$this->_client->get('/xsgrxx.aspx?xh='.$this->_stuId)) {
			die('发生错误: '.$client->getError());
		}
		$temp= $this->_client->getContent();
		$pageContents = mb_convert_encoding($temp,'utf-8','gb2312');
		//对页面进行解析
		$html = str_get_html($pageContents);
		$info = array();
		//学号
		$info['stu_id'] = $html->find('#xh',0)->plaintext;
		//姓名
		$info['name'] = $html->find('#xm',0)->plaintext;
		//性别
		$info['sex'] = $html->find('#lbl_xb',0)->plaintext;
		//专业
		$info['major'] = $html->find('#lbl_zymc',0)->plaintext;
		//学院
		$info['academy'] = $html->find('#lbl_xy',0)->plaintext;
		//当前所在级
		$info['grade'] = $html->find('#lbl_dqszj',0)->plaintext;
		//班级
		$info['class'] = $html->find('#lbl_xzb',0)->plaintext;
		//手机号码
		$info['phone'] = $html->find('#lbl_TELNUMBER',0)->plaintext;
		//家庭所在地
		$info['adress'] = $html->find('#lbl_jtszd',0)->plaintext;
		//出生日期
		$info['birth'] = $html->find('#lbl_csrq',0)->plaintext;
		//身份证号，身份证号加密处理
		$temp = $html->find('#lbl_sfzh',0)->plaintext;
		$info['idcard'] = jiami($temp,'yunfou');
		
		return $info;
	}
	//用于处理class数组array()
	/*private function dealClassArr($arr){
		if(empty($arr) || !is_array($arr))
			return false;
		foreach($this->_classes as $k=>$class){
			if($class[0]['name'] == $arr['name']){
				$this->_classes[$k][] = $arr;
				//$class[] = $arr;
				break;	
			}
		}
		//array_push($this->_classes,array($arr));
		return ;
	}*/
	//处理$this->_classes,主要是将其录入数据库
	/*private function dealClassesArr(){
		$schedule = array(); 
		foreach($this->_classes as $class){
			$isExist = M('Class')->where(array('class_data'=>serialize($class)))->find();
			if(!$isExist){
				$id = M('Class')->add(array('class_data'=>serialize($class)));
				$schedule[] = $id;	
			}else{
				$schedule[] = $isExist['id'];
			}
		}
		array_unique($schedule);
		$str = implode(',',$schedule);
		M('UserDetail')->where(array('uid'=>$this->_uid))->setField(array('schedule'=>$str,'is_load'=>1));
		//$recordes = M('Class')->select();
	}*/
	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
} 

?>