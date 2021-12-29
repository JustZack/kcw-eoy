<?php
/*
* Plugin Name:       KCW End Of Year
* Description:       Process KCW transactions from Wells fargo into end of year documents.
* Version:           0.0.2
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Zack Jones
*/

include_once "api.php";
include_once "file-helpers.php";
include_once "globals.php";

function  kcw_eoy_register_dependencies() {
    wp_register_style("kcw-eoy", plugins_url("kcw-eoy.css", __FILE__), null, "1.0.1");
    wp_register_script("kcw-eoy", plugins_url("kcw-eoy.js", __FILE__), array('jquery'), "1.0.5");
}
add_action("wp_enqueue_scripts", "kcw_eoy_register_dependencies");

function kcw_eoy_enqueue_dependencies() {
    wp_enqueue_style("kcw-eoy");
    wp_enqueue_script("kcw-eoy");
}

function kcw_eoy_transactions_html($show = false) {
    $style = ""; if ($show) $style = "style='display:none;'";
    return "
    <div id='kcw-eoy-transactions-wrapper' $style>

    </div>";
}

function kcw_eoy_upload_html($show = false) {
    $style = ""; if (!$show) $style = "style='display:none;'";

    return "
    <div id='kcw-eoy-upload-wrapper' $style>
        <div class='kcw-eoy-step-explanation'>
            Upload Wells Fargo Statements.
            Statements from January To January are required to generate a full end of year document.
        </div>
        <form id='uploads-form' action=''>
            <label for='kcw-eoy-statements-upload'>Upload Wells Fargo Statements: </label>
            <input type='file' multiple id='kcw-eoy-statements-upload' name='kcw-eoy-statements-upload'> 
            <button id='kcw-eoy-upload-files-button'>Upload Files</button>
        </form>
        <div id='kcw-eoy-upload-status-wrapper'></div>
        <button id='kcw-eoy-categorize-transactions'>View & Categorize Transactions</button>
    </div>
    ";
}

function kcw_eoy_dropdown($id, $values) {
    $select = "<select id='$id'>";
    foreach ($values as $val)
        $select .= "<option value='$val'>$val</option>";
    $select .= "</select>";
    return $select;
}

function kcw_eoy_dashboard_html($show = true) {
    $last20years = array();
    $current_year = (int)date("Y");
    $first_year = 2001;
    for ($i = $current_year;$i >= $first_year;$i--) {
        $last20years[] = $i;
    }
    $dropdown = kcw_eoy_dropdown("kcw-eoy-select-year", $last20years);
    return "
    <div id='kcw-eoy-start'>
        <div class='kcw-eoy-start-year'>
            <label for='kcw-eoy-year-input'>Choose a year </label>
            $dropdown
        </div>
        <div id='kcw-eoy-start-option'>
            <button id='kcw-eoy-start-generate-eoy'>Upload & Generate EOY</button>
            <button id='kcw-eoy-start-browse-eoy'>Browse EOY Documents</button>
        </div>
    </div>";
}

function kcw_eoy_header_html() {
    return "
    <div id='kcw-eoy-header'>
        <div></div>
        <div id='kcw-eoy-header-home'>Home</div>
        <div id='kcw-eoy-header-selected-year'></div>
        <div id='kcw-eoy-header-file-browser'>Files</div>
    </div>";
}

function kcw_eoy_js_data_html() {
    global $kcw_eoy_upload_path;
    global $kcw_eoy_api_url;
    $uploadURL = plugins_url('upload-statements.php', __FILE__);
    $uploadPath = $kcw_eoy_upload_path;
    $apiURL = $kcw_eoy_api_url;
    $js = "<script>var kcw_eoy = { 'uploadURL' : '$uploadURL'," 
         ."'uploadPath' : '$uploadPath'," 
         ."'api_url' : '$apiURL'"
         ."}</script>";
    return $js;
}

function kcw_eoy_StartBlock() {
    return "<div class='kcw-eoy-wrapper'>\n";
} 
function kcw_eoy_EndBlock() {
    return "</div>";
}
function kcw_eoy_Init() {

    kcw_eoy_enqueue_dependencies();

    $html = kcw_eoy_StartBlock();

    $html .= kcw_eoy_js_data_html();

    $html .= kcw_eoy_header_html();
    $html .= kcw_eoy_dashboard_html();
    $html .= kcw_eoy_upload_html();
    $html .= kcw_eoy_transactions_html();

    $html .= kcw_eoy_EndBlock();
    echo $html;
}
add_shortcode("kcw-eoy", 'kcw_eoy_Init');
?>