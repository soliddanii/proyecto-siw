$(document).ready(function(){

	$('select[name=order]').change(function(){
		
		var orderby = $('select[name=order]').val();
		var currentPage = $('#currentPage').val();					

		if (orderby != 0){		

			$('#orderby').val(orderby);

			$.get('../php/controller.php',{
				cmd: 'adminCmd',
				id: 2,
				order: true,
				orderby: orderby,
				currentPage: currentPage
			},function(response){
				$('.list').empty().append(response);			
			});
		}
		
	});

	

	$('.page').click(function(event){
		
		var nextPage = event.target.id;
		var currentPage = parseInt($('#currentPage').val());
		var maxPage = parseInt($('#maxPage').val());
		var orderby = $('#orderby').val();


		if (nextPage == 'Back')
			var next = currentPage - 1;
		else if (nextPage == 'Next')
			var next = currentPage + 1;
		else
			var next = nextPage;

		if ((next != currentPage) &&(next > 0) && (next <= maxPage)){
			$('#currentPage').val(next);
			$.get('../php/controller.php',{
				cmd: 'adminCmd',
				id: 2,
				order: true,
				orderby: orderby,
				nextPage: next
			},function(response){
				$('.list').empty().append(response);			
			});				
		}		 	
		
	});


	/* Gestion de eliminar un usuario*/

	var u = 0;

	$('.delete').click(function(event){
		u = event.target.id;		
		$('#dialog').dialog("open");
	});
	
	$( "#dialog" ).dialog({
  	autoOpen: false,
  	resizable: false,
  	height:200,
  	modal: true,
  	buttons: {
	    "Si": function() {
	      $( this ).dialog( "close" );
	      var link = "../php/controller.php?cmd=adminCmd&id=4&u="+u;
	      window.location = link;
	    },
	    "No": function() {
	      $( this ).dialog( "close" );
	    }
  	}
  });

  /*
  *	Gestión de modificar un usuario
  */

  $('#modifyForm').submit(function(event){

  	var name  = $('input[name=name]').val();
  	var email = $('input[name=email]').val();

  	if (name.length == 0 && email.length == 0){
  		event.preventDefault();
  	}else{
  		if (name.length != 0 && !NAME.eval(name)){
  			event.preventDefault();
  		}

  		if (email.length != 0 && !EMAIL.eval(email)){
  			event.preventDefault();
  		}
  		
  	}

  });

  $('#cancel').click(function(){  	
  	window.history.back();
  });

  $('.back').click(function(){
  	window.history.back();  	
  });

  /*$('select[name=ordenar]').change(function(){

  	var order = $('select[name=ordenar]').val();  
  	var state = $('#state').val();
  	
  	if (order == 'new')
  		var orderby = 1;
  	else if (order == 'old')
  		var orderby = 2;
  	else if (order == 'cheap')
  		var orderby = 3;
  	else if (order == 'expensive')
  		var orderby = 4;

  	if (state == 'Activos'){
  		$.get('../php/controller.php',{
				cmd: 'adminView',
				id: 5,
				state: 1,
				order: orderby
			},function(response){				
				$('.anuncios').empty().append(response);
			});
  	}else if(state = 'Cancelados'){
  		$.get('../php/controller.php',{
				cmd: 'adminView',
				id: 5,
				state: 0,
				order: orderby
			},function(response){
				$('.anuncios').empty().append(response);
			});
  	}

  });*/


});