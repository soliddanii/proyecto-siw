//Dropzone.autoDiscover = false;

$(document).ready(function(){
 
    /* CONFIGURACION DEL DROPZONE */
    // https://github.com/enyo/dropzone/wiki/Combine-normal-form-with-Dropzone
    //https://stackoverflow.com/questions/30808901/dropzone-js-inside-another-php-form
    
    //var myDropzone = new Dropzone("div#myDropzoneJs", { url: "/file/post"});
    /*$("div#myDropzoneJs").dropzone({
	    url: "/file/post"
	});*/
      
    Dropzone.options.myDropzoneJs = {
    
        url: "../php/class/upload.php", //where to post the images
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 50,
        maxFiles: 50,
        maxFilesize: 10, //MB
        addRemoveLinks: true,
        dictDefaultMessage: "Arrastra aquí las imágenes",
        dictResponseError: 'Servidor No Configurado',
        acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
        init:function(){
        
            // Config
            this.options.addRemoveLinks = true;
            this.options.dictRemoveFile = "Borrar";
            
            // First change the button to actually tell Dropzone to process the queue.
            this.element.querySelector("button[type=submit]").addEventListener("click", function(e) {
                // Make sure that the form isn't actually being sent.
                e.preventDefault();
                e.stopPropagation();
                this.processQueue();
            });
    
            // New file added
            this.on("addedfile", function (file) {
                console.log('new file added ', file);
            });
            
            // Send file starts
            this.on("sending", function (file) {
                console.log('upload started', file);
                $('.meter').show();
            });
            
            // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
            // of the sending event because uploadMultiple is set to true.
            this.on("sendingmultiple", function() {
                // Gets triggered when the form is actually being sent.
                // Hide the success button or the complete form.
            });
            
            this.on("successmultiple", function(files, response) {
                // Gets triggered when the files have successfully been sent.
                // Redirect user or notify of success.
            });
    
            this.on("errormultiple", function(files, response) {
                // Gets triggered when there was an error sending the files.
                // Maybe show form again, and notify user of error
            });
      
            // File upload Progress
            this.on("totaluploadprogress", function (progress) {
                console.log("progress ", progress);
                $('.roller').width(progress + '%');
            });

            this.on("queuecomplete", function (progress) {
                $('.meter').delay(999).slideUp(999);
            });
      
            // On removing file
            this.on("removedfile", function (file) {
                console.log(file);
            });
        }
    };

});