// JavaScript Document
window.onload=function(){
	$('#btnXiuxiu').click(function(){
		xiuxiu.embedSWF("altContent",1,"600px","600px");
        /*第1个参数是加载编辑器div容器，第2个参数是编辑器类型，第3个参数是div容器宽，第4个参数是div容器高*/
		xiuxiu.setUploadType(2);
		//xiuxiu.setUploadDataFieldName ('Filedata');
		xiuxiu.setUploadURL('http://localhost/agdQ/index.php/PublicTpl/doBeautifyPic');//修改为您自己的上传接收图片程序
		xiuxiu.onInit = function (){
			//xiuxiu.loadPhoto("http://open.web.meitu.com/sources/images/1.jpg");
		}	
		xiuxiu.onUploadResponse = function (data){
			//alert("上传响应" + data);  可以开启调试
			$('#myModal').modal('hide');
			$('#xiaoluo').append('<img src="/agdQ/App/Lib/Action/Home/'+data+'" />');
		}
		xiuxiu.onClose = function (id){
			$('#myModal').modal('hide');
		}
	});
}