$(document).ready(function(){

	$('#recoverpass').submit(function(event){

		var user  = $('#nameuser').val();
		var pwd0 = $('#newpass').val();

		if(!USER.eval(user)){
			console.log(USER.error());
            $('.jsError1').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError1'>Nombre de Usuario no válido. Alfanumerico entre 4 y 20.</div>");
			event.preventDefault();
		}else{
            $('.jsError1').remove();
        }

		if(!PASSWORD.eval(pwd0)){
			console.log(PASSWORD.error());
            $('.jsError2').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError2'>Contraseña Inválida. Debe contener números y letras.</div>");
			event.preventDefault();
		}else{
            $('.jsError2').remove();
        }

	});

});