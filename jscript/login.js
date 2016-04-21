$(document).ready(function(){

	$('#login').submit(function(event){
		
		var user = $('input[name=name]').val();
		var pwd  = $('input[name=passwd0]').val();

		if(!USER.eval(user)) {
			console.log(USER.error());
			event.preventDefault();
		}

		if(!PASSWORD.eval(pwd)) {
			console.log(PASSWORD.error());
			event.preventDefault();
		}		
		
	});


});