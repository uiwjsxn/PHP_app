var nameCheck = $('<strong>');
var passwordCheck = $('<strong>');
var password2Check = $('<strong>');

$(document).on('ready',function(){
	$('#username').on('focusout',function(event){
		var str = $('#username').val();
		if(str){
			$.ajax('../server/server.php',{
				method:'POST',
				data:{
					'type':'username_query',
					'data':str
				},
				timeout:10000,
				success:function(data,textStatus,jqXHR){
					if(data.success){
						if(data.pass){
							nameCheck.text('username OK');
							$('#p1').append(nameCheck);
						}else{
							nameCheck.text('username exists,change to another name');
							$('#p1').append(nameCheck);
							$('#username').val('');
						}
					}else{
						$('#username').val('');
						alert('failed to query the existence of username,please try again later');
						console.log(data.error);
					}
				},
				error:function(jqXHR,textStatus,errorThrown){
					$('#username').val('');
					alert('failed to query the existence of username,please try again later\n' + errorThrown);
					console.log(errorThrown);
				}
			});
		}
	});

	$('#passwd').on('focusout',function(event){
		$('#passwd2').val(''); //只要passwd1修改了，passwd2就清空
		password2Check.text('');
		var password = $('#passwd').val();
		var rex1 = /\d/;
		var rex2 = /[a-z]/;
		var rex3 = /[A-Z]/;
		var rex4 = /[^\w|_]/;
		var res = false;
		if(password.length < 8){
			passwordCheck.text('password length is at least 8');
		}else if(!rex1.test(password)){
			passwordCheck.text('password must contain numbers');
		}else if(!rex2.test(password)){
			passwordCheck.text('password must contain lower case characters');
		}else if(!rex3.test(password)){
			passwordCheck.text('password must contain upper case characters');
		}else if(!rex4.test(password)){
			passwordCheck.text('password must special characters,like $,%,#,@,^,etc.');
		}else{
			res = true;
			passwordCheck.text('password OK');
			$('#p2').append(passwordCheck);
		}
	
		if(!res){
			$('#passwd').val('');
			$('#p2').append(passwordCheck);
		}
	});

	$('#passwd2').on('focusout',function(event){
		var passwd1 = $('#passwd').val();
		var passwd2 = $('#passwd2').val();
		if(!passwd1 || passwd1 != passwd2){
			password2Check.text('two password is not match');
			$('#passwd2').val('');
		}else{
			password2Check.text('password2 OK');
		}
		$('#p3').append(password2Check);
	});

	$('form').submit(function(event){
		event.preventDefault();
		if(!$('#passwd').val() || !$('#passwd2').val() || !$('#username').val()){
			alert('you have not fill in the form completely');
		}else{
			$.ajax('../server/register_process_p1.php',{
				method:'POST',
				'data':{
					'username':$('#username').val(),
					'passwd':$('#passwd').val(),
					'passwd2':$('#passwd2').val()
				},
				timeout:5000,
				success:function(data,textStatus,jqXHR){ 
					if(data.success){
						if(data.pass){
							$('button').attr('disabled','disabled');
							$(location).attr('href','register_p2.php');
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
	//在html中初始时这些input和button都是disabled，等待页面载入完成和上面的事件触发器配置完成后才启用这些input，确保jQuery中的限定条件得到执行
	$('#username').removeAttr('disabled');
	$('#passwd').removeAttr('disabled');
	$('#passwd2').removeAttr('disabled');
	$('button').removeAttr('disabled');
});