var emailCheck = $('<strong>');
var codeCheck = $('<strong>');
var left_times = 2;
$('p:last').append(codeCheck);
$('p:first').append(emailCheck);

$(document).on('ready',function(){
	$('#email').on('focusout',function(){ 
		var email = $('#email').val();
		var reg = /^[\w\-.]+@[a-zA-Z0-9][a-zA-Z0-9\-]*\.[a-zA-Z0-9][a-zA-Z0-9\-.]*$/
		if(!reg.test(email)){
			$('#email').val('');
			emailCheck.text('invalid email address, change to another');
		}else{
			emailCheck.text('');
			$.ajax('../server/server.php',{
				method:'POST',
				data:{
					'type':'email_query',
					'data':email
				},
				timeout:10000,
				success:function(data,textStatus,jqXHR){ 
					if(data.success){
						if(data.pass){
							emailCheck.text('email OK');
						}else{
							$('#email').val('');
							emailCheck.text('the email is registered, change to another');
						}
					}else{
						$('#email').val('');
						alert('failed to query the existence of email,please reload the page and try again');
						console.log(data.error);
					}
				},
				error:function(jqXHR,textStatus,errorThrown){
					$('#email').val('');
					alert('failed to query the existence of email,please reload the page and try again');
					console.log(errorThrown);
				}
			});
		}
	});

	
	$('#send_button').on('click',function(){
		if(!$('#email').val()){
			alert('you have not input the email');
		}else{
			$('#send_button').attr('disabled','disabled');
			alert('you have pressed send button. please wait 1 minute for another verification code if you need');
			setTimeout(function(){ 
				$('#send_button').removeAttr('disabled');
				codeCheck.text('');
			},60000);
		
			$.ajax('../server/generate_verification_code.php',{
				method:'POST',
				data:{
					'email':$('#email').val()
				},
				timeout:20000,
				success:function(data,textStatus,jqXHR){ 
					if(data.success){
						if(data.pass){
							alert('verification code generated');
							codeCheck.text('verification code generated');
							$('#submit_button').removeAttr('disabled');
						}else{
							codeCheck.text('verification code generation failed');
						}
					}else{
						codeCheck.text('verification code generation failed');
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

	$('form').submit(function(event){
		event.preventDefault();
		if(!$('#email').val() || !$('#verification_code').val()){
			alert('you have not filled in the form completely');
		}else{
			$.ajax('../server/verify_verification_code.php',{
				method:'POST',
				data:{
					'email':$('#email').val(),
					'code':$('#verification_code').val()
				},
				timeout:20000,
				success:function(data){
					if(data.success){
						if(!data.pass){
							if(left_times > 0){
								alert('wrong verification code,you can still try '+(left_times--)+' times');
							}else{
								/*$.ajax('../server/server.php',{ //失败次数太多，命令服务器销毁session
									method:'POST',
									data:{
										'type':'session_destroy',
										'data':'none'
									},
									success:function(data){
										if(data.success && data.pass){
											alert('success');
										}else{
											alert('failed');
										}
										$(location).attr('href', 'register_p1.php'); //重定向至注册页面p1
										alert('verification failed');
									},
									error:function(){
										$(location).attr('href', 'register_p1.php'); //重定向至注册页面p1
										alert('verification failed');
										alert('error');
									}
								});*/
								//服务器已实现此功能，不用前端进行
								$(location).attr('href', 'register_p1.php'); //重定向至注册页面p1
								alert('verification failed');
							}
						}else{
							$(location).attr('href','../server/register_process_p2.php');
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
	
	$('#email').removeAttr('disabled');
	$('#verification_code').removeAttr('disabled');
	$('#send_button').removeAttr('disabled');
});