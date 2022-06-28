var oldAlias = new Array();
var nameCheck = $('<strong>');

$(document).on('ready',function(event){

$('.delete').on('click',function(event){
	var button = $(event.target);
	var username = button.parent().parent().attr('id');
	if(confirm('Are you sure to delete the person from your contacts ?')){	
		$.ajax('server/chat_setting_process.php',{
			method:'POST',
			data:{
				'type':'delete',
				'contact':username,
			},
			timeout:10000,
			success:function(data,textStatus,jqXHR){ 
				if(data.success){
					alert(username+' has been deleted from your contacts');
					$('#'+username).remove();
				}
				else{
					alert('failed to delete, try again later');
				}
			},
			error:function(jqXHR,textStatus,errorThrown){
				alert('transmission error');
				console.log(errorThrown);
			}
		});	
	}
});

$('.info1').on('focusout',function(event){
	var input = $('.info1');
	if(!input.val()){
		input.attr('placeholder','username can not be null');
	}else{
		$.ajax('../server/server.php',{
			method:'POST',
			data:{
				'type':'username_query',
				'data':input.val()
			},
			timeout:10000,
			success:function(data,textStatus,jqXHR){
				if(data.success){
					if(data.pass){
						input.val('');
						input.attr('placeholder','no such user');
					}
				}else{
					input.val('');
					alert('failed to query the existence of username,please try again later');
					console.log(data.error);
				}
			},
			error:function(jqXHR,textStatus,errorThrown){
				input.val('');
				alert('failed to query the existence of username,please try again later');
				console.log(errorThrown);
			}
		});	
	}
});
//不要直接声明$('.alias'),否则当button.attr('class','newAliasButton');之后，会触发下面的$('td').on('click','.newAliasButton',function(event){}) 启动
$('td').on('click','.alias',function(event){
	var button = $(event.target);
	var td = button.parent().prev();
	oldAlias[button.parent().parent().attr('id')] = td.text();
	//alert(oldAlias[button.parent().parent().attr('id')]);
	td.text('');
	var input = '<input type="text" placeholder="new alias" />';
	td.append(input);
	button.attr('class','newAliasButton');
	alert('input new alias and press the modify alias button again');
});

var recover = function(button,td,value,id){
	td.children('input').remove()
	td.text(value);
	button.attr('class','alias');
	button.removeAttr('disabled');
}
//.newAliasButton是一开始不存在的元素,不能直接声明$('.newAliasButton')，否则无效
$('td').on('click','.newAliasButton',function(event){ 
	var button = $(event.target);
	var input = button.parent().prev().children('input');
	if(!input.val()){
		alert('alias can not be null');
	}else{
		button.attr('disabled','disabled');
		var username = button.parent().parent().attr('id');
		var newAlias = input.val();
		$.ajax('server/chat_setting_process.php',{
			method:'POST',
			data:{
				'type':'alias',
				'contact':username,
				'newAlias':newAlias
			},
			timeout:10000,
			success:function(data,textStatus,jqXHR){ 
				if(data.success){
					alert('alias modified');
					recover(button,input.parent(),newAlias);
				}
				else{
					alert('failed to send request, try again later');
					recover(button,input.parent(),oldAlias[username]);
				}
			},
			error:function(jqXHR,textStatus,errorThrown){
				alert('transmission error');
				console.log(errorThrown);
				recover(button,input.parent(),oldAlias[username]);
			}
		});
	}
});

$('.add_button').on('click',function(event){
	var username = $('.info1').val();
	var alias = $('.info2').val();
	if(!username){
		alert('you have not fill in the form completely');
	}else{
		$.ajax('server/chat_setting_process.php',{
			method:'POST',
			data:{
				'type':'add',
				'contact':username,
				'alias':alias
			},
			timeout:10000,
			success:function(data,textStatus,jqXHR){ 
				if(data.success){
					alert('the request has been sent, waiting for acceptance from the other');
				}
				else{
					alert('failed to send request. '+data.error);
					console.log(data.error);
				}
			},
			error:function(jqXHR,textStatus,errorThrown){
				alert('transmission error');
				console.log(errorThrown);
			}
		});
	}
});

});