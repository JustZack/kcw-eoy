<?php

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

//Return the given gallery, with an assumed first page
function kcw_eoy_api_GetGallery($data) {
    $data['gpage'] = 1;
}

//Register all the API routes
function kcw_eoy_api_RegisterRestRoutes() {
    global $kcw_eoy_api_namespace;
    //Route for /gallery-id
    register_rest_route( "$kcw_eoy_api_namespace/v1", '/(?P<guid>[a-zA-Z0-9-\.\(\)_h]+)', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetGallery',
    ));
    //Route for /gallery-id/meta
    register_rest_route( "$kcw_eoy_api_namespace/v1", '/(?P<guid>[a-zA-Z0-9-\.\(\)_h]+)/meta', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetGalleryMeta',
    ));
    //Route for /gallery-id/page
    register_rest_route("$kcw_eoy_api_namespace/v1", '/(?P<guid>[a-zA-Z0-9-\.\(\)_h ]+)/(?P<gpage>\d+)', array(
        'methods' => 'GET',
        'callback' => 'kcw_eoy_api_GetGalleryPage',
    ));
}

add_action( 'rest_api_init', "kcw_eoy_api_RegisterRestRoutes");

?>