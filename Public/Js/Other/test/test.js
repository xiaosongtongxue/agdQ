/**
 * 核心Js函数库文件，目前已经在core中自动加载
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */

/*
 * 字符串长度 - 中文和全角符号为1；英文、数字和半角为0.5
 *@param string str 字符串
 *@param boolean shortUrl 目的是：以news|telnet|nttp|file|http|ftp|https任何一个单词首字母位置开始算起，超过10个算10个；例如：“http://www.xiaoluo.com/index/index.html”===》算10个，
 				“xiaoluohttp://www.xiaoluo.com/index/index.html”===》算14个，要算上以上单词之前的数，
				“http://www.xiaoluo.com/index/index.htmlhttp://www.xiaoluo.com/index/index.html”===》算10个
 */
var getLength = function(str, shortUrl) {
	if (true == shortUrl) {
		// 一个URL当作十个字长度计算
		return Math.ceil(str.replace(/((news|telnet|nttp|file|http|ftp|https):\/\/){1}(([-A-Za-z0-9]+(\.[-A-Za-z0-9]+)*(\.[-A-Za-z]{2,5}))|([0-9]{1,3}(\.[0-9]{1,3}){3}))(:[0-9]*)?(\/[-A-Za-z0-9_\$\.\+\!\*\(\),;:@&=\?\/~\#\%]*)*/ig, 'xxxxxxxxxxxxxxxxxxxx')
							.replace(/^\s+|\s+$/ig,'').replace(/[^\x00-\xff]/ig,'xx').length/2);
	} else {
		return Math.ceil(str.replace(/^\s+|\s+$/ig,'').replace(/[^\x00-\xff]/ig,'xx').length/2);
	}
};

/*
 * 截取字符串
 *@param string str 字符串
 *@param int len 长度-中文2字节，英文1字节
 *@return string 字符串
 */
var subStr = function(str, len) {
    if(!str) {
    	return '';
    }
    len = len > 0 ? len * 2 : 280;
    var count = 0;			// 计数：中文2字节，英文1字节
	var temp = '';  		// 临时字符串
    for(var i = 0; i < str.length; i ++) {
    	if(str.charCodeAt(i) > 255) {
        	count += 2;
        } else {
        	count ++;
        }
        // 如果增加计数后长度大于限定长度，就直接返回临时字符串
        if(count > len) {
        	return temp;
        }
        // 将当前内容加到临时字符串
		temp += str.charAt(i);
    }

    return str;
};

/*
 * 异步请求页面
 *@param url 处理表单的url
 *@param target 目标的标签id或class或name等等能识别元素的属性
 *@function callback 回调函数
 *@return boolean true or false
 */
var async_page = function(url, target, callback) {
	if(!url) {
		return false;
	} else if(target) {
		var $target = $(target);
		//$target.html('<img src="'+_THEME_+'/images/icon_waiting.gif" width="20" style="margin:10px 50%;" />');
	}
	$.post(url, {}, function(txt) {
		txt = eval("(" + txt + ")");//txt = json_encode($array);==============================================
		if(txt.status) {
			if(target) {
				$target.html(txt.data+txt.info);
			}
			if(callback) {
				if(callback.match(/[(][^()]*[)]/)) {
					eval(callback);
				} else {
					eval(callback)(txt);
				}
			}
			if(txt.info) {
				//ui.success(txt.info);
				eval('alert(txt.info)');
			}
		} else if(txt.info) {
			//ui.error(txt.info);
			eval('alert(txt.info)');
			return false;
		}
	});
	return true;
};