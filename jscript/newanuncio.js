$(document).ready(function(){
        
    Dropzone.options.myDropzone = {
            
        url: "../php/controller.php?cmd=userCmd&id=5",
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 100,
        maxFiles: 100,
        maxFilesize: 10, //10MB
        addRemoveLinks: true,
        acceptedFiles: ".png,.jpg,.gif,.jpeg",
        dictDefaultMessage: "Arrastra aquí las imágenes",

        init: function () {

            var submitButton = document.querySelector("#submit-all");
            var wrapperThis = this;

            submitButton.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (wrapperThis.getQueuedFiles().length > 0) {                        
                    wrapperThis.processQueue();  
                } else {                       
                    $('#full-form').submit(); //send the normal form
                }                                    
            });

            this.on('sendingmultiple', function (data, xhr, formData) {
                formData.append("titulo", $("#titulo").val());
                formData.append("localizacion", $("#localizacion").val());
                formData.append("telefono", $("#telefono").val());
                formData.append("precio", $("#precio").val());
                formData.append("categoria", $("#categoria").val());
                formData.append("descripcion", $("#descripcion").val());
            });
            
            this.on("success", function(file, response) {
                //LA RESPUESTA DE PHP A ESTE FORM NO PUEDE
                //SER COMO LAS DEMAS (UNA PAGINA ENTERA)
                
                //maldito dropzone
                location.reload();
                
                /*console.log(response);
                
                if(response.startsWith('redirect')){
                    //Redirigir al nuevo anuncio
                    $('#redirectIdAnuncio').val(response.substring(9))
                    $('#redirectToAnuncio').submit();
                    
                }else{
                    //Mantener esta pagina y cargar errores
                    
                }*/

            });
        }
        
    };  
});

//http://www.howwaydo.com/integrating-dropzone-js-into-existing-html-form-with-other-fields/