<?php

include_once "globals.php";

function kcw_eoy_TransactionFileToAPIData($name) {
    $contents = file_get_contents($name);
    $json = json_decode($contents, true);


    $date = substr($name, 0, strrpos($name, '.json'));
    $date = substr($date, strrpos($date, '/')+1);
    $filename = $date;
    $date = substr($date, strpos($date, '-')+1);
    $date = ucwords(str_replace('-', ' ', $date));
    
    $fData = array();
    $fData["date"] = $date;
    $fData["count"] = count($json);
    $fData["filename"] = $filename.".json";
    $last = $fData["count"]-1;
    $fData["first"] = $json[0]["Month"].'/'.$json[0]["Day"];
    $fData["last"] = $json[$last]["Month"].'/'.$json[$last]["Day"];
    $fData["year"] = substr($date, strrpos($date, " ")+1);
    $fData["uploaded"] = filectime($name);

    return $fData;
}

/* Note: Wells fargo statements are labeled by their LAST transaction.
    I.E. A statement from January 18th, 2021 
    includes transactions from December 18th, 2020 up to the statement date.
--> To include all transactions in 2021 you need Statements from January 2021 -> January 2022.
*/
//Get transaction file data from January of the given year to January of the next.
function kcw_eoy_GetTransactionFileDataFor($year) {
    $files = kcw_get_GetStatementJSONFiles();
    $data = array();

    for ($month = 1;$month<13;$month++) $data["".$month] = array();

    $intYear = (int)$year;
    foreach ($files as $name) {
        $fData = kcw_eoy_TransactionFileToAPIData($name);
        $startMonth = explode("/", $fData["first"])[0];
        $endMonth = explode("/", $fData["last"])[0];

        //Any statement from the given year passes OR Check for the first statement of the NEXT year to get the last couple transactions of the desired year
        if ($fData["year"] == $year) {
            if (!isset($data[$endMonth])) $data[$endMonth] = array();
            array_push($data[$endMonth], $fData);
        } else if ($intYear+1 == (int)$fData["year"] && $startMonth == "12") {
            if (!isset($data["13"])) $data["13"] = array();
            array_push($data["13"], $fData);
        }
    }

    return $data;
}

function kcw_eoy_CullTransactions($transactions, $keepMonth) {
    $toReturn = array();

    for ($i = 0;$i < count($transactions);$i++) {
        $t = $transactions[$i];
        if ($t["Month"] == $keepMonth) array_push($toReturn, $t);
    }

    return $toReturn;
}

/* 
    NOTE: this code ends up taking the LAST file it finds for each month of the given year,
    Since it is non-arbituaruy to determine which files to use, it is important that
    exactly 13 statements exist for the given year when EOY is generated.
 */
function kcw_eoy_GetTransactionsFor($year) {
    $files = kcw_get_GetStatementJSONFiles();
    $data = array();
    for ($month = 1;$month<13;$month++) $data["".$month] = array();
    $intYear = (int)$year;
    foreach ($files as $name) {
        $fData = kcw_eoy_TransactionFileToAPIData($name);
        $startMonth = explode("/", $fData["first"])[0];
        $endMonth = explode("/", $fData["last"])[0];
        
        $transactions = json_decode(file_get_contents($name), true);
        
        //Any statement from the given year passes OR Check for the first statement of the NEXT year to get the last couple transactions of the desired year
        if ($fData["year"] == $year) {
            //var_dump($fData["year"]);
            //var_dump($endMonth);
            //Cull December transactions from current year January transactions
            if ($startMonth == "12") $transactions = kcw_eoy_CullTransactions($transactions, "1");
            $data[$endMonth] = $transactions;
        } else if ($intYear+1 == (int)$fData["year"] && $startMonth == "12") {
            //Cull January transactions from currnt year December transactions
            $data["13"] = kcw_eoy_CullTransactions($transactions, "12");
        }
    }

    $allTransactions = array();
    foreach ($data as $t)
        $allTransactions = array_merge($allTransactions, $t);

    return $allTransactions;
}

function kcw_eoy_GetTransactionFileData() {
    $files = kcw_get_GetStatementJSONFiles();
    $data = array();

    foreach ($files as $name) {
        $fData = kcw_eoy_TransactionFileToAPIData($name);
        array_push($data, $fData);
    }

    return $data;
}

//Save transaction data based on the given year.
//Saves under kcw-eoy/years/$year.x.json
function kcw_eoy_SaveTransactionData($year, $transactions) {
    $years_transactions = kcw_get_GetTransactionJSONFiles();
    $copyNum = 0;
    foreach ($years_transactions as $year_path) if (strpos($year_path, "/".$year.".")) $copyNum++;

    $name = $year . "." . $copyNum;
    $file = kcw_eoy_GetTransactionFilesFolder() . "/" . $name . ".json";
    file_put_contents($file, json_encode($transactions));

    return $name;
}

function kcw_eoy_GetYearFile($name) {
    $years_transactions = kcw_get_GetTransactionJSONFiles();
    foreach ($years_transactions as $year_path) 
        if (strpos($year_path, "/".$name.".json"))
            return json_decode(file_get_contents($year_path), true);
}
function kcw_eoy_SaveYearFile($name, $transactions) {
    $file = kcw_eoy_GetTransactionFilesFolder() . "/" . $name . ".json";
    file_put_contents($file, json_encode($transactions));
}

function kcw_eoy_YearFileToAPIData($name) {
    $contents = file_get_contents($name);
    $json = json_decode($contents, true);
    
    $fData = array();
    $fData["filename"] = substr($name, strrpos($name, "/")+1);
    $fData["count"] = count($json);
    $last = $fData["count"]-1;
    $fData["first"] = $json[0]["Month"].'/'.$json[0]["Day"];
    $fData["last"] = $json[$last]["Month"].'/'.$json[$last]["Day"];
    $fData["created"] = filectime($name);

    return $fData;
}

function kcw_eoy_GetYearFileData($year = -1) {
    $filepaths = kcw_get_GetTransactionJSONFiles();
    $files = array();
    if ($year == -1) {
        $files = $filepaths;
    } else {
        foreach ($filepaths as $year_path) 
            if (strpos($year_path, "/".$year.".")) 
                array_push($files, $year_path);
    }

    $data = array();
    foreach ($files as $yearfile) {
        array_push($data, kcw_eoy_YearFileToAPIData($yearfile));
    }

    return $data;
}
?>