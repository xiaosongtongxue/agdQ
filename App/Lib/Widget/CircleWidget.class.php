<?php
   /**
	* 活动相关组件
	* @example <{:W('Circle',array('file_html' => 'Circle', 'circle_type' => '0 | 1 | 2 | 3...'))}>
	* @version agdQ 1.0
	*/
class CircleWidget extends Widget {
	/**
     *@param file_html 组件模板文件名称 有 和 可选择
	 *@param 
     */
	public function render($data) {
		$fileName = isset($data['file_html']) ? $data['file_html'] : 'Circle';
		if($fileName == 'Circle'){
			$var['circle_type_info'] = D('UserCircle')->getUserCircleByType($data['circle_type']);
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