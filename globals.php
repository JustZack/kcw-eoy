<?php

$kcw_eoy_upload_path = wp_upload_dir()["basedir"]."/kcw-eoy/";
$kcw_eoy_upload_path = str_replace('\\', "/", $kcw_eoy_upload_path);
$kcw_eoy_upload_path = str_replace('//', "/", $kcw_eoy_upload_path);

function kcw_eoy_GetFiles($ext = "*", $relativepath = "") {
    global $kcw_eoy_upload_path;
    return glob($kcw_eoy_upload_path . "$relativepath/$ext");
}

function kcw_get_GetStatementJSONFiles() {
    return kcw_eoy_GetJSONFiles();
}

function kcw_get_GetTransactionJSONFiles() {
    return kcw_eoy_GetJSONFiles("");
}

function kcw_eoy_GetJSONFiles($relativepath = "") {
    return kcw_eoy_GetFiles("*.json", $relativepath);
}

?>