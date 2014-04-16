/**
 * 后台JS操作对象 -
 * 
 * 后台所有JS操作都集中在此
 */
var admin = {};

/*admin.checkAll = function(){
	$('#checkAll').bind('click', function() {
		if( this.checked == true ){
	        $('#list input[name="checkbox"]').attr('checked','true');
	        
	    }else{
	        $('#list input[name="checkbox"]').removeAttr('checked');
	       
	    }
		
	});
}
*/
admin.checkAll = function(o){
    if( o.checked == true ){
        $('#list input[type="checkbox"]').attr('checked','true');
        //$('tr[overstyle="on"]').addClass("bg_on"); 
    }else{
        $('#list input[type="checkbox"]').removeAttr('checked');
       // $('tr[overstyle="on"]').removeClass("bg_on");
    }
};
admin.doMoveGroup = function(){
	var ids = new Array();
	$.each($('#moveGroup input:checked'), function(i, n){
        if($(n).val() !='0' && $(n).val()!='' ){
            ids.push( $(n).val() );    
        }
    });
	if(ids.length<1){
		$.scojs_message('请选择用户组', $.scojs_message.TYPE_ERROR);
		return false;
    }

	ids = ids.join(',');
    var uid = $('#uid').val();
	//var uid = $('#movegroup input[name="uid"]').val();
    $.post('/agdQ/index.php/admin/User/doMoveGroup',{uid:uid,group_ids:ids},function(msg){
    	//admin.ajaxReload(msg);
    	$.scojs_message(msg.info, $.scojs_message.TYPE_OK);
    	//关闭对话框
    	var modal = $.scojs_modal({
    		  keyboard: true
    		});
    	modal.show();
    	modal.destroy();
    },'json');
}
/*admin.domoveUsergroup = function(){
	var ids = new Array();
    $.each($('#movegroup input:checked'), function(i, n){
        if($(n).val() !='0' && $(n).val()!='' ){
            ids.push( $(n).val() );    
        }
    });
    if(ids.length<1){
    	ui.error( L('PUBLIC_PLEASE_SELECT_USERGROUP') );return false;
    }	
    ids = ids.join(',');
    var uid = $('#uid').val();
    $.post(U('admin/User/domoveUsergroup'),{uid:uid,user_group_id:ids},function(msg){
    	admin.ajaxReload(msg);
    },'json');
};*/