<?php
   /**
	* 头像获取路径组件
	* @example <{:W('Attach',array('file_type'=>'image | file or...', 'app_name'=>'[应用名称]', 'table'=>'[表名称]'))}>
	* @version agdQ 1.0
	*/
class AttachWidget extends Widget {
	/**
     * @param 
	 * @param string 
     * @param string 
     */
	public function render($data) {
		$file_type = isset($data['file_type']) ? $data['file_type'] : 'file';
		if($file_type == 'image'){
			$var['url']       = '__APP__/Account/uploadImage?app_name='.$data['app_name'].'&table='.$data['table'];
			$var['add_name']  = '添加图片...';
			$var['name_file'] = '图片文件';
			$var['type_exts'] = '*.gif;*.jpg;*.png;*.bmp;*.jpeg';
			$var['file_max_size'] = '';
		}else{
			$var['url']       = '__APP__/Acount/uploadAttach?app_name='.$data['app_name'].'&table='.$data['table'];
			$var['add_name']  = '添加文件...';
			$var['name_file'] = '文件';
			$var['type_exts'] = '*.jpg;*.gif;*.png;*.jpeg;*.bmp;*.zip;*.rar;*.doc;*.xls;*.ppt;*.docx;*.xlsx;*.pptx;*.pdf';
			$var['file_max_size'] = '';
		}
		$content = $this->renderFile('Attach', $var);
		return $content;
	}
}
?>