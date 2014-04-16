<?php
/**
 * 文件上传
 * @example {:W('Upload',array('uploadType'=>'file','inputname'=>'inputname','urlquery'=>'a=aaa&b=bb','attachIds'=>'1,2,3,4'))}
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */

class UploadWidget extends Widget{
    private  static $rand = 1;

    /**
     * @param string uploadType 上传类型，分file和image
     * @param string inputname 上传组件的name
     * @param string urlquery 需要存储在附件表的一些信息，暂时可能用到的不多
     * @param mixed attachIds 已有附件ID，可以为空
     */
	public function render($data){
		$var = array();
		$var['uploadType'] = 'file';
		$var['inputname'] = 'attach';
		$var['attachIds'] = '';
		$var['inForm'] = 1;
		
		is_array($data) && $var = array_merge($var, $data);
		
		$uploadType = in_array($var['uploadType'], array('image','file')) ? t($var['uploadType']) : 'file';
		$uploadTemplate = $uploadType.'Upload';
		
		if(!empty($var['attachIds'])){
			!is_array($var['attachIds']) && $var['attachIds'] = explode(',', $var['attachIds']);
			
			$attachInfo = D('Attach')->getAttachByIds($var['attachIds']);
			foreach($attachInfo as $v){
				if($attachInfo['uploadType'] == 'image'){
					$v['src'] = getImageUrl($v['save_path'].$v['save_name'],100,100,true);
				}
				$v['extension'] = strtolower($v['extension']);
				$var['attachInfo'][] = $v;
			}
			$var['attachInfo'] = implode('|',$var['attachInfo']);
		}
		//渲染模板
		$content = $this->renderFile($uploadTemplate, $var);
		
		unset($var, $data);
		
		return $content;
	}

    /**
     * 附件上传
     * @return array 上传的附件的信息
     */
/*	public function save(){
		$data['attach_type'] = t($_REQUEST['attach_type']);
		$data['upload_type'] = $_REQUEST['upload_type'] ? t($_REQUEST['upload_type']) : 'file';
		
		$thumb = intval($_REQUEST['thumb']);
		$whith = intval($_REQUEST['width']);
		$height = intval($_REQUEST['height']);
		$cut = intval($_REQUEST['cut']);
		
		$options['attach_type'] = $data['attach_type'];
		
		//关联别的表名的相关的信息
		$options['app_name'] = '';
		$options['table'] = '';
		$options['row_id'] = '';
		
		$info = D('Attach')->upload($data, $options);
		
		if($info['status']){
			$data = $info['info'];
			if($thumb == 1){
				$data['src'] = getImageUrl($data['save_path'].$data['save_name'],$whith,$height,$cut);
			}else{
				$data['src'] = $data['save_path'].$data['save_name'];
			}
			
			$data['extension'] = strtolower($data['extension']);
			$return = array('status' => 1, 'data' => $data);
		}else{
			$return  = array('status' => 0, 'info' => $info['info']);
		}
		return $return;
	}*/
	
    /**
     * 附件下载
     */
/*    public function down(){
   		$aid	=	intval($_GET['attach_id']);
		$attach	=	D('Attach')->getAttachById($aid);

		if(!$attach){
			die(L('PUBLIC_ATTACH_ISNULL'));
		}

        $filename = $attach['save_path'].$attach['save_name'];
        $realname = auto_charset ( $attach['name'], "UTF-8", 'GBK//IGNORE');

		//下载函数
		tsload(ADDON_PATH.'/library/Http.class.php');
        //从云端下载
        $cloud = model('CloudAttach');
        if($cloud->isOpen()){
            $url = $cloud->getFileUrl($filename);
            redirect($url);
            //$content = $cloud->getFileContent($filename); //读文件下载
            //Http::download('', $realname, $content);
        //从本地下载
        }else{
    		if(file_exists(UPLOAD_PATH.'/'.$filename)) {
    			Http::download(UPLOAD_PATH.'/'.$filename, $realname);
    		}else{
    			echo L('PUBLIC_ATTACH_ISNULL');
    		}
        }
    }*/
}
?>