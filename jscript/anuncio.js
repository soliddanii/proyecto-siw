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
    
    
});