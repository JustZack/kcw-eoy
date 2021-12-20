<?php

include_once "globals.php";
include_once "api-helpers.php";

$kcw_eoy_api_namespace = "kcweoy";
$kcw_eoy_api_url = home_url('wp-json/' . $kcw_eoy_api_namespace . '/v1/');
//$kcw_eoy_api_url = "https://kustomcoachwerks.com/wp-json/kcweoy/v1/";

//Api request ran into error
function kcw_eoy_api_Error($msg) {
    $data = array();
    $data["message"] = $msg;
    $data["status"] = "Error";
    return $data;
}
//Api request succeeded!
function kcw_eoy_api_Success($data) {
    $data["status"] = "Success";
    $data["time"] = time();
    return $data;
}

function kcw_eoy_api_GetTransactionFileList($data) {
    $data = array();
    $data["items"] = kcw_eoy_GetTransactionFileData();
    return kcw_eoy_api_Success($data);
}

//Get all transaction file data for the given year
function kcw_eoy_api_Status($data) {
    $year = $data["year"];
    $files = kcw_eoy_GetTransactionFileDataFor($year);
    $data = array();
    $data["items"] = $files;
    return kcw_eoy_api_Success($data);
}

function kcw_eoy_epi_DeleteStatement($data) {
    global $kcw_eoy_upload_path;
    $filename = $data["filename"];
    $file = $kcw_eoy_upload_path.$filename;

    if (file_exists($file)) {
        unlink($file);
        return kcw_eoy_api_Success(array());
    } else {
        return kcw_eoy_api_Error("File '$filename' doesnt exist");
    }
}

//Get all transactions for the given year
function kcw_eoy_api_GetTransactions($data) {
    $year = $data["year"];

    //Get all available transactions for the given year
    $transactions = kcw_eoy_GetTransactionsFor($year);

    $toReturn = array();
    $toReturn["year"] = $year;
    $toReturn["items"] = $transactions;

    return kcw_eoy_api_Success($toReturn);
}

//Register all the API routes
function kcw_eoy_api_RegisterRestRoutes() {
    global $kcw_eoy_api_namespace;

    register_rest_route("$kcw_eoy_api_namespace/v1", '/Status/(?P<year>[0-9]{4})', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_Status',
    ));

    register_rest_route("$kcw_eoy_api_namespace/v1", '/GetTransactions/(?P<year>[0-9]{4})', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetTransactions',
    ));

    register_rest_route("$kcw_eoy_api_namespace/v1", '/DeleteStatement/(?P<filename>(([a-zA-Z0-9]+)-){3}([0-9]{4})\.json)', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_epi_DeleteStatement',
    ));
}

add_action( 'rest_api_init', "kcw_eoy_api_RegisterRestRoutes");

?>