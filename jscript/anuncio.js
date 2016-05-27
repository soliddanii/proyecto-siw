$(document).ready(function(){

    //**********************************************************
    //********************** GALERIA ***************************
    //**********************************************************

    //Cambiar la imagen mediana segun cual pequeña se pinche
    $('.imagenEnana').click(function() {
        urlM = $(this).data('medium');
        urlB = $(this).data('big');
        $('#imagenMediana').attr('src', urlM);
        $('#imagenMediana').data('big', urlB);
        
        actual = $(this).attr('id');
        $('#imagenGrande').data('actual', actual);
    });
    
    //Cambiar la imagen grande cuando se pinche la mediana
    $('#imagenMediana').click(function() {
    
        urlB = $(this).data('big');
        $('#imagenGrande').attr('src', urlB);
        $(".lightbox").fadeIn(1000);

    });
    
    //Cerrar la imagen grande cuando se pincha
    $('#imagenGrande').click(function() {
        $(".lightbox").fadeOut(800);
    });
    
    $('#case-right').click(function() {
        max = $('#imagenGrande').data('max');
        actual = $('#imagenGrande').data('actual');
        actual = parseInt(actual);
        if(actual == (max-1)){
            actual = 0;
        }else{
            actual = actual + 1;
        }
        
        urlB = $('#'+actual).data('big');
        $('#imagenGrande').data('actual', actual);
        $('#imagenGrande').attr('src', urlB);
        
    });
    
    $('#case-left').click(function() {
        max = $('#imagenGrande').data('max');
        actual = $('#imagenGrande').data('actual');
        actual = parseInt(actual);
        if(actual == 0){
            actual = (max-1);
        }else{
            actual = actual - 1;
        }

        urlB = $('#'+actual).data('big');
        $('#imagenGrande').data('actual', actual);
        $('#imagenGrande').attr('src', urlB);
    });
    
    
    //**********************************************************
    //********** Caracteres restantes en comentario ************
    //**********************************************************
    
    var maxchar = 400;
    var i = document.getElementById("nuevoComentario");
    var c = document.getElementById("counter");
    c.innerHTML = maxchar;
    
    i.addEventListener("keydown",count);
    
    function count(e){
        var len =  i.value.length;
        if (len >= maxchar){
            e.preventDefault();
        } else{
            c.innerHTML = maxchar - len - 1;   
        }
    }

    
    //**********************************************************
    //*************** Click en el boton responder **************
    //**********************************************************
    $('.linkRespuesta').click(function() {  
        
        id = $(this).data('id');
        $('#idComentarioRespuesta').val(parseInt(id));
        $('#nuevoComentario').attr("placeholder", "Escribir una respuesta a "+$(this).data('nick')+"...");
        $('html, body').animate({ scrollTop: $('#nuevoComentario').offset().top }, 'slow');
        $('#nuevoComentario').focus();
        $('#cancelResponse').css('display', 'inline-block');

    });
    
    $('#cancelResponse').click(function(){
    
        $('#cancelResponse').css('display', 'none');
        $('#idComentarioRespuesta').val(-1);
        $('#nuevoComentario').attr("placeholder", "Escribir un comentario...");

    });
    
    $(document).on("click", "a", function(){
        if($(this).attr('href').startsWith('#coment')){
            $($(this).attr('href')).fadeToggle(700);
            $($(this).attr('href')).fadeToggle(700);
        }
    });

    
    //**********************************************************
    //*************** Prevenir un comentario vacio *************
    //**********************************************************
    $('#newcomentario').submit(function(event){
    
        if($('#nuevoComentario').val() == ''){
            $('.jsError1').remove();
            $(".contenido").append("<div id = 'errorMessage' class = 'jsError1'>No puede publicar un comentario vacio</div>");
            $('html, body').animate({ scrollTop: $('.jsError1').offset().top }, 'slow');
			event.preventDefault();
        }else{
            $('.jsError1').remove();
        }
            
    
    });
    
    
    //**********************************************************
    //*************** Click en el boton comprar ****************
    //**********************************************************
    $('#buyCancelProduct').submit(function(event){
    
        estado = parseInt($('#buyCancelProductHidden').data('estado'));
        if(estado == 0){
            $('.jsError1').remove();
            $(".contenido").prepend("<div id = 'errorMessage' class = 'jsError1'>No puede acceder a un anuncio cancelado</div>");
            event.preventDefault();
        }
        else if(estado == 2){
            $('.jsError1').remove();
            $(".contenido").prepend("<div id = 'errorMessage' class = 'jsError1'>No puede acceder a un anuncio vendido</div>");
            event.preventDefault();
        }
    
    });
    
    
});