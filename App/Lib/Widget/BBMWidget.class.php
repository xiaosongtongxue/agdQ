<?php
   /**
	* 帮帮忙postFeed组件
	* 
	* @version TS3.0
	*/
class BBMWidget extends Widget {
	/**
     
     */
	public function render($data) {
		$content = $this->renderFile();
		unset($var, $data);
        // 输出数据
		return $content;
	}
	
}

?>