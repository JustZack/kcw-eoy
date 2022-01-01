<?php

$kcw_eoy_upload_path = wp_upload_dir()["basedir"]."/kcw-eoy/";
$kcw_eoy_upload_path = str_replace('\\', "/", $kcw_eoy_upload_path);
$kcw_eoy_upload_path = str_replace('//', "/", $kcw_eoy_upload_path);

function kcw_eoy_GetFiles($ext = "*", $relativepath = "") {
    global $kcw_eoy_upload_path;
    $abolsutedir = $kcw_eoy_upload_path.$relativepath;
    if (!file_exists($abolsutedir)) mkdir($abolsutedir);
    return glob($abolsutedir . "/*$ext");
}

/*function kcw_eoy_GetFiles($ext = "", $relativepath = "") {
    global $kcw_eoy_upload_path;
    $abolsutedir = $kcw_eoy_upload_path.$relativepath;
    if (!file_exists($abolsutedir)) mkdir($abolsutedir);
    $files = scandir($abolsutedir);

    $actual_files = array();
    foreach ($files as $fname) {
        if (strlen($ext) == 0 || strpos($fname, $ext) > -1) {
            $file_path = $abolsutedir."/".$fname;
            if (file_exists($file_path)) {
                array_push($actual_files, $file_path);
            }
        }
    }
    return $actual_files;
}*/

function kcw_eoy_GetJSONFiles($relativepath = "") {
    return kcw_eoy_GetFiles(".json", $relativepath);
}

function kcw_get_GetWellsFargoStatementJSONFiles() {
    return kcw_eoy_GetJSONFiles();
}
function kcw_eoy_GetYearTransactionsFilesFolder() {
    global $kcw_eoy_upload_path;
    $abolsutedir = $kcw_eoy_upload_path . "years";
    return $abolsutedir;
}
function kcw_get_GetYearTransactionJSONFiles() {
    return kcw_eoy_GetJSONFiles("years");
}

?>