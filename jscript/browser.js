$(document).ready(function(){

    //***************************************************************
    //* Mantener el precio minimo por debajo del maximo y viceversa *
    //***************************************************************
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
    
    
    //**********************************************************
    //*********** ENVIAR PETICIONES AJAX AL SERVIDOR ***********
    //**********************************************************
    $('#submitBuscar').click(function(event){
        sendAjaxRequest();
    });
    
    $('#orden').change(function(event){
        sendAjaxRequest();
    });
    
    $('#paginaSiguiente').click(function(event){
        pagina = parseInt($('#paginaActual').val());
        $('#paginaActual').val(pagina+1);
        sendAjaxRequest();
    });
    
    $('#paginaAnterior').click(function(event){
        pagina = parseInt($('#paginaActual').val());
        if(pagina > 1){
            pagina = pagina-1;
        }else{
            e.preventDefault();
        }

        $('#paginaActual').val(pagina);
        sendAjaxRequest();
    });
    
    
    function sendAjaxRequest(){
     
        //Recoger los datos y estructurarlos para mandarlos
        palabra = $('#titulo').val();
        localizacion = $('#localizacion').val();
        categoria = $('select[name=categoria]').val()
        priceMin = $('#priceMin').val();
        priceMax = $('#priceMax').val();
        orden = $('#orden').val();
        misAnuncios = $('#misAnuncios').val();
        misFavoritos = $('#misFavoritos').val();
        pagina = $('#paginaActual').val();

        parameters = {"titulo" : palabra, "localizacion" : localizacion, 
            "categoria" : categoria, "priceMin" : priceMin, "priceMax" : priceMax, 'orden' : orden};
            
        urlServer = "../php/controller.php?cmd=userView&id=8";
        if(misAnuncios == '1'){
            urlServer = urlServer + "&user=1";
        }
        if(misFavoritos == '1'){
            urlServer = urlServer + "&favoritos=1";
        }
        
        urlServer = urlServer + "&pagina="+pagina;
    
        //Enviar la peticion en POST
        $.ajax({
            data: parameters,
            type: "POST",
            dataType: "json",
            url: urlServer,
        })
        .done(function( data, textStatus, jqXHR ) {
            if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
                
                //Vaciar la lista actual
                $(".errMsg").remove();

                //Añadir los errores si hay
                $.each(data.errors, function(index, itemData) {
                    $( "#ordenAnuncios" ).after( ' <div class = "errMsg" id = "errorMessage">'+itemData.errorCode+': '+itemData.message+'</div>' );
                });
               
               htmlN = '';
                //Rellenar la lista con los nuevos datos
                $.each(data.data, function(index, itemData) {
                    precio = itemData.precio;
                    if(precio == '0.00'){
                        precio = 'GRATIS';
                    }
                    aa = '';
                    bb = '';
                    if(itemData.esMio == true){
                        aa = ' esMiAnuncio'
                    }
                    
                    if(parseInt(itemData.estado) == 2){
                        bb = ' terminado'
                    }else if(parseInt(itemData.estado) == 0){
                        bb = ' cancelado';
                    }
                
                    htmlN = htmlN + '<li><form id="anuncioForm'+itemData.id
                    +'" action="../php/controller.php?cmd=userView&id=7" method="POST"><input type="hidden" name="idAnuncio" value="'+itemData.id
                    +'"/><div class = "bloqueAnuncio'+aa+bb+'" onClick="document.getElementById(\'anuncioForm'+itemData.id
                    +'\').submit();"><div class = "Aleft"><img class = "miniaturaAnuncio" src="'+itemData.miniatura
                    +'" height="160px" alt="miniatura"/></div><div class = "Aright"><div class = "Atop"><span class = "tituloAnuncio">'+itemData.titulo
                    +'</span><span class = "precioAnuncio">'+precio
                    +'</span></div><div class = "Abottom"><span class = "localizacionAnuncio">Localización: '+itemData.localizacion
                    +'</span><span class = "fechaAnuncio">'+itemData.fecha+'</span></div></div></div></form></li>';
                });
                
                
                //Actualizar el numero de pagina
                $('#paginaActual').val(data.page);
                
                $('#listaAnunciosUL').fadeToggle(500, function() {
                    $('#listaAnunciosUL').html(htmlN);
                    $('#listaAnunciosUL').fadeToggle(500);
                });
                
                
            }
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            if ( console && console.log ) {
                console.log( "La solicitud a fallado: " +  textStatus);
                console.log(jqXHR);
                $(".errMsg").remove();
                $( "#ordenAnuncios" ).after( ' <div class = "errMsg" id = "errorMessage">La solicitud AJAX ha fallado</div>' );
            }
        });
    }


});