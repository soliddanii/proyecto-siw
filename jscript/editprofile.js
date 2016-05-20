$(document).ready(function(){

	$('#editprofile').submit(function(event){

		var name  = $('#name').val();
		var email = $('#email').val();
		var pwd0  = $('#passwd0').val();
		var pwd1  = $('#passwd1').val();

		if(name.length == 0 && email.length == 0 
			&& pwd0.length == 0 && pwd1.length == 0){
			console.log("No hay datos para proceder");
            $('.jsError1').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError1'>No hay datos para proceder</div>");
			event.preventDefault();
		}else{
            $('.jsError1').remove();
        }

		if(name.length > 0 && !USER.eval(name)) {
			console.log(USER.error());
            $('.jsError2').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError2'>Nombre de Usuario no válido. Alfanúmerico entre 4 y 20.</div>");
			event.preventDefault();
		}else{
            $('.jsError2').remove();
        }

		if(email.length > 0 && !EMAIL.eval(email)) {
			console.log(EMAIL.error());
            $('.jsError3').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError3'>El email introducido no es válido.</div>");
			event.preventDefault();
		}else{
            $('.jsError3').remove();
        }

		if((pwd0.length > 0 && pwd1.length == 0) 
			|| (pwd0.length == 0 && pwd1.length > 0)){
			console.log("Campos de contraseña incompletos");
            $('.jsError4').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError4'>Campos de contraseña incompletos.</div>");
			event.preventDefault();
		}else{
            $('.jsError4').remove();
        }

		if(pwd0.length > 0 && pwd1.length > 0) {					
			if(!PASSWORD.eval(pwd0)){
				console.log(PASSWORD.error());
                $('.jsError5').remove();
                $(".contenido").append("<div id = 'errorMessage' class = 'jsError5'>Contraseña vieja inválida.</div>");
				event.preventDefault();
			}else{
                $('.jsError5').remove();
            }
            
            if(!PASSWORD.eval(pwd1)){
				console.log(PASSWORD.error());
                $('.jsError6').remove();
                $(".contenido").append("<div id = 'errorMessage' class = 'jsError6'>Contraseña nueva inválida.</div>");
				event.preventDefault();
			}else{
                $('.jsError6').remove();
            }
		}			

	});

});