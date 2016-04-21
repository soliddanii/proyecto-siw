$(document).ready(function(){

	USER = {
	
		eval: function(user){
			
			var regex_user = /^[0-9a-zA-ZáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ]((\.|_|-)?[0-9a-zA-ZáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ]+){3,20}$/;			
			// Permite numeros, letras latinas minuscula y mayuscula. 
			// Y si hay un guión o punto pide una letra o numero. 
			// No acepta caracteres raros ni espacios.
			return regex_user.test(user);
		},

		error: function(){
			return "Nombre de usuario no válido";
		}
	}

	PASSWORD = {

		eval: function(pwd){

			var regex_pwd = /^([a-z]+[0-9]+)|([0-9]+[a-z]+)/i;
			// Debe contener numeros y letras

			return regex_pwd.test(pwd); 

		},

		error: function(){

			return "Contraseña invalida";

		}

	}

	EMAIL = {

		eval: function(email){

			var regex_email = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
			
			return regex_email.test(email);		

		},

		error: function(){

			return "Email no válido";

		}

	}

	NAME = {

		eval: function(name){

			var regex_name_last = /^([a-z ñáéíóú]{2,60})$/i;

			return regex_name_last.test(name);

		},

		error: function(){

			return "Nombre no válido";

		}

	}

});