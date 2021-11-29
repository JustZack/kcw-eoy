jQuery(document).ready(function(){
   var api_url = kcw_eoy.api_url;
   function ApiCall(endpoint, paremeter_string, then) {
       var url = api_url + endpoint + paremeter_string;
       console.log("REQUEST: " + url);
       
       var req = jQuery.get(url, then).done(function() {

       }).fail(function() {
           FailedRequest(endpoint);
       }).always(function() {

       });
   }
   function FailedRequest(endpoint) {
      console.log("Request for "+endpoint+" Failed");
   }

   var CALENDAR_YEAR = -1;
   function setCalendarYear() {
      CALENDAR_YEAR = jQuery("#kcw-eoy-select-year").val();
   }

   function createMonthRow(monthData) {
      var html = "";
      for (var d in monthData) {
         html += "<div>"+monthData[d].filename+"</div>";
      }
      return html;
   }
   function displayYearStatus(data) {
      var status = data.items;
      var html = "";
      for (var stat in status) {
         html += createMonthRow(status[stat]);
      }
      jQuery("#kcw-eoy-upload-status-wrapper").html(html);
   }
   function updateYearStatus() {
      ApiCall("Status/", CALENDAR_YEAR, displayYearStatus);
   }
   function doStepOne() {
      updateYearStatus();
      jQuery("#kcw-eoy-upload-wrapper").attr("style", "");
      jQuery("#kcw-eoy-start").attr("style", "display:none;");
      
   }
   
   jQuery("#kcw-eoy-start-generate-eoy").on('click', function(e){
      setCalendarYear();
      doStepOne();
   });

   jQuery("#kcw-eoy-start-browse-eoy").on('click', function(e){
      setCalendarYear();
   });

   jQuery("#kcw-eoy-upload-files-button").on('click', function(e){
      e.preventDefault();
      doUploadFile();
   });
   // Upload file
   function doUploadFile() {
      var files = document.getElementById("kcw-eoy-statements-upload").files;
      
      if(files.length > 0) {
         
         var formData = new FormData();
         formData.append("uploadDir", kcw_eoy.uploadPath);
         for (var f = 0; f < files.length; f++)
            formData.append("statements[]", files[f]);

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

