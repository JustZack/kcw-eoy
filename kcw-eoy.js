jQuery(document).ready(function(){
    Dropzone.autoDiscover = false;
   
    var myDropzone = new Dropzone(".dropzone", { 
       autoProcessQueue: false,
       maxFilesize: 1,
       acceptedFiles: ".pdf"
    });
    
    $('#kcw-eoy-upload-files').click(function(){
       myDropzone.processQueue();
    });
});

