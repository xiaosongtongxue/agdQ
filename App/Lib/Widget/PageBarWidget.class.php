<?php
class PageBarWidget extends Widget{
	
	public function render($data){
		$var['first'] = intval($data['first']);
		$var['cur'] = intval($data['cur']);
		$var['last'] = intval($data['last']);
		
		$var['page_bar'] = getPageBar($var['first'], $var['cur'], $var['last']);
		$content = $this->renderFile('PageBar',$var);
		unset($var, $data);
		// 输出数据
		return $content;
	}
}
?>