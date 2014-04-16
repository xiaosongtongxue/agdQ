<?php
/**
 * CircleAction 圈子模块
 * @author  xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class CircleAction extends CommonAction {	
	/**
	 * 圈子页面初始化
	 **/
    public function circle(){
		$this->assign('title','圈子');
		$this->display();
    }
	
	/**
	 * 创建圈子页面初始化
	 */
	public function createCircle(){
		
		$this->setTitle('创建圈子');
		$this->display();
	}
	
	/**
	 * 进入单个圈子页面
	 */
	public function singleCircle(){
		$user_circle_id = $_GET['circle_id'];
		$circle_info = D('UserCircle')->getUserCircle($user_circle_id);
		// 该圈创建者的用户信息
		$creator_user = D('UserCircle')->getCircleAdminInfo($user_circle_id);
		// 该圈除去创建者以外的所有用户信息
		$ex_all_user_info = D('UserCircleLink')->getUserInfoByCircleId($user_circle_id);
		$this->assign('circle_info', $circle_info)->assign('creator_user', $creator_user)->assign('ex_all_user_info', $ex_all_user_info);
		$this->setTitle('创建圈子');
		$this->display();
	}
	
	/**
	 * 处理创建圈子表单的信息
	 */
	public function doCreCircle(){
		//D('Cache')->set('createCircle', $_POST);
		$data['user_circle_name']    = t($_POST['CName']);
		$data['user_circle_intro']   = t($_POST['CIntro']);
		$data['user_circle_type']    = t($_POST['CType']);
		$data['app_name']            = 'circle';
		//$data['is_authenticate']     = 0;// 系统已默认为0，即不认证
		$aid = $data['user_circle_icon']    = t($_POST['aid'][0]);
		//添加圈子相关信息
		$circle_model = D('Circle');
		$result = $circle_model->addBaseCircle($data);
		if($result){
			// 把圈子和logo关联
			D('Attach')->linkID($aid, $result);
			$tags    = t($_POST['CTags']);
			D('TagApp')->setAppName($data['app_name'])->setTable('circle')->setAppTags($result, $tags);
			
			$circle['cid']       = $result;
			$circle['admin_id']  = intval($_SESSION[C('USER_AUTH_KEY')]);
			$circle['phone']     = t($_POST['CPhone']);
			$circle['qq']        = t($_POST['CQQ']);
			$res = $circle_model->addCircle($circle);
			if($res){
				$this->ajaxReturn('处理圈子表单', '创建成功', 1);
			}else{
				$this->ajaxReturn('', $circle_model->getLastError(), 0);
			}
		}else{
			$this->ajaxReturn('', $circle_model->getLastError(), 0);
		}
	}
}