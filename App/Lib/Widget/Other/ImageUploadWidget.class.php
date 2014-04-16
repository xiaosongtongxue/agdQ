<?php
   /**
	* 上传图片（含有样式）相关组件
	* @example <{:W('ImageUpload',array('inputName'=>'[输入名称]'))}>
	* @version agdQ 1.0
	*/
class ImageUploadWidget extends Widget {
	/**
     *@param inputName 图片上传的名称，如活动海报
     */
	public function render($data) {	
		$var['inputName'] = $data['inputName'];
		$content = $this->renderFile('ImageUpload', $var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>