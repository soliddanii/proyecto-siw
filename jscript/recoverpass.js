$(document).ready(function(){

	$('#recoverpass').submit(function(event){

		var user  = $('input[name=nameuser]').val();
		var email = $('input[name=email]').val();

		if(!USER.eval(user)){
			console.log(USER.error());	
			event.preventDefault();
		}

		if(!EMAIL.eval(email)){
			console.log(EMAIL.error());	
			event.preventDefault();
		}

	});

});