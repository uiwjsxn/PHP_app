function zero(value){
	if(value < 10){
		value = '0'+value;
	}
	return value;
}

function timeStampToDate_10bit(time_str){
	var date = new Date(time_str*1000);
	var year = date.getFullYear();
	var month = date.getMonth()+1;
	var day = date.getDate();
	var hour = date.getHours();
	var minute = date.getMinutes();
	var second = date.getSeconds();
	var res = year+'-'+zero(month)+'-'+zero(day)+'	'+zero(hour)+':'+zero(minute)+':'+zero(second);
	return res;
}

function timeStampToDate_13bit(time_str){
	var date = new Date(time_str);
	var year = date.getFullYear();
	var month = date.getMonth()+1;
	var day = date.getDate();
	var hour = date.getHours();
	var minute = date.getMinutes();
	var second = date.getSeconds();
	var res = year+'-'+zero(month)+'-'+zero(day)+'	'+zero(hour)+':'+zero(minute)+':'+zero(second);
	return res;
}

var notRead = new Array();
var notReadTime = new Array();
var last_search = 0;
var first_query = true;
var first_set = true;
var old_last_query_ = 0;

$(document).on('ready',function(event){
	
	$('button').on('click',function(event){
		if(!$('#chatMessage').val()){
			alert('you have not enter any message');
		}else{
			var message = $('#chatMessage').val()
			$.ajax('server/chat_pair_process.php',{
				method:'POST',
				data:{
					'type':'send',
					'message':message
				},
				timeout:10000,
				success:function(data,textStatus,jqXHR){ 
					if(data.success){
						var time = new Date().getTime();
						var tag = $('<strong>').text(' (Not Read) ');
						notRead.push(tag);
						notReadTime.push(time/1000);
						var div = $('<div class="right">'+timeStampToDate_13bit(time)+'</div>');
						$('#chatPanel').append(div);
						$('#chatPanel').append($('<div class="row bubble-sent pull-right">'+
						message+'</div><div class="clearfix"></div>'));
						div.append(tag);
					}
					else{
						alert('failed to send message, try again later');
					}
				},
				error:function(jqXHR,textStatus,errorThrown){
					alert('transmission error');
					console.log(errorThrown);
				}
			});
		}
	});
	
	var rec = function(){
		$.ajax('server/chat_pair_process.php',{
			method:'POST',
			data:{
				'type':'receive'
			},
			timeout:5000,
			success:function(data,textStatus,jqXHR){ 
				if(data.success){
					if(first_query){
						old_last_query_ = data.last_query_;
						first_query = false;
					}else if(first_set && old_last_query_ < data.last_query_){ //the other has open the chat window
						$('.notRead').text(' (Read) '); //set the original 'Not Read' message from php server to 'Read'
						first_set = false;
					}
					while(last_search < notReadTime.length){
						if(notReadTime[last_search] < data.last_query_){
							notRead[last_search++].text(' (Read) ');
						}else break;
					}
					$.each(data.message,function(key,value_){
						$('#chatPanel').append($('<div class="clearfix">'+timeStampToDate_10bit(value_['date_created'])+'</div><div class="row bubble-recv">'+
						value_['message']+'</div><div class="clearfix"></div>'));
						
					});
				}
				else{
					alert('failed to receive message, trying again... '+data.error);
				}
			},
			error:function(jqXHR,textStatus,errorThrown){
				alert('transmission error');
				console.log(errorThrown);
			}
		});
		setTimeout(rec,5000);
	}
	setTimeout(rec,3000);
});
