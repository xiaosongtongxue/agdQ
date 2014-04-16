<?php
   /**
	* 美图秀秀相关组件
	* @example <{:W('Xiuxiu',array('file_html'=>'fullEdition | beautifyPic | puzzle | avatarEditor'))}>
	* @version agdQ 1.0
	*/
class XiuxiuWidget extends Widget {
	/**
     *@param file_html 组件模板文件名称 fullEdition | beautifyPic | puzzle | avatarEditor
     */
	public function render($data) {
		$fileName =  $data['file_html'];
		if($fileName == 'fullEdition'){
			// 完整版
		}elseif($fileName == 'beautifyPic'){
			// 美化图片
		}elseif($fileName == 'puzzle'){
			// 拼图
		}elseif($fileName == 'avatarEditor'){
			// 头像编辑器
		}
		
		$content = $this->renderFile($fileName, $var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>