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
			emailCheck.text('invalid email');
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
						if(!data.pass){
							emailCheck.text('email OK');
						}else{
							$('#email').val('');
							emailCheck.text('invalid email');
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
			$.ajax('../server/password_recovery_process_p2.php',{
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
								$(location).attr('href', 'password_recovery_p1.php'); //????????????????????????p1
								alert('verification failed');
							}
						}else{
							$(location).attr('href', 'password_recovery_p3.php');
							alert('verification success, you can set your new password now');
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