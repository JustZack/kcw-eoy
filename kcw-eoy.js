jQuery(document).ready(function(){

   jQuery("#kcw-eoy-upload-files-button").on('click', function(e){
      e.preventDefault();
      uploadFile();
   });

    // Upload file
   function uploadFile() {
      var files = document.getElementById("kcw-eoy-statements-upload").files;
      
      if(files.length > 0) {
         
         var formData = new FormData();
         formData.append("uploadDir", kcw_eoy.uploadPath);
         for (var f = 0; f < files.length; f++) {
            formData.append("statements[]", files[f]);
         }
         //for (var f in files) formData.append("statements[]", f);

         jQuery.ajax({
            url: kcw_eoy.uploadURL,
            data: formData,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(data, status){
               console.log("Data: " + data + "\nStatus: " + status);
            }
         });
      }
   }
});

