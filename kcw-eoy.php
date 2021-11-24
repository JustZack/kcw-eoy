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
include_once "categorize-transactions.php";

function  kcw_eoy_register_dependencies() {
    wp_register_style("kcw-eoy", plugins_url("kcw-eoy.css", __FILE__), null, "1.0.0");
    //wp_register_script("kcw-gallery", plugins_url("kcw-gallery.js", __FILE__), array('jquery'), "1.4.5");
}
add_action("wp_enqueue_scripts", "kcw_eoy_register_dependencies");

function kcw_eoy_enqueue_dependencies() {
    wp_enqueue_style("kcw-eoy");
    //wp_enqueue_script("kcw-eoy");
}

function kcw_eoy_upload_html($show = false) {
    return "
    <script src='https://unpkg.com/dropzone@5/dist/min/dropzone.min.js'></script>
    <link rel='stylesheet' href='https://unpkg.com/dropzone@5/dist/min/dropzone.min.css' type='text/css' />
    <div class='kcw-eoy-upload-wrapper'>
        <form method='post' action='upload-statements.php' class='dropzone' id='upload-dropzone' enctypemultipart/form-data'>
            <label for='kcw-eoy-statements-upload'>Upload Wells Fargo Statements: </label>
            <input type='file' id='kcw-eoy-statements-upload' name='kcw-eoy-statements-upload'> 
            <button id='kcw-eoy-upload-files'>Upload Files</button>
        </form>
    </div>
    ";
}



function kcw_eoy_dashboard_html($show = true) {
    return "
    <div class='kcw-eoy-dashboard-wrapper'>
        <div class='kcw-eoy-dashboard-row'>
            <center>
                <div class='kcw-eoy-dashboard-item'>Upload</div><div class='kcw-eoy-dashboard-item'>Select Transactions</div>
            </center>
        </div>
        <div class='kcw-eoy-dashboard-row'>
            <center>
                <div class='kcw-eoy-dashboard-item'>Download Reports</div>
            </center>
        </div>
    </div>";
}

function kcw_eoy_StartBlock() {
    return "<div class='kcw-eoy-wrapper'>\n";
} 
function kcw_eoy_EndBlock() {
    return "</div>";
}
function kcw_eoy_Init() {

    kcw_eoy_enqueue_dependencies();

    $current_step = $_GET["step"];
    if (!isset($current_step)) $current_step = "dashboard";

    $html = kcw_eoy_StartBlock();

    $html .= kcw_eoy_dashboard_html();
    $html .= kcw_eoy_upload_html();

    $f = kcw_eoy_getJSONfromFile(__DIR__ . "\october-18-2021.json");
    kcw_eoy_auto_categorize($f);

    $html .= kcw_eoy_EndBlock();
    echo $html;
}
add_shortcode("kcw-eoy", 'kcw_eoy_Init');
?>