<?php
/**
 * 附件模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class AttachModel extends Model {
	private $_xdata_model = null; //模型名称
	private $_attach_path = null; //附件的路径
	
	/**
	 * 初始化方法,初始化默认参数
	 * @return void
	 */
	public function _initialize() {
		$this->_xdata_model = D('Xdata');
		//$this->_attach_path = ltrim('Public/Uploads/Attach','/');
	} 

	/**
	 * 通过附件ID获取附件数据
	 * @param array $ids 附件ID数组
	 * @param string $field 附件数据显示字段，默认为显示全部
	 * @return array 相关附件数据
	 */
	public function getAttachByIds($ids, $field = '*') {
		if(empty($ids)) {
			return false;
		}
		!is_array($ids) && $ids = explode(',',preg_replace('/[，,]+/u', ',', $ids));
		$where['attach_id'] =	array('IN', $ids);
		$where['is_del'] = 0;//获取没删除的文件~不获取假删除的文件
		$info = $this->where($where)->field($field)->select();
		$data = $info;
		foreach($info as $index => $value){
			$data[$index]['save_path'] = ATTACH_PATH.'/'.$value['save_path'];
			$data[$index]['save_url'] = ATTACH_URL.'/'.$value['save_path'];
		}
		empty($data) && $data = array();
		
		return $data;
	}
	
	/**
	 * 通过单个附件ID获取其附件信息
	 * @param integer $id 附件ID
	 * @return array 指定附件ID的附件信息
	 */
	public function getAttachById($id) {
		if(empty($id)) {
			return false;
		}

		$where['attach_id'] = $id;
		$where['is_del'] = 0;//获取没删除的文件~不获取假删除的文件
		$sc = $this->where($where)->find();
		// TODO:下面一行代码放弃，已在全局文件中改正
//		$sc['save_path'] = ATTACH_PATH.'/'.$sc['save_path'];//此行不能用，否则全局文件都得改
		$sc['save_url'] = ATTACH_URL.'/'.$sc['save_path'];// 此行暂时不能注释，模板文件有用到此行，改时模板也要改
		empty($sc) && $sc = array();

		return $sc;
	}
	
	/**
	 * 删除附件信息，提供假删除功能
	 * @param integer $id 附件ID
	 * @param string $type 操作类型，若为delAttach则进行假删除操作，deleteAttach则进行彻底删除操作,默认为假删除
	 * @return array 返回操作结果信息
	 */
	public function doEditAttach($id, $type = 'delAttach') {
		$return = array('status'=>'0','data'=> '操作失败');//L('PUBLIC_ADMIN_OPRETING_ERROR'));		// 操作失败
		if(empty($id)) {
			$return['data'] = '附件ID不能为空';//L('PUBLIC_ATTACHMENT_ID_NOEXIST');			// 附件ID不能为空
		} else {
			$where['attach_id'] = is_array($id) ? array('IN', $id) : intval($id);
			$save['is_del'] = ($type == 'delAttach') ? 1 : 0;
			if($type == 'deleteAttach') {
				$resInfo = M('Attach')->where($where)->select();
				//	 TODO:删除附件文件 
				// 删除附件文件 
				foreach($resInfo as $attach){
					$filename = ATTACH_PATH.'/'.$attach['save_path'].$attach['save_name'];
					@unlink($filename);
				}
				/**
				 +-------------------------------------------------------------------------------------
				 * TODO:云储存删除代码~~
				 +-------------------------------------------------------------------------------------
				 */

				// 彻底删除操作
				$res = M('Attach')->where($where)->delete();
			} else {
				// 假删除或者恢复操作
				$res = M('Attach')->where($where)->save($save);
			}
			if($res) {
				//TODO:是否记录日志，以及后期缓存处理
				$return = array('status'=>1,'data'=> '操作成功');//L('PUBLIC_ADMIN_OPRETING_SUCCESS'));		// 操作成功
			}
		}
		return $return;
	}
	
	/**
	 * 获取所有附件的扩展名
	 * @return array 扩展名数组
	 */
	public function getAllExtensions() {
		$res = $this->field('extension')->group('extension')->select();
		return getSubByKey($res, 'extension');
	}

	/**
	 * 上传附件
	 * @param array $data 附件相关信息
	 		$data['upload_type'] == 'image'
	 * @param array $input_options 配置选项[不推荐修改, 默认使用后台的配置]
	 		$input_options['custom_path'] = date($system_default['attach_path_rule']); //定义的上传目录规则
			$input_options['max_size'] = floatval($system_default['attach_max_size']) * 1024 * 1024;//单位为：兆
			$input_options['allow_exts'] = $system_default['attach_allow_extension'];
			$input_options['allow_types'] = array();//允许上传的文件类型（留空为不限制），使用数组设置，默认为空数组 
			$input_options['save_path'] = ATTACH_PATH.'/'.$default_options['custom_path'];
			$input_options['save_name'] =	''; //指定保存的附件名.默认系统自动生成
			$input_options['save_to_db'] = true;
			$input_options['app_name'] = '';//'app_name'    => t($input_options['app_name']),
			$input_options['table'] = '';//'table'       => t($input_options['table']),
			$input_options['row_id'] = '';//'row_id'      => t($input_options['row_id']),
			$input_options['attach_type'] = '';//'attach_type' => t($input_options['attach_type']),
			$input_options['uid'] = '';//'uid'         => (int) $input_options['uid'] ? $input_options['uid'] : $_SESSION[C('USER_AUTH_KEY')],
			$input_options['private'] = '';//'private'     => $input_options['private'] > 0 ? 1 : 0,
			$input_options['from'] = '';//'from'        => isset($input_options['from']) ? intval($input_options['from']) : 0 , //暂时默认0为网页版，可以将0换成一个检测设备的方法
	 * @param boolean $thumb 是否启用缩略图
	 * @return array 上传的附件的信息
	 */
	public function upload($data = null, $input_options = null, $thumb = false){
		// 获取系统附件配置
		$system_default = $this->_xdata_model->get('admin_Config:attach');
		if(empty($system_default['attach_path_rule']) || empty($system_default['attach_max_size']) || empty($system_default['attach_allow_extension'])){
			$system_default['attach_path_rule'] = 'Y/md/H/'; //三级路径，年/月日/时
			$system_default['attach_max_size'] = 2;  //默认为2Mb,在后面得到代码中自会乘上1024的二次方
			$system_default['attach_allow_extension'] = 'jpg,gif,png,jpeg,bmp,zip,rar,doc,xls,ppt,docx,xlsx,pptx,pdf';
			
			$this->_xdata_model->put('admin_Config:attach',$system_default);
		}
		
		$system_default['attach_allow_extension'] = explode(',',$system_default['attach_allow_extension']);//上传文件时，需要是数组
		
		//载入默认规则
		$default_options = array();
		$default_options['custom_path'] = date($system_default['attach_path_rule']); //定义的上传目录规则
		$default_options['max_size'] = floatval($system_default['attach_max_size']) * 1024 * 1024;//单位为：兆
		$default_options['allow_exts'] = $system_default['attach_allow_extension'];
		$default_options['allow_types'] = array();//允许上传的文件类型（留空为不限制），使用数组设置，默认为空数组 
		$default_options['save_path'] = ATTACH_PATH.'/'.$default_options['custom_path'];
		$default_options['save_name'] =	''; //指定保存的附件名.默认系统自动生成
		$default_options['save_to_db'] = true;
		
		//定制化设置，覆盖默认设置
		$options = is_array($input_options) ? array_merge($default_options, $input_options) : $default_options;

        //云图片
        if($data['upload_type'] == 'image'){
            $cloud = D('CloudImage');
            if($cloud->isOpen()){
                return $this->cloudImageUpload($options);
            }else{
            	return $this->localUpload($options, $thumb);
            }
        }else{//云附件
            $cloud = D('CloudAttach');
            if($cloud->isOpen()){
                return $this->cloudAttachUpload($options);
            }else{
            	return $this->localUpload($options, false);
            }
        }
	}
	
	/**
	 * 处理本地服务器文件下载
	 *@param array $options 文件的相关设置
	 *@param boolean $thumb 是否进行缩略图处理
	 *@return array 返回文件的相关信息
	 */
	private function localUpload($options, $thumb){
		import('ORG.Net.UploadFile');
		// 实例化上传类
		$upload = new UploadFile();
		// 设置附件上传大小
		$upload->maxSize  = $options['max_size'] ;
		// 设置附件上传类型,允许上传的文件后缀（留空为不限制），使用数组设置，默认为空数组
		$upload->allowExts  = $options['allow_exts'] ;
		// 允许上传的文件类型（留空为不限制），使用数组设置，默认为空数组 
		is_array($options['allow_types']) && $upload->allowTypes = $options['allow_types'] ; 
		// 设置上传路径
		$upload->savePath = $options['save_path'];
        // 启用子目录
		$upload->autoSub = false;
		// 默认文件名规则
		$upload->saveRule = uniqid;
        // 是否缩略图====================================================
        $upload->thumb = $thumb;

		// 创建目录
		$this->_createFolder($upload->savePath);
		
		// 执行上传操作
        if(!$upload->upload()) {
			// 上传失败，返回错误
			$return['status'] = 0;
			$return['info']	= $upload->getErrorMsg();
			is_array((array)$return['info']) && $return = array_merge($return['info'],$options);			
			return $return;
		} else {
			$upload_info = $upload->getUploadFileInfo();
			// 保存信息到附件表
			$data = $this->saveInfo($upload_info, $options);
			// 输出信息
			$return['status'] = 1;
			$return['info']   = $data;
			// 上传成功，返回信息
			return $return;
    	}
	}
	
	/*
	 *保存附件的信息到数据库或不保存到数据库，且返回附件的相关信息
	 *@parem $upload_info 下载文件的相关信息
	 *@param $options 其它附件的相关信息
	 */
/*array
  0 => 
    array
      'name' => string '11.png' (length=6)
      'type' => string 'image/png' (length=9)
      'size' => int 51645
      'key' => string 'xiaoluo' (length=7)
      'extension' => string 'png' (length=3)
      'savepath' => string 'C:/wamp/www/agdQ/Public/Uploads/Attach/xiaoluo/' (length=48)
      'savename' => string '51fc6a0589c24.png' (length=17)
      'hash' => string 'bad6955417474cee54193f382a88b504' (length=32)*/
	  
	private function saveInfo($upload_info, $options){
		$data = array(
			'app_name'    => t($options['app_name']),
			'table'       => t($options['table']),
			'row_id'      => t($options['row_id']),
			'attach_type' => t($options['attach_type']),
			'uid'         => (int) $options['uid'] ? $options['uid'] : $_SESSION[C('USER_AUTH_KEY')],
			'ctime'       => time(),
			'private'     => $options['private'] > 0 ? 1 : 0,
			'is_del'      => 0,
			'from'        => isset($options['from']) ? intval($options['from']) : 0 , //暂时默认0为网页版，可以将0换成一个检测设备的方法
		);
		if($options['save_to_db']){
			foreach($upload_info as $u){
				$data['name']      = $u['name'];
				$data['type']      = $u['type'];
				$data['size']      = $u['size'];
				$data['extension'] = strtolower($u['extension']);
				$data['hash']      = $u['hash'];
				$data['save_path'] = $options['custom_path'];
				$data['save_name'] = $u['savename'];
				//$data['save_domain'] = C('ATTACH_SAVE_DOMAIN'); 	//如果做分布式存储，需要写方法来分配附件的服务器domain
				$aid = $this->add($data);
				$data['attach_id'] = intval($aid);
				$data['key']       = $u['key'];//UploadWidget.class.php里面的inputname的值
				$data['size']      = byte_format($data['size']);
				$infos[]           = $data;
				unset($data);
			}
		}else {
			foreach($upload_info as $u) {
				$data['name']      = $u['name'];
				$data['type']      = $u['type'];
				$data['size']      = byte_format($u['size']);
				$data['extension'] = strtolower($u['extension']);
				$data['hash']      = $u['hash'];
				$data['save_path'] = $options['custom_path'];
				$data['save_name'] = $u['savename'];
				//$data['save_domain'] = C('ATTACH_SAVE_DOMAIN'); 	//如果做分布式存储，需要写方法来分配附件的服务器domain
				$data['key']       = $u['key'];
				$infos[]           = $data;
				unset($data);
			}
		}
		return $infos;
	}
	
	/**
	 * 上传到云图片
	 * @param array $options 配置选项
	 * @return array 上传的图片的信息
	 */
	private function cloudImageUpload($options){
		$upload = D('CloudImage');
		$upload->customPath = $options['custom_path'];
		$upload->maxSize = $options['max_size'];
		$upload->allowExts = $options['allow_exts'];
		$upload->saveName = $options['save_name'];
		
		// 执行上传操作
        if(!$upload->upload()) {
			// 上传失败，返回错误
			$return['status'] = 0;
			$return['info']	= $upload->getErrorMsg();			
			return $return;
		} else {
			$upload_info = $upload->getUploadFileInfo();
			// 保存信息到附件表
			$data = $this->saveInfo($upload_info, $options);
			// 输出信息
			$return['status'] = 1;
			$return['info']   = $data;
			// 上传成功，返回信息
			return $return;
    	}
	}
	/**
	 * 上传到云附件
	 * @param array $options 配置选项
	 * @return array 上传的附件的信息
	 */
	private function cloudAttachUpload($options){
		$upload = D('CloudAttach');
		$upload->customPath = $options['custom_path'];
		$upload->maxSize    = $options['max_size'];
		$upload->allowExts  = $options['allow_exts'];
		$upload->saveName   = $options['save_name'];
       	
		// 执行上传操作
        if(!$upload->upload()) {
			// 上传失败，返回错误
			$return['status'] = 0;
			$return['info']	= $upload->getErrorMsg();			
			return $return;
		} else {
			$upload_info = $upload->getUploadFileInfo();
			// 保存信息到附件表
			$data = $this->saveInfo($upload_info, $options);
			// 输出信息
			$return['status'] = 1;
			$return['info']   = $data;
			// 上传成功，返回信息
			return $return;
    	}
	}
	
	/**
	 * 关联附件和其他相关应用
	 *@param integer $aids 附件的ID
	 *@param integer $other_id 其他需要关联的ID
	 *@return boolean 返回真假
	 */
	public function linkID($aids, $other_id){
		$where['attach_id'] = is_array($aids) ? array('IN', $aids) : $aids;
		$save['row_id'] = $other_id;
		$result = $this->where($where)->save($save);
		return $result;
	}
	
	/**
	 * 创建多级文件目录
	 * @param string $path 路径名称
	 * @return void
	 */
	private function _createFolder($path){
		if(!is_dir($path)){
			$this->_createFolder(dirname($path));
			mkdir($path);
		}
	}
}