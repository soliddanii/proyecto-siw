$(document).ready(function(){
	
	$('#loginadmin').submit(function(event){
		
		var user = $('#usuario').val();
		var pass = $('#password').val();

		if(USER.eval(user)){
			if(!PASSWORD.eval(pass)){
				event.preventDefault();
				$('#error').empty().append(PASSWORD.error()).css({"color":"red"});
			}
		}else{
			event.preventDefault();
			$('#error').empty().append(USER.error()).css({"color":"red"});
		}
	});
	
});