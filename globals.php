<?php

$kcw_eoy_upload_path = wp_upload_dir()["basedir"]."/kcw-eoy/";
$kcw_eoy_upload_path = str_replace('\\', "/", $kcw_eoy_upload_path);
$kcw_eoy_upload_path = str_replace('//', "/", $kcw_eoy_upload_path);

function kcw_eoy_GetFiles($ext = "*") {
    global $kcw_eoy_upload_path;
    return glob($kcw_eoy_upload_path . "/$ext");
}

function kcw_eoy_GetJSONFiles() {
    return kcw_eoy_GetFiles("*.json");
}

?>