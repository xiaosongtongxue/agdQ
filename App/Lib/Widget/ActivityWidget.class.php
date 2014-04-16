<?php
   /**
	* 活动相关组件
	* @example <{:W('Activity',array('file_html'=>'singleActivity | Activity','activity_id'=>'['活动ID']'))}>
	* @version agdQ 1.0
	*/
class ActivityWidget extends Widget {
	/**
     *@param file_html 组件模板文件名称 有singleActivity 和Activity可选择
	 *@param activity_id 活动的ID
     */
	public function render($data) {
		$fileName = isset($data['file_html']) ? $data['file_html'] : 'Activity';
		if($fileName == 'singleActivity'){
			// TODO:活动专题需要的相关信息
			$activityInfo = D('Activity')->getActivityByUserId($data['activity_id']);
			$var = array_merge($var, $activityInfo);
		}else{
			// TODO:展示活动需要的相关信息
		}
		
		$content = $this->renderFile($fileName, $var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>