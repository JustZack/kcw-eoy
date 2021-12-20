<?php

include_once "file-helpers.php";

$kcw_eoy_filter_json = kcw_eoy_getJSONfromFile(__DIR__ . "\auto-filter.json");

function kcw_eoy_categorize($json_transaction) {
    global $kcw_eoy_filter_json;

    $search = strtolower($json_transaction["Memo"]);
    $foundCategory = "None";
    foreach ($kcw_eoy_filter_json as $category=>$array) {
        foreach ($kcw_eoy_filter_json[$category] as $match) {
            if (strpos($search, $match) > -1)  {
                $foundCategory = $category; break;
            }
        }
    }

    $json_transaction["Category"] = $foundCategory;
    return $json_transaction;
}

//
function kcw_eoy_auto_categorize($json_transactions) {
    $categorized = [];
    foreach ($json_transactions as $transaction) {
        array_push($categorized, kcw_eoy_categorize($transaction));
    }

    return $categorized;

}

?>