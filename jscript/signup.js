$(document).ready(function(){

	$('#signup').submit(function(event){

		var user  = $('#nameuser').val();
		var name  = $('#name').val();
		var email = $('#email').val();
		var pwd0  = $('#passwd0').val();
		var pwd1  = $('#passwd1').val();

		if(!USER.eval(user)){
			console.log(USER.error());
            $('.jsError1').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError1'>Nombre de Usuario no válido. Alfanúmerico entre 4 y 20.</div>");
			event.preventDefault();
		}else{
            $('.jsError1').remove();
        }

        if(name != ''){
            if(!NAME.eval(name)){
                console.log(NAME.error());
                $('.jsError2').remove();
                $(".contenido").append("<div id = 'errorMessage' class = 'jsError2'>Nombre no permitido.</div>");
                event.preventDefault();
            }else{
                $('.jsError2').remove();
            }
        }
		
		if(!EMAIL.eval(email)){
			console.log(EMAIL.error());
            $('.jsError3').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError3'>El email introducido no es válido.</div>");
			event.preventDefault();
		}else{
            $('.jsError3').remove();
        }

		if(pwd0 == pwd1){
			
            $('.jsError5').remove();
            
			if(!PASSWORD.eval(pwd0)){
				console.log(PASSWORD.error());	
                $('.jsError4').remove();
                $(".contenido").append("<div id = 'errorMessage' class = 'jsError4'>Contraseña Inválida. Debe contener números y letras.</div>");
				event.preventDefault();
			}else{
                $('.jsError4').remove();
            }

		}else{
			console.log("Las contraseñas no coinciden");
            $('.jsError5').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError5'>Las contraseñas no coinciden.</div>");
			event.preventDefault();
		}
		
	});


});