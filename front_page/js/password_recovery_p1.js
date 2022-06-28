var nameCheck = $('<strong>');
$('p').append(nameCheck);
$(document).on('ready',function(){
	$('#username').on('focusout',function(){
		if(!$('#username').val()){
			nameCheck.text('username not empty');
		}else{
			$.ajax('../server/server.php',{
				method:'POST',
				data:{
					'type':'username_query',
					'data':$('#username').val()
				},
				timeout:10000,
				success:function(data,textStatus,jqXHR){
					if(data.success){
						if(!data.pass){
							nameCheck.text('username OK');
						}else{
							nameCheck.text('no such user');
							$('#username').val('');
						}
					}else{
						alert('failed to query the existence of username,please try again later');
						console.log(data.error);
					}
				},
				error:function(jqXHR,textStatus,errorThrown){
					alert('failed to query the existence of username,please try again later');
					console.log(errorThrown);
				}
			});
		}
	});	
	
	$('form').submit(function(event){
		event.preventDefault();
		if(!$('#username').val()){
			alert('you have not fill in the form completely');
		}else{
			$.ajax('../server/password_recovery_process_p1.php',{
				method:'POST',
				'data':{
					'username':$('#username').val()
				},
				timeout:10000,
				success:function(data,textStatus,jqXHR){ 
					if(data.success){
						if(data.pass){
							$('button').attr('disabled','disabled');
							$(location).attr('href','password_recovery_p2.php');
						}else{
							alert('you have not fill in the form correctly');
						}
					}else{
						alert('something went wrong, please register again later');
						console.log(data.error);
					}
				},
				error:function(jqXHR,textStatus,errorThrown){
					alert('transmission error, please register again later');
					console.log(errorThrown);
				}
			});
		}
	});
	
	$('#username').removeAttr('disabled');
});