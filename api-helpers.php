<?php

include_once "globals.php";

function kcw_eoy_TransactionInRange($transaction, $from, $to) {
    
}

function kcw_eoy_TransactionFileToAPIData($name) {
    $contents = file_get_contents($name);
    $json = json_decode($contents, true);

    $date = substr($name, 0, strrpos($name, '.json'));
    $date = substr($date, strrpos($date, '/')+1);
    $date = ucwords(str_replace('-', ' ', $date));
    
    $fData = array();
    $fData["date"] = $date;
    $fData["count"] = count($json);
    $last = $fData["count"]-1;
    $fData["first"] = $json[0]["Month"].'/'.$json[0]["Day"];
    $fData["last"] = $json[$last]["Month"].'/'.$json[$last]["Day"];
    $fData["year"] = substr($date, strrpos($date, " ")+1);
    $fData["uploaded"] = filectime($name);
    $fData["uid"] = $fData["first"] .">". $fData["last"] . strtolower(str_replace(" ", "", $date));

    return $fData;
}

function kcw_eoy_GetTransactionFileData() {
    $files = kcw_eoy_GetJSONFiles();
    $data = array();

    foreach ($files as $name) {
        $fData = kcw_eoy_TransactionFileToAPIData($name);
        array_push($data, $fData);
    }

    return $data;
}

?>