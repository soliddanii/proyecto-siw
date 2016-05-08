$(document).ready(function(){
        
    Dropzone.options.myDropzone = {
            
        url: "../php/controller.php?cmd=userCmd&id=5",
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 100,
        maxFiles: 100,
        maxFilesize: 10, //10MB
        //addRemoveLinks: true,
        acceptedFiles: ".png,.jpg,.gif,.jpeg",
        dictDefaultMessage: "Arrastra aquí las imágenes",

        init: function () {

            var submitButton = document.querySelector("#submit-all");
            var wrapperThis = this;

            submitButton.addEventListener("click", function () {
                wrapperThis.processQueue();
            });

            this.on("addedfile", function (file) {

                // Create the remove button
                var removeButton = Dropzone.createElement("<button class='btn btn-lg dark'>Remove File</button>");

                // Listen to the click event
                removeButton.addEventListener("click", function (e) {
                    // Make sure the button click doesn't submit the form:
                    e.preventDefault();
                    e.stopPropagation();

                    // Remove the file preview.
                    wrapperThis.removeFile(file);
                    // If you want to the delete the file on the server as well,
                    // you can do the AJAX request here.
                });

                // Add the button to the file preview element.
                file.previewElement.appendChild(removeButton);
            });

            this.on('sendingmultiple', function (data, xhr, formData) {
                formData.append("titulo", $("#titulo").val());
                formData.append("localizacion", $("#localizacion").val());
                formData.append("telefono", $("#telefono").val());
                formData.append("precio", $("#precio").val());
                formData.append("categoria", $("#categoria").val());
                formData.append("descripcion", $("#descripcion").val());
            });
        }
    };  
});

//http://www.howwaydo.com/integrating-dropzone-js-into-existing-html-form-with-other-fields/