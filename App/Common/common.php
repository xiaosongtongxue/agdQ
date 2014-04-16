<?php

/**
 *=====================================================================================================================================
 * 新添加的函数
 *=====================================================================================================================================
*/

/**
 * 创建多级文件目录
 * @param string $path 路径名称
 * @return void
 */
function createFolder($path){
	if(!is_dir($path)){
		$this->_createFolder(dirname($path));
		mkdir($path);
	}
}
//2013-04-14 ZS  清空文件夹下所有文件和文件夹,慎用
function clear_dir($path){
	if(!is_dir($path)){
		echo "路径不正确";
		return false;
	}
	$mydir=dir($path);
	while($file=$mydir->read()){
		$sub_dir=$path.DIRECTORY_SEPARATOR.$file;
		//.表示本级目录，..表示上级目录
		if(($file==".") || ($file=="..")){
			continue;
		}
		//如果是文件夹,继续遍历
		elseif(is_dir($sub_dir)){
			clear_dir($sub_dir);
			rmdir($sub_dir);
		}
		//如果是文件，直接删除
		else
			unlink($sub_dir);
	}
	return true;
}
// 自动转换字符集 支持数组转换
function auto_charset($fContents,$from,$to){
	$from = strtoupper($from)=='UTF8'? 'utf-8':$from;
	$to   = strtoupper($to) == 'UTF8'? 'utf-8':$to;
	if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
		//如果编码相同或者非字符串标量则不转换，希望转换字符串标量或数组等非标量
		return $fContents;
	}
	if(is_string($fContents) ) {
		if(function_exists('mb_convert_encoding')){
			return mb_convert_encoding ($fContents, $to, $from);
		}elseif(function_exists('iconv')){
			return iconv($from,$to,$fContents);
		}else{
			return $fContents;
		}
	}elseif(is_array($fContents)){
		foreach ( $fContents as $key => $val ) {
			$_key = auto_charset($key,$from,$to);
			$fContents[$_key] = auto_charset($val,$from,$to);
			if($key != $_key )
				unset($fContents[$key]);
		}
		return $fContents;
	}else{
		return $fContents;
	}
}

/*
 * 分页工具函数
 * @auther icubit
 */
function getPageBar($first,$cur,$last){
	if($first>$cur || $cur>$last){
		return false;
	}
	$page = array();
	if($last - $first < 5){
		for($i = $first;$i <= $last;$i++)
			$page[] = $i;
	}else{
		if($last - $cur < 2){
			for($i = $last - 4;$i <= $last;$i++)
				$page[] = $i;
		}elseif ($cur - $first < 2){
			for($i = $first;$i <= $first + 4;$i++){
				$page[] = $i;
			}
		}else{
			for($i = $cur - 2;$i <= $cur + 2;$i++){
				$page[] = $i;
			}
		}
	}
	
	if($page[0] != $first){
		$f = true;
	}else{
		$f = false;
	}
	if($cur != $first){
		$prev = true;
	}else{
		$prev = false;
	}
	
	if($cur != $last){
		$next = true;
	}else{
		$next = false;
	}
	if($page[count($page)-1] != $last){
		$l = true;
	}else{
		$l = false;
	}
	return array('first'=>$first,'cur'=>$cur,'last'=>$last,'page'=>$page,'bfirst'=>$f,'bprev'=>$prev,'bnext'=>$next,'blast'=>$l);
	
}
 /**
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 * @return string
 */
function byte_format($size, $dec=2) {
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
         $size /= 1024;
           $pos++;
    }
    return round($size,$dec)." ".$a[$pos];
}
//文件名
/**
 * 获取缩略图
 * @param unknown_type $filename 原图路径、url
 * @param unknown_type $width 宽
 * @param unknown_type $height 高
 * @param unknown_type $cut 是否切割 默认不切割
 * @return string src和输入的$filename去掉ATTACH_URL.'/'之后的一样
 */
function getThumbImage($filename, $width=100, $height=100, $cut=false){
    $filename  = str_ireplace(ATTACH_URL, '',$filename); //将URL转化为本地地址
	$filename  = str_ireplace('./', '',$filename);
	$filename  = ltrim($filename, '/');
    $info      = pathinfo(ATTACH_PATH.'/'.$filename);
    $oldFile   = $info['dirname'].'/'.$info['filename'].'.'.$info['extension'];
    $thumbFile = $info['dirname'].'/'.$info['filename'].'_'.$width.'_'.$height.'.'.$info['extension'];
	
//	$oldFile = str_replace('\\','/', $oldFile);
//    $thumbFile = str_replace('\\','/',$thumbFile);
	
	$oldFile = str_replace(ATTACH_PATH, '', $oldFile);
    $thumbFile = str_replace(ATTACH_PATH, '', $thumbFile);    

    $oldFile    = ltrim($oldFile, '/');
    $thumbFile  = ltrim($thumbFile, '/');

    //原图不存在直接返回
    if(!file_exists(ATTACH_PATH.'/'.$oldFile)){
        $info['src']    = $oldFile;
        $info['width']  = intval($width);
        $info['height'] = intval($height);
        return $info;
    //缩图已存在
    }elseif(file_exists(ATTACH_PATH.'/'.$thumbFile)){
        $imageinfo      = getimagesize(ATTACH_PATH.'/'.$thumbFile);
        $info['src']    = $thumbFile;
        $info['width']  = intval($imageinfo[0]);
        $info['height'] = intval($imageinfo[1]);
        return $info;
    //执行缩图操作
    }else{
        $oldimageinfo     = getimagesize(ATTACH_PATH.'/'.$oldFile);
        $old_image_width  = intval($oldimageinfo[0]);
        $old_image_height = intval($oldimageinfo[1]);
        if($old_image_width <= $width && $old_image_height <= $height){
            @unlink(ATTACH_PATH.'/'.$thumbFile);
            @copy(ATTACH_PATH.'/'.$oldFile, ATTACH_PATH.'/'.$thumbFile);
            $info['src']    = $thumbFile;
            $info['width']  = $old_image_width;
            $info['height'] = $old_image_height;
            return $info;
        }else{
			import('ORG.Util.Image.ThinkImage');
			$Think_img = new ThinkImage(THINKIMAGE_GD); 
            //生成缩略图
            if($cut){
                //裁剪原图
				$Think_img->open(ATTACH_PATH.'/'.$filename)->crop($width,$height,0,0)->save(ATTACH_PATH.'/'.$thumbFile);
            }else{
                //生成缩略图
				$Think_img->open(ATTACH_PATH.'/'.$filename)->thumb($width, $height, 1)->save(ATTACH_PATH.'/'.$thumbFile);
            }
            //缩图不存在
            if(!file_exists($thumbFile)){
                $thumbFile = $oldFile;
            }
            $info['src']    = $thumbFile;
            $info['width']  = $width;
            $info['height'] = $height;
            return $info;
        }
    }
}

//获取图片信息 - 兼容云
function getImageInfo($file){
    $cloud = D('CloudImage');
    if($cloud->isOpen()){
        $imageInfo = getimagesize($cloud->getImageUrl($file));
    }else{
        $imageInfo = getimagesize(ATTACH_PATH.'/'.$file);
    }
    return $imageInfo;
}

//获取图片地址 - 兼容云
function getImageUrl($file,$width='0',$height='auto',$cut=false){
    $cloud = D('CloudImage');
    if($cloud->isOpen()){
        $imageUrl = $cloud->getImageUrl($file,$width,$height,$cut);
    }else{
        if($width>0){
            $thumbInfo = getThumbImage($file,$width,$height,$cut);
            $imageUrl = ATTACH_URL.'/'.ltrim($thumbInfo['src'],'/');
        }else{
            $imageUrl = ATTACH_URL.'/'.ltrim($file, '/');
        }
    }
    return $imageUrl;
}
//获取附件信息 - 兼容云
function getImageUrlByAttachId($attachid){
    if($attachInfo = D('Attach')->getAttachById($attachid)){
        return getImageUrl($attachInfo['save_path'].$attachInfo['save_name']);
    }else{
        return false;
    }
}
//获取附件地址 - 兼容云
function getAttachUrl($filename){
	//云端
	$cloud = D('CloudAttach');
	if($cloud->isOpen()){
		return  $cloud->getFileUrl($filename);
	}
	//本地
	if (file_exists ( ATTACH_PATH . '/' . $filename )) {
		return ATTACH_URL . '/' . $filename;
	} else {
		return '';
	}
}

function getAttachUrlByAttachId($attachid){
	if($attachInfo = D('Attach')->getAttachById($attachid)){
		return getAttachUrl($attachInfo['save_path'].$attachInfo['save_name']);
	}else{
		return false;
	}
}
//全站静态缓存,替换之前每个model类中使用的静态缓存
function static_cache($cache_id,$value=null){
	static $cacheHash = array();
	if(empty($cache_id)){
		return false;
	}
	if($value === null){
		//获取缓存数据
		return isset($cacheHash[$cache_id]) ? $cacheHash[$cache_id] : false;
	}else{
		//设置缓存数据
		$cacheHash[$cache_id] = $value;
		return $cacheHash[$cache_id];
	}
}

/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称。为空的话，该函数返回空数组
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey=""){
	$result = array();
	if(is_array($pArray)){
		foreach($pArray as $temp_array){
			if(is_object($temp_array)){
				$temp_array = (array) $temp_array;
			}
			$result[] = (""==$pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
		}
		return $result;
	}else{
		return false;
	}
}

/**
 * 时间处理函数
 * @return string 显示的时间
 */
function formatTime($publish_time){

	//10分钟以内：刚刚
	if((time()-$publish_time)<600){
		$publish_time = '刚刚';
	}else{
		//一个小时以内：XX分钟前
		if((time()-$publish_time)<3600){
			$minutes = intval((time()-$publish_time)/60);
			$publish_time = $minutes.'分钟前';
		}else{
			$now = getdate(time());
			$tmptime = getdate($publish_time);
			//今天凌晨的时间戳
			$today = time()-$now['hours']*3600+$now['minutes']*60+$now['seconds'];

			if($today-$publish_time<=0){//今天
				$publish_time = '今天'.'  '.$tmptime['hours'].':'.$tmptime['minutes'];
			}elseif($today-$publish_time<86400){//昨天
				$publish_time = '昨天'.'  '.$tmptime['hours'].':'.$tmptime['minutes'];
			}elseif($now['year']==$tmptime['year']){

				$publish_time = $tmptime['mon'].'月'.$tmptime['mday'].'日'.'  '.$tmptime['hours'].':'.$tmptime['minutes'];
			}else{
				$publish_time = $tmptime['year'].'年'.$tmptime['mon'].'月'.$tmptime['mday'].'日'.'  '.$tmptime['hours'].':'.$tmptime['minutes'];
			}
		}
	}
	return $publish_time;
}

/**
 * 加密函数
 * @param string $txt 需加密的字符串
 * @param string $key 加密密钥，默认读取SECURE_CODE配置
 * @return string 加密后的字符串
 */
function jiami($txt, $key = null) {
	empty($key) && $key = C('SECURE_CODE');
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
	$nh = rand(0, 64);
	$ch = $chars[$nh];
	$mdKey = md5($key.$ch);
	$mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
	$txt = base64_encode($txt);
	$tmp = '';
	$i = 0;
	$j = 0;
	$k = 0;
	for($i = 0; $i < strlen($txt); $i++) {
		$k = $k == strlen($mdKey) ? 0 : $k;
		$j = ($nh + strpos($chars, $txt [$i]) + ord($mdKey[$k++])) % 64;
		$tmp .= $chars[$j];
	}
	return $ch.$tmp;
}

/**
 * 解密函数
 * @param string $txt 待解密的字符串
 * @param string $key 解密密钥，默认读取SECURE_CODE配置
 * @return string 解密后的字符串
 */
function jiemi($txt, $key = null) {
	empty($key) && $key = C('SECURE_CODE');
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
	$ch = $txt[0];
	$nh = strpos($chars, $ch);
	$mdKey = md5($key.$ch);
	$mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
	$txt = substr($txt, 1);
	$tmp = '';
	$i = 0;
	$j = 0;
	$k = 0;
	for($i = 0; $i < strlen($txt); $i++) {
		$k = $k == strlen($mdKey) ? 0 : $k;
		$j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
		while($j < 0) {
			$j += 64;
		}
		$tmp .= $chars[$j];
	}
	return base64_decode($tmp);
}


/**
 * 用于判断文件后缀是否是图片
 * @param string file 文件路径，通常是$_FILES['file']['tmp_name']
 * @return bool
 */
function is_image_file($file){
  $fileextname = strtolower(substr(strrchr(rtrim(basename($file),'?'),"."),1,4));
  if(in_array($fileextname,array('jpg','jpeg','gif','png','bmp'))){
    return true;
  }else{
    return false;
  }
}

/**
 * t函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
function t($text){
    $text = nl2br($text);
    $text = real_strip_tags($text);
    $text = str_ireplace(array("\r","\n","\t","&nbsp;"),'',$text);
    $text = htmlspecialchars($text,ENT_QUOTES);
    $text = trim($text);
    return $text;
}
//输出安全的html
function h($text, $tags = null) {
	$text	=	trim($text);
	//完全过滤注释
	$text	=	preg_replace('/<!--?.*-->/','',$text);
	//完全过滤动态代码
	$text	=	preg_replace('/<\?|\?'.'>/','',$text);
	//完全过滤js
	$text	=	preg_replace('/<script?.*\/script>/','',$text);

	$text	=	str_replace('[','&#091;',$text);
	$text	=	str_replace(']','&#093;',$text);
	$text	=	str_replace('|','&#124;',$text);
	//过滤换行符
	$text	=	preg_replace('/\r?\n/','',$text);
	//br
	$text	=	preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
	$text	=	preg_replace('/<p(\s\/)?'.'>/i','[br]',$text);
	$text	=	preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
	//过滤危险的属性，如：过滤on事件lang js
	while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1],$text);
	}
	while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].$mat[3],$text);
	}
	if(empty($tags)) {
		$tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
	}
	//允许的HTML标签
	$text	=	preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
	$text = preg_replace('/<\/('.$tags.')>/Ui','[/\1]',$text);
	//过滤多余html
	$text	=	preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
	//过滤合法的html标签
	while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
	}
	//转换引号
	while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
		$text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
	}
	//过滤错误的单个引号
	while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
		$text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
	}
	//转换其它所有不合法的 < >
	$text	=	str_replace('<','&lt;',$text);
	$text	=	str_replace('>','&gt;',$text);
	$text	=	str_replace('"','&quot;',$text);
	//反转换
	$text	=	str_replace('[','<',$text);
	$text	=	str_replace(']','>',$text);
	$text	=	str_replace('|','"',$text);
	//过滤多余空格
	$text	=	str_replace('  ',' ',$text);
	return $text;
}

/**
 * 获取字符串的长度
 *
 * 计算时, 汉字或全角字符占1个长度, 英文字符占0.5个长度
 *
 * @param string  $str
 * @param boolean $filter 是否过滤html标签
 * @return int 字符串的长度
 */
function get_str_length($str, $filter = false){
    if ($filter) {
        $str = html_entity_decode($str, ENT_QUOTES);
        $str = strip_tags($str);
    }
    return (strlen($str) + mb_strlen($str, 'UTF8')) / 4;
}

function getShort($str, $length = 40, $ext = '') {
	$str    =   htmlspecialchars($str);		//该函数把一些预定义的字符转换为 HTML 实体
    $str    =   strip_tags($str);	//该函数始终会剥离 HTML,如过滤<b><img>等所有的HTML标签
    $str    =   htmlspecialchars_decode($str);	//该函数把一些预定义的 HTML 实体转换为字符
    $strlenth   =   0;
    $output     =   '';
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);
    foreach($match[0] as $v){
        preg_match("/[\xe0-\xef][\x80-\xbf]{2}/",$v, $matchs);
        if(!empty($matchs[0])){
            $strlenth   +=  1;
        }elseif(is_numeric($v)){
            //$strlenth +=  0.545;  // 字符像素宽度比例 汉字为1
            $strlenth   +=  0.5;    // 字符字节长度比例 汉字为1
        }else{
            //$strlenth +=  0.475;  // 字符像素宽度比例 汉字为1
            $strlenth   +=  0.5;    // 字符字节长度比例 汉字为1
        }

        if ($strlenth > $length) {
            $output .= $ext;
            break;
        }

        $output .=  $v;
    }
    return $output;
}

/**
* 检查是否是以手机浏览器进入(IN_MOBILE)
*/
function isMobile() {
    $mobile = array();
    static $mobilebrowser_list ='Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
    //note 获取手机浏览器
    if(preg_match("/$mobilebrowser_list/i", $_SERVER['HTTP_USER_AGENT'], $mobile)) {
        return true;
    }else{
        if(preg_match('/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }else{
            if($_GET['mobile'] === 'yes') {
                return true;
            }else{
                return false;
            }
        }
    }
}

function isiPhone()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false;
}

function isiPad()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false;
}

function isiOS()
{
    return isiPhone() || isiPad();
}

function isAndroid()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false;
}

/**
 * 获取用户浏览器型号。新加浏览器，修改代码，增加特征字符串.把IE加到12.0 可以使用5-10年了.
 */
function getBrowser(){
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Maxthon')) {
        $browser = 'Maxthon';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 12.0')) {
        $browser = 'IE12.0';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 11.0')) {
        $browser = 'IE11.0';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0')) {
        $browser = 'IE10.0';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0')) {
        $browser = 'IE9.0';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) {
        $browser = 'IE8.0';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) {
        $browser = 'IE7.0';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
        $browser = 'IE6.0';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'NetCaptor')) {
        $browser = 'NetCaptor';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')) {
        $browser = 'Netscape';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx')) {
        $browser = 'Lynx';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
        $browser = 'Opera';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
        $browser = 'Google';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
        $browser = 'Firefox';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
        $browser = 'Safari';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'iphone') || strpos($_SERVER['HTTP_USER_AGENT'], 'ipod')) {
        $browser = 'iphone';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
        $browser = 'iphone';
    } elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'android')) {
        $browser = 'android';
    } else {
        $browser = 'other';
    }
    return $browser;
}

/**
 * 检查Email地址是否合法
 *
 * @return boolean
 */
function isValidEmail($email) {
    return preg_match("/^[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email) !== 0;
}
function isValidStuId($stu_id){
	return preg_match("");
}
function real_strip_tags($str, $allowable_tags="") {
    $str = stripslashes(htmlspecialchars_decode($str));
    return strip_tags($str, $allowable_tags);
}
/**
 *
 * 正则替换和过滤内容
 *
 * @param  $html
 * @author jason
 */
function preg_html($html){
	$p = array("/<[a|A][^>]+(topic=\"true\")+[^>]*+>#([^<]+)#<\/[a|A]>/",
			"/<[a|A][^>]+(data=\")+([^\"]+)\"[^>]*+>[^<]*+<\/[a|A]>/",
			"/<[img|IMG][^>]+(src=\")+([^\"]+)\"[^>]*+>/");
	$t = array('topic{data=$2}','$2','img{data=$2}');
	$html = preg_replace($p, $t, $html);
	$html 	= strip_tags($html,"<br/>");
	return $html;
}
//获取一条微博的来源信息
function getFromClient($type=0, $app='public', $app_name){
	if ( $app != 'public' ){
		return '来自<a href="'.U($app).'" target="_blank">'.$app_name."</a>";
	}
	$type = intval($type);
	$client_type = array(
			0 => '来自网站',
			1 => '来自手机版',
			2 => '来自Android客户端',
			3 => '来自iPhone客户端',
			4 => '来自iPad客户端',
			5 => '来自win.Phone客户端',
	);

	//在列表中的
	if(in_array($type, array_keys( $client_type ))){
		return $client_type[$type];
	}else{
		return $client_type[0];
	}
}


//获取当前访问者的客户端类型
function getVisitorClient(){
	//客户端类型，0：网站；1：手机版；2：Android；3：iPhone；3：iPad；3：win.Phone
	return '0';
}

?>