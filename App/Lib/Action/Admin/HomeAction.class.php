<?php

class HomeAction extends AdministratorAction {
	public function statistics()
	{
		// 插入统计数据
		//$gradeInfo = model('System')->upgrade();
		$statistics = array();

		/**
		 * 重要: 为了防止与应用别名重名，“服务器信息”、“用户信息”、“开发团队”作为key前面有空格
		 */

		// 服务器信息
		//$site_version = model('Xdata')->get('siteopt:site_system_version');
		$serverInfo['核心版本'] = 'agdQ1.0';
        $serverInfo['服务器系统及PHP版本']	= PHP_OS.' / PHP v'.PHP_VERSION;
        $serverInfo['服务器软件'] = $_SERVER['SERVER_SOFTWARE'];
        $serverInfo['最大上传许可'] = (@ini_get('file_uploads')) ? ini_get('upload_max_filesize') : '<font color="red">no</font>';
        // 数据库信息
        $mysqlinfo = D('')->query("SELECT VERSION() AS version");
        $serverInfo['MySQL版本'] = $mysqlinfo[0]['version'] ;

        $t = D('')->query("SHOW TABLE STATUS LIKE '".C('DB_PREFIX')."%'");
        foreach($t as $k) {
            $dbsize += $k['Data_length'] + $k['Index_length'];
        }
        
       $umap['is_del'] = 0;
       $userInfo['totalUser'] = M('User')->where($umap)->count();				// 用户总数
	   
       $aumap['last_login_time'] = array('GT', time() - 24 * 3600 * 60);		// 2个月内登录过的用户算活跃用户,GT大于操作
       $userInfo['activeUser'] = M('User')->where($aumap)->count();
        
       $ymap['day'] = date('Y-m-d', strtotime("-1 day"));
       $d = M('online_stats')->where($ymap)->find();
       $userInfo['yesterdayUser'] = $d['most_online'];

//        $onmap['uid'] = array('GT', 0);
//        $onmap['activeTime'] = array('GT', time() - 1800);
// //       $userInfo['onlineUser'] = count(D()->table(C('DB_PREFIX').'online')->where($onmap)->findAll());
//        $onmap['uid'] = 0;
//  //      $userInfo['onlineUser'] += count(D()->table(C('DB_PREFIX').'online')->where($onmap)->findAll());		// 加上游客
//
//		$ymap['day'] = array('GT', date('Y-m-d', strtotime("-7 day")));
// //       $d = D('online_stats')->where($ymap)->field('max(most_online) AS most_online')->find();
//        $userInfo['weekAvg'] = $d['most_online'];
//
        $this->assign('userInfo', $userInfo);
//
//        $ymap['day'] = array('GT', date('Y-m-d', strtotime("-7 day")));
// //       $d = D('online_stats')->where($ymap)->getHashList('day', '*');
//        
//        $visitCount = array();
//        $today = date('Y-m-d');
//        $yesterday = date('Y-m-d', strtotime('-1 day'));
//        $visitCount['today'] = array('pv'=>$d[$today]['total_pageviews'],'pu'=>$d[$today]['total_users'],'guest'=>$d[$today]['total_guests']);
//        $visitCount['yesterday'] = array('pv'=>$d[$yesterday]['total_pageviews'],'pu'=>$d[$yesterday]['total_users'],'guest'=>$d[$yesterday]['total_guests']);
//        $apv = 0;
//        $apu = 0;
//        $agu = 0;
//        foreach($d as $v) {
//        	$apv += $v['total_pageviews'];
//        	$apu += $v['total_users'];
//        	$agu += $v['total_guests'];
//        }
//
//        $visitCount['weekAvg'] = array('pv'=>ceil($apv/count($d)),'pu'=>ceil($apu/count($d)),'guest'=>ceil($agu/count($d)));
//        $this->assign('visitCount', $visitCount);

 //       $serverInfo['数据库大小'] = byte_format($dbsize);
        $statistics['服务器信息'] = $serverInfo;
        unset($serverInfo);

        // 开发团队
        $statistics['开发团队'] = array(
        	'版权所有' => '<a href="http://www.yunfou.net" target="_blank">'.'安工大云否团队'.'</a>',
        );

        $this->assign('statistics', $statistics);
       	$this->display();
	}
}
	
?>