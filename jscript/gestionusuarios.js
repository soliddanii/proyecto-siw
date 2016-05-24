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
  *
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

});