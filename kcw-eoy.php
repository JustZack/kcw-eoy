<?php
/*
* Plugin Name:       KCW End Of Year
* Description:       Process KCW transactions from Wells fargo into end of year documents.
* Version:           0.0.1
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Zack Jones
*/

include_once "api.php";
include_once "file-helpers.php";
include_once "categorize-transactions.php";

function  kcw_eoy_register_dependencies() {
    //wp_register_style("kcw-gallery", plugins_url("kcw-gallery.css", __FILE__), null, "1.4.6");
    //wp_register_script("kcw-gallery", plugins_url("kcw-gallery.js", __FILE__), array('jquery'), "1.4.5");
}
add_action("wp_enqueue_scripts", "kcw_eoy_register_dependencies");

function kcw_eoy_enqueue_dependencies() {
    //wp_enqueue_style("kcw-eoy");
    //wp_enqueue_script("kcw-eoy");
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

    $f = kcw_eoy_getJSONfromFile(__DIR__ . "\october-18-2021.json");
    kcw_eoy_auto_categorize($f);

    $html .= kcw_eoy_EndBlock();
    echo $html;
}
add_shortcode("kcw-eoy", 'kcw_eoy_Init');
?>