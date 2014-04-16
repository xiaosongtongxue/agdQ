<?php
/**
 * 模板样式模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class TemplateModel extends Model {
	private $_template_style_model;  //模板样式模型字段
	private $_error;					// 错误信息字段
	
	/**
	 * 初始化操作，实例化用户模型对象 
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->_template_style_model = M('Template_style');
	}
	
	/**
	 * 获取登录页面的背景图片
	 * @return $file['backgroud_image'] 文件名称
	 */
	public function getBackgroudImageName(){
		$backgroudImages = 'default.jpg';
		$SystemConfig = D('Admin://SystemConfig');
		$value = $SystemConfig->getSystemConfig('DEFAULT_LOGIN_BG');
		
		//如果不开启默认登录背景，则开启随机模式
		if(!$value){
			import('ORG.Util.String');
			$backgroudImages = String::randNumber(1,5).'.jpg';	
		}
		return $backgroudImages;
	}
	
	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
}
?>