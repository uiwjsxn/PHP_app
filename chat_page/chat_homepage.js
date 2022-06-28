$(document).on('ready',function(event){
	
$('body').on('click','.chat',function(event){
	var button = $(event.target);
	var contact = button.attr('id');
	button.attr('disabled','disabled');
	$.ajax('server/chat_homepage_process.php',{
		method:'POST',
		data:{
			'type_homepage':'chat',
			'contact':contact
		},
		timeout:10000,
		success:function(data,textStatus,jqXHR){ 
			if(data.success){
				button.removeAttr('disabled');
				$(location).attr('href','chat_pair.php');
			}else{
				alert('failed to load page for chatting');
				button.removeAttr('disabled');
			}
		},
		error:function(jqXHR,textStatus,errorThrown){
			button.removeAttr('disabled');
			alert('transmission error');
			console.log(errorThrown);
		}
	});
});

$('.accept').on('click',function(event){
	var button = $(event.target);
	var contact = button.parent().parent().attr('class');
	var alias = button.parent().prev().children().val();
	if(!alias) alias = contact;
	button.attr('disabled','disabled');
	$.ajax('server/chat_homepage_process.php',{
		method:'POST',
		data:{
			'type_homepage':'accept',
			'contact':contact,
			'alias':alias
		},
		timeout:10000,
		success:function(data,textStatus,jqXHR){ 
			if(data.success){ //在页面中渲染这个新联系人,并移除请求
				$('table:first').append("<tr><td>"+contact+"</td> <td>"+alias+"</td> <td>no</td>"+
					"<td><button id='"+contact+"' class='chat' type='button' >Chat</button></td></tr>");
				button.parent().parent().remove();
				alert('add contact success');
			}else{
				button.removeAttr('disabled');
				alert('failed to add contact now, try again later.');
			}
		},
		error:function(jqXHR,textStatus,errorThrown){
			button.removeAttr('disabled');
			alert('transmission error');
			console.log(errorThrown);
		}
	});
});

$('.decline').on('click',function(event){
	var button = $(event.target);
	var contact = button.parent().parent().attr('class');
	button.attr('disabled','disabled');
	$.ajax('server/chat_homepage_process.php',{
		method:'POST',
		data:{
			'type_homepage':'decline',
			'contact':contact
		},
		timeout:10000,
		success:function(data,textStatus,jqXHR){ 
			if(data.success){
				button.parent().parent().remove();
				alert('the contact is declined');
			}else{
				button.removeAttr('disabled');
				alert('failed to decline contact now, try again later.');
			}
		},
		error:function(jqXHR,textStatus,errorThrown){
			button.removeAttr('disabled');
			alert('transmission error');
			console.log(errorThrown);
		}
	});
});

});