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

function kcw_eoy_api_GetTransactions($data) {
    $transactions = kcw_eoy_GetTransactionFileData();
    $data = array();
    
    //Get transactions for the given range if required params are present
    if (isset($_GET["from"]) && isset($_GET["to"])) {
        $from = explode('.', $_GET["from"]);
        $to = explode('.', $_GET["to"]);
        $data["from"] = $from;
        $data["to"] = $to;

        $items = array();
        foreach ($transactions as $t) {
            array_push($items, $t);
        }
        $data["items"] = $items;
    }//Otherwise respond with an error
    else { 
        return kcw_eoy_api_Error("Missing required parameters.");
    }
    return kcw_eoy_api_Success($data);
}

//Register all the API routes
function kcw_eoy_api_RegisterRestRoutes() {
    global $kcw_eoy_api_namespace;
    register_rest_route("$kcw_eoy_api_namespace/v1", '/List/TransactionFiles', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetTransactionFileList',
    ));
    register_rest_route("$kcw_eoy_api_namespace/v1", '/GetTransactions', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetTransactions',
    ));
}

add_action( 'rest_api_init', "kcw_eoy_api_RegisterRestRoutes");

?>