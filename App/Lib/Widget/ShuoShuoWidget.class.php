<?php
/*说说基础组件
 *参数说明:feed_type:feed类型ID,placeholder:文本框默认内容,media:多媒体数组,button:按钮显示文字
 */
class ShuoShuoWidget extends Widget{
	public function render($data){
		//feed_id参数只在type为repost下起作用
		$var['feed_id'] = isset($data['feed_id']) ? intval($data['feed_id']) : 0;
		$var['type'] = isset($data['type']) ? $data['type'] : 'post';
		$var['feed_type'] = isset($data['feed_type']) ? intval($data['feed_type']) : 1;
		$var['placeholder'] = isset($data['placeholder']) ? $data['placeholder'] : '';
		$var['media'] = isset($data['media']) ? $data['media'] : array(1,1,1,1);
		$var['textarea_value'] = isset($data['textarea_value']) ? $data['textarea_value'] : '';
		
		$var['feed_type_name'] = M('FeedType')->where(array('tid'=>$var['feed_type']))->getField('tname');
		$var['button'] = isset($data['button']) ? t($data['button']) : '发表';
		$content = $this->renderFile('ShuoShuo',$var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>