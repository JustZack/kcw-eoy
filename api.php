<?php

include_once "globals.php";
include_once "api-helpers.php";
include_once "categorize-transactions.php";

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

function kcw_eoy_api_GetKnownCategories($data) {
    $toReturn = array();
    $toReturn["items"] = kcw_eoy_GetKnownCategories();
    return kcw_eoy_api_Success($toReturn);
}

function kcw_eoy_api_GetStatements($data) {
    $toReturn = array();
    $toReturn["items"] = kcw_eoy_GetTransactionFileData();
    return kcw_eoy_api_Success($toReturn);
}

//Get all transaction file data for the given year
function kcw_eoy_api_Status($data) {
    $year = $data["year"];
    $files = kcw_eoy_GetStatementFileDataFor($year);
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
    $transactions = kcw_eoy_auto_categorize(kcw_eoy_GetTransactionsFor($year));

    $toReturn = array();
    $toReturn["year"] = $year;
    $toReturn["items"] = $transactions;

    return kcw_eoy_api_Success($toReturn);
}

//Save a year of transactions to a file
function kcw_eoy_api_SaveTransactions($data) {
    $transactions = kcw_eoy_api_GetTransactions($data);
    $year = kcw_eoy_SaveYearFile($transactions["year"], $transactions["items"]);
    $transactions["year"] = $year;
    return $transactions;
}

function kcw_eoy_epi_DeleteTransactionFile($data) {
    $year = $data["year"];
    $file = kcw_eoy_GetYearTransactionsFilesFolder()."/".$year.".json";

    if (file_exists($file)) {
        unlink($file);
        return kcw_eoy_api_Success(array());
    } else {
        return kcw_eoy_api_Error("File '$file' doesnt exist");
    }
    return $file;
}

function kcw_eoy_api_GetYearTransactionFiles($data) {
    $toReturn = array();
    $files = kcw_eoy_GetYearFilesData();
    $toReturn["files"] = $files;

    return kcw_eoy_api_Success($toReturn);
}

function kcw_eoy_api_GetTransactionFile($data) {
    $year = $data["year"];
    $month = $data["month"];
    $yearfile = kcw_eoy_GetYearTransactionFile($year);
    $toReturn = array();
    
    if (isset($month)) {
        if ((int)$month > 12) return kcw_eoy_api_Error("$month is not a month index.");
        $toReturn["items"] = kcw_eoy_GetMonthOfTransactions($yearfile, $month);
    }
    else $toReturn["items"] = kcw_eoy_GetMonthOfTransactions($yearfile, 1);
    
    return kcw_eoy_api_Success($toReturn);
}

function kcw_eoy_api_SetTransactionCategory($data) {
    $year = $data["year"];
    $index = (int)$data["index"];
    $category = $data["category"];
    
    $yearfile = kcw_eoy_GetYearTransactionFile($year);
    $transaction = $yearfile[$index];
    $oldCategory = $transaction["category"];
    $transaction["category"] = $category;
    $yearfile[$index] = $transaction;    
    kcw_eoy_SaveYearFile($year, $yearfile);

    $toReturn = array();
    $toReturn["transaction"] = $transaction;
    $toReturn["old"] = $oldCategory;
    $toReturn["new"] = $transaction["category"];

    return kcw_eoy_api_Success($toReturn);
}

function kcw_eoy_api_RegisterStatementBasedRoutes() {
    global $kcw_eoy_api_namespace;


    //List all statements
    register_rest_route("$kcw_eoy_api_namespace/v1", '/GetStatements/', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetStatements',
    ));
    //Get the status of a tax year, broken down by each month
    register_rest_route("$kcw_eoy_api_namespace/v1", '/Status/(?P<year>[0-9]{4})', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_Status',
    ));
    //Delete the given statement
    register_rest_route("$kcw_eoy_api_namespace/v1", '/DeleteStatement/(?P<filename>(([a-zA-Z0-9]+)-){3}([0-9]{4})\.json)', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_epi_DeleteStatement',
    ));
}

function kcw_eoy_api_RegisterYearBasedRoutes() {
    global $kcw_eoy_api_namespace;

    //Get all transactions for the given year without saving it
    register_rest_route("$kcw_eoy_api_namespace/v1", '/GetTransactions/(?P<year>[0-9]{4})', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetTransactions',
    ));
    //Save a file for all transactions in the given year
    register_rest_route("$kcw_eoy_api_namespace/v1", '/SaveTransactions/(?P<year>[0-9]{4})', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_SaveTransactions',
    ));
}

function kcw_eoy_api_RegisterYearFileBasedRoutes() {
    global $kcw_eoy_api_namespace;

    //Get all transaction files.
    register_rest_route("$kcw_eoy_api_namespace/v1", '/GetTransactionFiles/', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetYearTransactionFiles',
    ));
    //Delete the given transaction file
    register_rest_route("$kcw_eoy_api_namespace/v1", '/DeleteTransactionFile/(?P<year>([0-9]{4}))', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_epi_DeleteTransactionFile',
    ));
    //Get the transaction data for the given file (Starting on month 1)
    register_rest_route("$kcw_eoy_api_namespace/v1", '/GetTransactionFile/(?P<year>([0-9]{4}))', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetTransactionFile',
    ));
    //Get the transaction data for the given file on the given month
    register_rest_route("$kcw_eoy_api_namespace/v1", '/GetTransactionFile/(?P<year>([0-9]{4}))/(?P<month>([0-9]{1,2}))', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetTransactionFile',
    ));
    
    //Set the category for a given transaction in the given file
    register_rest_route("$kcw_eoy_api_namespace/v1", '/SetTransactionCategory/(?P<year>([0-9]{4}))/(?P<index>([0-9]+))/(?P<category>()[a-zA-Z]+)', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_SetTransactionCategory',
    ));
}

//Register all the API routes
function kcw_eoy_api_RegisterRestRoutes() {
    global $kcw_eoy_api_namespace;

    //Get all transaction files.
    register_rest_route("$kcw_eoy_api_namespace/v1", '/GetKnownCategories/', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetKnownCategories',
    ));
    kcw_eoy_api_RegisterStatementBasedRoutes();
    kcw_eoy_api_RegisterYearBasedRoutes();
    kcw_eoy_api_RegisterYearFileBasedRoutes();
}

add_action( 'rest_api_init', "kcw_eoy_api_RegisterRestRoutes");

?>