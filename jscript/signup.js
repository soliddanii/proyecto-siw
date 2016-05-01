$(document).ready(function(){

	$('#signup').submit(function(event){

		var user  = $('input[name=nameuser]').val();
		var name  = $('input[name=name]').val();
		var email = $('input[name=email]').val();
		var pwd0  = $('input[name=passwd0]').val();
		var pwd1  = $('input[name=passwd1]').val();

		if(!USER.eval(user)){
			console.log(USER.error());	
			event.preventDefault();
		}

		if(!NAME.eval(name)){
			console.log(NAME.error());	
			event.preventDefault();
		}

		if(!EMAIL.eval(email)){
			console.log(EMAIL.error());	
			event.preventDefault();
		}

		if(pwd0 == pwd1){
			
			if(!PASSWORD.eval(pwd0)){
				console.log(PASSWORD.error());	
				event.preventDefault();
			}

		}else{
			console.log("Las contraseñas no coinciden");	
			event.preventDefault();
		}
		
	});


});