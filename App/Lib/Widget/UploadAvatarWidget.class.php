<?php
   /**
	* 头像上传组件
	* @example <{:W('UploadAvatar')}>
	* @version agdQ 1.0
	*/
class UploadAvatarWidget extends Widget {
	
	/**
     * @param array
     */
	public function render($data) {
		$var = array();
		$var['uploadType'] = 'image';
		$var['inputname'] = 'avatar';
		$var['attachIds'] = '';
		$var['inForm'] = 1;
		
		is_array($data) && $var = array_merge($var, $data);
		
		$uploadType = in_array($var['uploadType'], array('image','file')) ? t($var['uploadType']) : 'file';
		
		$content = $this->renderFile('UploadAvatar', $var);
		
		return $content;
	}
}
?>