$(document).ready(function(){

	$('#priceMin').bind('keyup mouseup', function () {
		
		var min = parseInt($('#priceMin').val());
		var max = parseInt($('#priceMax').val());

		if(min > max) {
			$('#priceMin').val(max);
		}	
		
	});
    
    
    $('#priceMax').focusout(function () {
		
		var min = parseInt($('#priceMin').val());
		var max = parseInt($('#priceMax').val());

		if(max < min) {
			$('#priceMax').val(min);
		}	
		
	});


});