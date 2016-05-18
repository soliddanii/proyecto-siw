$(document).ready(function(){

	$('#editprofile').submit(function(event){

		var name  = $('input[name=name]').val();
		var email = $('input[name=email]').val();
		var pwd0  = $('input[name=passwd0]').val();
		var pwd1  = $('input[name=passwd1]').val();

		if(name.length == 0 && email.length == 0 
			&& pwd0.length == 0 && pwd1.length == 0){
			console.log("No hay datos para proceder");
			event.preventDefault();
		}		

		if(!USER.eval(name) && name.length > 0) {
			console.log(USER.error());
			event.preventDefault();
		}

		if(!EMAIL.eval(email) && email.length > 0) {
			console.log(EMAIL.error());
			event.preventDefault();
		}

		if((pwd0.length > 0 && pwd1.length == 0) 
			|| (pwd0.length == 0 && pwd1.length > 0)){
			console.log("Campos de contraseña incompletos");
			event.preventDefault();
		}

		if(pwd0.length > 0 && pwd1.length > 0) {					
			if(!PASSWORD.eval(pwd0)){
				console.log(PASSWORD.error());	
				event.preventDefault();
			}			 	
		}			

	});

});