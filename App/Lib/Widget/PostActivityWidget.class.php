<?php
   /**
	* 编辑活动相关组件
	* @example <{:W('PostActivity',array('file_html'=>'afterActivity | postActivity | preActivity','activity_id'=>'['活动的ID']'))}>
	* @version agdQ 1.0
	*/
class PostActivityWidget extends Widget {
	/**
     *@param file_html 组件模板文件名称 有afterActivity | postActivity | preActivity可选择
	 *@param activity_id 活动的ID
     */
	public function render($data) {
		$fileName =  $data['file_html'];
		$var['activity_id'] = $data['activity_id'];
		if($fileName == 'preActivity'){
			$var['actInfo'] = D('Activity')->getActivityById($var['activity_id']);
			$var['posterInfos'] = D('Attach')->getAttachByIds($var['actInfo']['poster']);
		}elseif($fileName == 'afterActivity'){
			$var['actInfo'] = D('Activity')->getActivityById($var['activity_id']);
		}elseif($fileName == 'postActivity'){
			// TODO:发布活动需要的相关信息
		}
		
		$content = $this->renderFile($fileName, $var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>