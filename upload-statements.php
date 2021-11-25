<?php

$files = $_FILES["statements"];
$valid_type = array("application/pdf");
$path = str_replace("/", "\\", $_POST["uploadDir"]);
//Create the EOY uploads directory if needed
if (!file_exists($path)) mkdir($path, 0777);

//Non zero number of files uploaded
if (isset($files['name']) && count($files['name']) > 0) {
    //For each file
    for ($i = 0;$i < count($files['name']);$i++){
        $type = $files["type"][$i];
        //File must be a PDF
        if(in_array($type, $valid_type)){
            $tmp_path = $files["tmp_name"][$i];
            $real_path = $path.$files["name"][$i];
            //Save the file
            if(move_uploaded_file($tmp_path,$real_path)) {
                $res;
                exec("java -jar EndOfYear.jar $real_path $path", $res);
                unlink($real_path);
            } 
        }
    }

}

exit;
//https://blog.filestack.com/thoughts-and-knowledge/php-file-upload/
?>
