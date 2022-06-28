$(document).on('ready',function(event){
	$('form').submit(function(event){
		event.preventDefault();
		$.ajax('../server/login_process.php',{
			method:'POST',
			data:{
				'username':$('#username').val(),
				'passwd':$('#passwd').val()
			},
			timeout:10000,
			success:function(data,textStatus,jqXHR){
				if(data.success){
					if(data.pass){
						$(location).attr('href','homepage.php');
					}else{
						$('#username').val('');
						$('#passwd').val('');
						alert('verification failed');
					}
				}else{
					alert('something went wrong');
					console.log(data.error);
				}
			},
			error:function(jqXHR,textStatus,errorThrown){
				alert('transmisson error, try again later');
				console.log(errorThrown);
			}
		});
	});
	
	$('#username').removeAttr('disabled');
	$('#passwd').removeAttr('disabled');
	$('button').removeAttr('disabled');
});