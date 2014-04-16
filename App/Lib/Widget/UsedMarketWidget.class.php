<?php
   /**
	* 二手市场postFeed组件
	* 
	* @version TS3.0
	*/
class UsedMarketWidget extends Widget {
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