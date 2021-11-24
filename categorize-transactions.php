<?php

include_once "file-helpers.php";

$kcw_eoy_filter_json = [];

function kcw_eoy_categorize($json_transaction) {
    global $kcw_eoy_filter_json;

    $search = strtolower($json_transaction["Memo"]);
    
    foreach ($kcw_eoy_filter_json as $category=>$array) {
        foreach ($kcw_eoy_filter_json[$category] as $match) {
            if (strpos($search, $match) > -1) return $category;
        }
    }
    return "None";
}

//
function kcw_eoy_auto_categorize($json_transactions) {

    global $kcw_eoy_filter_json;
    if (count($kcw_eoy_filter_json) == 0) $kcw_eoy_filter_json = kcw_eoy_getJSONfromFile(__DIR__ . "\auto-filter.json");
    
    $categorized = [];
    foreach ($json_transactions as $transaction) {
        $category = kcw_eoy_categorize($transaction);
        $newTransaction = $transaction;
        $newTransaction["Category"] = $category;
        array_push($categorized, $newTransaction);
    }
    
    var_dump($categorized);

    return null;

}

?>