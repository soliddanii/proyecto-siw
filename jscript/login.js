$(document).ready(function(){

	$('#login').submit(function(event){
		
		var user = $('#name').val();
		var pwd  = $('#passwd0').val();

		if(!USER.eval(user)) {
			console.log(USER.error());
            $('.jsError1').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError1'>Nombre de Usuario Inválido. Alfanúmerico entre 4 y 20.</div>");
			event.preventDefault();
		}else{
            $('.jsError1').remove();
        }

		if(!PASSWORD.eval(pwd)) {
			console.log(PASSWORD.error());
            $('.jsError2').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError2'>Contraseña Inválida. Debe contener números y letras.</div>");
			event.preventDefault();
		}else{
            $('.jsError2').remove();
        }		
		
	});


});