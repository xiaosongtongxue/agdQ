<?php
/**
 * IndexAction （首页）初始化模块
 * @author  xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class IndexAction extends CommonAction {

	/**
     * 首页
     * @param integer $page 用于显示feed列表第几页，默认为1
     * @param integer $cid 圈子ID，默认为0
     */
    public function index($cid=0,$page=1){

    	isset($_REQUEST['cid']) && $cid = intval($_REQUEST['cid']);
    	isset($_REQUEST['page']) && $page = intval($_REQUEST['page']);

		$uid = $_SESSION[C('USER_AUTH_KEY')];

		//$this->assign('uid',$uid);
		$this->assign('page',$page);
		$this->assign('cid',$cid);

		$this->setTitle('安工大圈首页')->display();
    }
}