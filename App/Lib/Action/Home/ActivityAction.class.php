<?php
/**
 * 活动体系设置控制器
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class ActivityAction extends CommonAction{
	
	private $editMenu = array(
		array('menu'=>'updateAct',     'name'=>'更新活动',   'href'=>'__URL__/updateAct'),
		array('menu'=>'afterAct',      'name'=>'活动后续',   'href'=>'__URL__/afterAct'),
		array('menu'=>'overAct',       'name'=>'活动设置',   'href'=>'__URL__/overAct'),
	);
	
	/**
	 * 更新活动页面
	 */
	public function updateAct(){
		$activity_id = intval($_REQUEST['activity_id']);
		$this->assign('activity_id', $activity_id);
		//菜单栏
		$this->assign('menulist',$this->editMenu);
		$this->assign('menu','updateAct');
		
		$this->setTitle('更新活动')->display();
	}
	
	/**
	 * 活动后续页面
	 */
	public function afterAct(){
		$activity_id = intval($_REQUEST['activity_id']);
		$this->assign('activity_id', $activity_id);
		//菜单栏
		$this->assign('menulist',$this->editMenu);
		$this->assign('menu','afterAct');
		
		$this->setTitle('活动后续')->display();
	}
	
	/**
	 * 活动结束页面
	 */
	public function overAct(){
		$activity_id = intval($_REQUEST['activity_id']);
		$this->assign('activity_id', $activity_id);
		//菜单栏
		$this->assign('menulist',$this->editMenu);
		$this->assign('menu','overAct');
		
		$actInfo = D('Activity')->getActivityById($activity_id);
		$this->assign('actInfo',$actInfo);
		
		$this->setTitle('活动设置')->display();
	}
	
	private $settingMenu = array(
		array('menu'=>'manageAct',        'name'=>'管理活动',        'href'=>'__URL__/manageAct'),
		array('menu'=>'underwayAct',      'name'=>'正在进行的活动',   'href'=>'__URL__/underwayAct'),
		array('menu'=>'completeAct',      'name'=>'已完成的活动',     'href'=>'__URL__/completeAct'),
		array('menu'=>'personalAct',      'name'=>'参加的活动',       'href'=>'__URL__/personalAct'),
	);
	
	/**
	 * 管理活动页面
	 */
	public function manageAct(){
		//菜单栏
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','manageAct');
		//获取个人创建的所有活动
		$actInfo = D('Activity')->getLoginUserActivityInfo();
		$this->assign('empty','<p class="text-warning">没有活动</p>');//没有创建活动的提示
		$this->assign('actInfo', $actInfo);
		
		$this->setTitle('管理活动')->display();
	}
	
	/**
	 * 正在进行的活动页面
	 */
	public function underwayAct(){
		//菜单栏
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','underwayAct');
		//获取正在进行的所有活动
		$actInfo = D('Activity')->getLoginUserUnderwayActivityInfo();
		$this->assign('empty','<p class="text-warning">没有正在进行的活动</p>');//没有正在进行的活动的提示
		$this->assign('actInfo', $actInfo);
		
		$this->setTitle('正在进行的活动')->display();
	}
	
	/**
	 * 已完成的活动页面
	 */
	public function completeAct(){
		//菜单栏
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','completeAct');
		//获取已完成的所有活动
		$actInfo = D('Activity')->getLoginUserCompleteActivityInfo();
		$this->assign('empty','<p class="text-warning">没有已完成的活动</p>');//没有已完成的活动的提示
		$this->assign('actInfo', $actInfo);
		
		$this->setTitle('已完成的活动')->display();
	}
	
	/**
	 * 个人参加的活动页面
	 */
	public function personalAct(){
		//菜单栏
		$this->assign('menulist',$this->settingMenu);
		$this->assign('menu','personalAct');
		//获取个人参加所有活动TODO：=====================================================================
		$actInfo = D('Activity')->getLoginUserActivityInfo();
		$this->assign('empty','<p class="text-warning">你没有参加活动</p>');//没有参加活动的提示
		$this->assign('actInfo', $actInfo);
		
		$this->setTitle('个人参加的活动')->display();
	}
	
	/**
	 * 所有校园活动展示页面
	 */
	public function campusActivity(){
		// 获取所有活动的信息
		$activityInfo = D('Activity')->getAllActivityInfo();
		$this->assign('activityInfo',$activityInfo);
		// 校园活动展示标题 <!>最好从后台设置</!>
		$campusActivityTitle = "所有好玩的校园活动哦~";
		$campusActivityIntro = "这里是所有的校园活动,你在这里会找到你喜欢的活动哦~要参加活动扩大自己的交际圈啊~";
		$this->assign('campusActivityTitle', $campusActivityTitle);
		$this->assign('campusActivityIntro', $campusActivityIntro);
		
		$this->setTitle('校园活动');
		$this->display();
	}
	
	/**
	 * 活动专题展示页面
	 */
	public function specialActivity(){
		$activityId = intval($_REQUEST['activityId']);
		$activity = D('Activity');
		// 活动专题相关信息
		$activityInfo = $activity->getActivityById($activityId);
		$this->assign('activityInfo',$activityInfo);
		
		// 海报相关信息
		$posters = $activity->getActivityPosterById($activityId);
		//dump($posters);
		
		//echo $activity->getLastError();
		$this->assign('posters', $posters);
		// 获取标签
		$tags = D('TagApp')->setTable('activity')->getAppTags($activityId);
		$this->assign('tags', $tags);
		
		$this->setTitle('活动专题');
		$this->display();
	}
	
	/**
	 * 校园编辑展示页面
	 */
	public function editActivity(){	
		$actInfo = D('Activity')->getLoginUserActivityInfo();
		$this->assign('actInfo', $actInfo);
		$this->setTitle('编辑活动');
		$this->display();
	}
	
	
	/**
	 * 测试发布活动页面
	 */
	public function testPostActivity(){
		//echo '{name:"xiaoluo"}';
		//$arr = array('name' => 'xiaoluo');
		//echo json_encode($arr);
		//exit(json_encode($_POST));
		
		dump($_POST);
		dump($_GET);
		
		//$return = array('name' => 'xiaoluo');
		//echo json_encode($return);exit();
		//$this->ajaxReturn($_POST, $return, 1);// data info status
	}
	
	/**
	 * 处理发布和更新活动表单
	 */
	public function doPostActivity(){
		$update_act_id   = $_GET['activity_id'];
		$ActivityTitle   = t($_POST['ActTitle']);
		$ActivityIntro   = t($_POST['ActIntro']);
		$ActivityContent =   $_POST['ActContent']; //不能用t()函数过滤
		$ActivityAddress = t($_POST['ActAddress']);
		$ActivityTime    =   $_POST['ActTime'];
		$ActivityTags    = t($_POST['ActTags']);
		$aid             =   $_POST['aid'];
		
		if(empty($ActivityTitle)){
			$this->ajaxReturn('','活动标题可不能为空啊,不然别人怎么来看你的活动啊~',0);
		}
		if(empty($ActivityIntro)){
			$this->ajaxReturn('','活动介绍是能让别人尽快了解你的活动滴,可不能为空啊~',0);
		}else{
			$ActivityIntro = getShort($ActivityIntro, 70);//70个中文或140个字符
		}		
		if(empty($ActivityContent)){
			$this->ajaxReturn('','活动内容是活动的灵魂,没内容怎么举办活动啊,是吧~',0);
		}
		if(empty($ActivityAddress)){
			$this->ajaxReturn('','没有活动地址,别人怎么去参加你的活动啊~',0);
		}
		if(empty($ActivityTime)){
			$this->ajaxReturn('','没有活动时间,别人怎么知道什么时候去参加你的活动啊~',0);
		}
		if(empty($ActivityTags)){
			$this->ajaxReturn('','定义活动的标签,别人可能会更快的找到你的活动哦',0);
		}
		
		$data['uid']     =  intval($_SESSION[C('USER_AUTH_KEY')]);//活动的创建者ID
		$data['title']   =  $ActivityTitle;//活动的标题
		$data['intro']   =  $ActivityIntro;//活动的简介
		$data['poster']  =  (empty($aid) ? '' : (is_array($aid) ? implode(',', $aid) : $aid));//活动的海报
		$data['content'] =  $ActivityContent;//活动的内容
		$data['time']    =  $ActivityTime;//活动时间
		$data['address'] =  $ActivityAddress;//活动地点
		$data['ctime']   =  time();//创建活动的时间
		$data['is_post'] =  1;//是否发布活动
		// 标签这个字段有点多余
		$data['tag']     =  $ActivityTags;//活动的标签
		
		!is_array($ActivityTags) && $ActivityTags = explode(',',preg_replace('/[，,]+/u', ',', $ActivityTags));
		
		if(empty($update_act_id)){
			$activityId = M('Activity')->add($data);
		}else{
			$activityId = M('Activity')->where('id='.$update_act_id)->save($data);
		}
		if($activityId){
			// 如果上传海报,则保存活动ID到Attach表里
			if(!empty($aid)){
				$where['attach_id'] = is_array($aid) ? array('IN', $aid) : $aid;
				$save['row_id'] = $activityId;
				M('Attach')->where($where)->save($save);
				
				// 获取已保存到服务器的活动图片,并删除多余的活动图片
				$getAid = D('Cache')->get('activityPosterIdPre'.$_SESSION[C('USER_AUTH_KEY')]);
				$diffAid = array_diff($attach_id, $aid);
				D('Attach')->doEditAttach($diffAid, 'deleteAttach');
				D('Cache')->rm('activityPosterIdPre'.$_SESSION[C('USER_AUTH_KEY')]);
			}
			// 保存标签
			if(!empty($ActivityTags)){
				D('TagApp')->setAppName('public')->setTable('activity')->setAppTags($activityId, $ActivityTags);			
			}
			$this->ajaxReturn($data,'活动信息保存成功',1);
		}else{
			$this->ajaxReturn($data,'活动信息保存失败',0);
		}
	}
	
	/**
	 * 处理活动后续表单
	 */
	public function doAfterAct(){
		$activity_id   = t($_GET['activity_id']);
		$ActSummary   = t($_POST['ActSummary']);
		$aid             =   $_POST['aid'];
		if(empty($ActSummary)){
			$this->ajaxReturn('','活动总结可不能为空啊',0);
		}
		$data['summary']   =  $ActSummary;
		$data['continue']   =  1;
		$data['afterpic']  =  (empty($aid) ? '' : (is_array($aid) ? implode(',', $aid) : $aid));//活动的海报
		$activityId = M('Activity')->where('id='.$activity_id)->save($data);
		if($activityId){
			// 如果上传海报,则保存活动ID到Attach表里
			if(!empty($aid)){
				$where['attach_id'] = is_array($aid) ? array('IN', $aid) : $aid;
				$save['row_id'] = $activityId;
				M('Attach')->where($where)->save($save);
				
				// 获取已保存到服务器的活动图片,并删除多余的活动图片
				$getAid = D('Cache')->get('activityPosterIdPre'.$_SESSION[C('USER_AUTH_KEY')]);
				$diffAid = array_diff($attach_id, $aid);
				D('Attach')->doEditAttach($diffAid, 'deleteAttach');
				D('Cache')->rm('activityPosterIdPre'.$_SESSION[C('USER_AUTH_KEY')]);
			}
			$this->ajaxReturn($data,'活动信息保存成功',1);
		}else{
			$this->ajaxReturn($data,'活动信息保存失败',0);
		}
	}
	
	/**
	 * 处理活动结束表单
	 */
	public function doOverAct(){
		$activity_id   = t($_POST['activity_id']);
		if(empty($activity_id)){
			$this->ajaxReturn('','结束活动失败',0);
		}else{
			$where['id'] = $activity_id;
			$save['is_complete'] = 1;
			M('Activity')->where($where)->save($save);
			$this->ajaxReturn('','结束活动成功',1);
		}
	}
	
	/**
	 * 处理活动结束表单
	 */
	public function doDelAct(){
		$activity_id   = t($_POST['activity_id']);
		if(empty($activity_id)){
			$this->ajaxReturn('','删除活动失败',0);
		}else{
			$where['id'] = $activity_id;
			$actInfo = M('Activity')->where($data)->find();
			$actInfo['poster'] = explode(',',preg_replace('/[，,]+/u', ',', $actInfo['poster']));
			$actInfo['afterpic'] = explode(',',preg_replace('/[，,]+/u', ',', $actInfo['afterpic']));
			$totalAid = array_merge($actInfo['poster'], $actInfo['afterpic']);
			D('Attach')->doEditAttach($totalAid, 'deleteAttach');
			M('Activity')->where($where)->delete();
			$this->ajaxReturn('','删除活动成功',1);
		}
	}
	
	/**
	 * 处理在发布活动海报时点击删除按钮的操作
	 */
	public function delActImage(){
		$attach_ids = $_GET['aid'];
		$return = D('Attach')->doEditAttach($attach_ids, 'deleteAttach');
		$this->ajaxReturn('', $return['data'], $return['status']);
	}
	
	/**
	 * 活动图片上传
	 * @param attach_type 附件的所属内容
	 * @param  upload_type 上传的文件类型
	 * @param activity_id activity表中的ID
	 * @return 上传文件的相关信息
	 */
/*	public function uploadActivityImage(){
		// 海报图片保存路径设置
		$custom_path = date('Y/md/H/');
		$path = ATTACH_PATH.'/'.$custom_path;
		$pathURL = ATTACH_URL.'/'.$custom_path;
		$this->_createFolder($path);
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();						// 实例化上传类
		$upload->maxSize       =  8*1024*1024;					//设置上传图片的大小
		$upload->allowExts     =  array('jpg','png','gif','bmp','jpeg');	//设置上传图片的后缀
		$upload->savePath      =  $path;
		$upload->saveRule      =  'uniqid';			//设置上传文件规则
		
		if(!$upload->upload()) {						// 上传错误提示错误信息
		//				echo $upload->getErrorMsg();
			//$this->ajaxReturn('文件上传错误,请重新上传！',$upload->getErrorMsg(),0);
			$this->ajaxReturn($path,$upload->getErrorMsg(),0);
		}else{											// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			// 保存到Attach表的数据库
			$data['app_name']    = 'Activity';
			$data['table']       = 'activity';
			$data['attach_type'] = 'activity_file';
			$data['uid']         =  $_SESSION[C('USER_AUTH_KEY')];
			$data['ctime']        = time();
			$data['name']        = $info[0]['name'];
			$data['type']        = $info[0]['type'];
			$data['size']        = $info[0]['size'];
			$data['extension']   = $info[0]['extension'];
			$data['hash']        = $info[0]['hash'];
			$data['save_path']   = $custom_path;
			$data['save_name']   = $info[0]['savename'];
			$aid = M('Attach')->add($data);
			if($aid){
				$getAid = D('Cache')->get('activityPosterIdPre'.$_SESSION[C('USER_AUTH_KEY')]);
				$getAid[] = $aid;
				D('Cache')->set('activityPosterIdPre'.$_SESSION[C('USER_AUTH_KEY')], $getAid);
			}
			$info[0]['aid'] = $aid;
			
			$info[0]['savepath'] = $path;
			$this->ajaxReturn($pathURL.$info[0]['savename'],$info,1);
		}*/
		
	/*	$data['attach_type'] = 'activity_file';
		$data['upload_type'] = 'image';
		$options['attach_type'] = $data['attach_type'];
		
		//关联别的表名的相关的信息
		$options['app_name'] = 'Activity';
		$options['table'] = 'activity';
		
		$info = D('Attach')->upload($data, $options);
		$data = $info['info'];
		if($info['status']){
			$data['src'] = $data['save_path'].$data['save_name'];
			$data['extension'] = strtolower($data['extension']);
			
			$this->ajaxReturn($data['src'],$data,1);
		}else{
			$this->ajaxReturn('操作失败',$data,0);
		}
	}*/
	
	/**
	 * 创建多级文件目录
	 * @param string $path 路径名称
	 * @return void
	 */
	private function _createFolder($path){
		if(!is_dir($path)){
			$this->_createFolder(dirname($path));
			mkdir($path);
		}
	}
}
?>