var nameCheck = $('<strong>');
var passwordCheck = $('<strong>');
var password2Check = $('<strong>');

$(document).on('ready',function(){
	$('#new_passwd').on('focusout',function(event){
		$('#new_passwd2').val(''); //只要passwd1修改了，passwd2就清空
		password2Check.text('');
		var password = $('#new_passwd').val();
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
			$('p:first').append(passwordCheck);
		}
	
		if(!res){
			$('#new_passwd').val('');
			$('p:first').append(passwordCheck);
		}
	});

	$('#new_passwd2').on('focusout',function(event){
		var passwd1 = $('#new_passwd').val();
		var passwd2 = $('#new_passwd2').val();
		if(!passwd1 || passwd1 != passwd2){
			password2Check.text('two password is not match');
			$('#new_passwd2').val('');
		}else{
			password2Check.text('password2 OK');
		}
		$('p:last').append(password2Check);
	});

	$('form').submit(function(event){
		if(!$('#new_passwd').val() || !$('#new_passwd2').val() ){
			alert('you have not fill in the form completely');
			event.preventDefault();
		}
	});
	//在html中初始时这些input和button都是disabled，等待页面载入完成和上面的事件触发器配置完成后才启用这些input，确保jQuery中的限定条件得到执行
	$('#new_passwd').removeAttr('disabled');
	$('#new_passwd2').removeAttr('disabled');
	$('button').removeAttr('disabled');
});