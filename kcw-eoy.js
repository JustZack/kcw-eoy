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

   function doDashboard() {
      jQuery("#kcw-eoy-start").attr("style", "");
      jQuery("#kcw-eoy-upload-wrapper").attr("style", "display:none;");
      jQuery("#kcw-eoy-transactions-wrapper").attr("style", "display:none; ");
      jQuery("#kcw-eoy-header-selected-year").text("");
   }

   jQuery("#kcw-eoy-header-home").on('click', function(e){
      doDashboard();
   });


   var CALENDAR_YEAR = -1;
   function setCalendarYear() {
      CALENDAR_YEAR = jQuery("#kcw-eoy-select-year").val();
      jQuery("#kcw-eoy-header-selected-year").text(CALENDAR_YEAR);
   }
   var months = ["January","February","March","April","May","June","July","August","September","October","November","December","January"];
   function createMonthRow(monthData, monthIndex) {
      var month = months[monthIndex];
      var year = monthIndex == 12 ? parseInt(CALENDAR_YEAR)+1 : CALENDAR_YEAR;

      var monthStatus = 2;      
      if (monthData.length > 1) monthStatus = 0;
      else if (monthData.length == 1) monthStatus = 1;

         var html = "<div class='kcw-eoy-month-status-row-wrapper'>";
      html += `<strong>${month} ${year}</strong>`;
      

      //Atleast one transaction log found
      if (monthData.length > 0) {
         for (var m in monthData) {
            var md = monthData[m]; var d = new Date(0); d.setUTCSeconds(md.uploaded)
            html += "<div class='kcw-eoy-month-status-row-item'>";
            html += `<div class='kcw-eoy-month-status-date'>${md.date}</div>`;
            html += `<div class='kcw-eoy-month-status-range'>${md.first} - ${md.last}</div>`;
            html += `<div class='kcw-eoy-month-status-count'>${md.count} Rows</div>`;
            html += `<div class='kcw-eoy-month-status-uploaded'>Uploaded on ${d.toLocaleString()}</div>`;

            //Show delete button for monthData.length>1
            if (monthStatus == 0) html += `<div class='kcw-eoy-month-status-delete-item' data-filename='${md.filename}'>Remove</div>`;
            html += "</div>";
         }
      }

      html += `<span class='kcw-eoy-month-status`;
      if (monthStatus == 0) { //Too many months
         monthStatus = 0;
         html += `-error'>Conflicting Statements`;
      } else if (monthStatus == 1) { //One month
         monthStatus = 1;
         html += `-ok'>One Statement Found`;
      } else if (monthStatus == 2) { //No months
         monthStatus = 2;
         html += `-warning'>No Statements Found`;
      }
      html += `</span>`;
         
      html += "</div>";
      return html;
   }

   function displayYearStatus(data) {
      var status = data.items;
      var html = "";
      for (var stat in status) {
         html += createMonthRow(status[stat], stat-1);
      }
      jQuery("#kcw-eoy-upload-status-wrapper").html(html);
   }
   function doStepOne() {
      ApiCall("Status/", CALENDAR_YEAR, displayYearStatus);
      jQuery("#kcw-eoy-upload-wrapper").attr("style", "");
      jQuery("#kcw-eoy-start").attr("style", "display:none;");
   }
   
   //Delete button in dynamic
   jQuery("#kcw-eoy-upload-status-wrapper").on('click', "div.kcw-eoy-month-status-delete-item", function(e) {
      var statement = jQuery(this).data("filename");
      ApiCall("DeleteStatement/", statement, doStepOne);
   });

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
   function doUploadFile(then) {
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
               doStepOne();
            }
         });
      }
   }

   function displayYearTransactions(data) {
      console.log(data);
   }
   function doStepTwo() {
      ApiCall("GetTransactions/", CALENDAR_YEAR, displayYearTransactions);
      jQuery("#kcw-eoy-transactions-wrapper").attr("style", "");
      jQuery("#kcw-eoy-upload-wrapper").attr("style", "display:none;");
   }

   jQuery("#kcw-eoy-categorize-transactions").on('click', function(e){
      doStepTwo();
   });
  
});

