<?php


/**
 *Directive	options:
 * default-src
 * script-src
 * style-src
 * img-src
 * connect-src
 * font-src
 * object-src
 * media-src
 * frame-src
 * sandbox
 * report-uri
 * child-src
 * form-action
 * frame-ancestors
 * plugin-types
 *
 *
 * Source List:
 * * (wildcard)
 * 'none'
 * 'self'
 * data:
 * domain.example.com
 * *.example.com
 * https://cdn.com
 * https:
 * 'unsafe-inline'
 * 'unsafe-eval'
 * 'nonce-'
 * 'sha256-'
 * */

include($_SERVER['DOCUMENT_ROOT'].'/_static'.'/csp.inc.php'); // Array containing the csp urls is cspArr

$white_list = array(
    '\'unsafe-inline\'',            //inline default
    '*.ssl.google-analytics.com',   //google analytics
    '*',                            //wildcard
    'data:',                        //base64
);
$script_white_list_default = array(
    '\'self\'',
    '\'unsafe-eval\'',
    '\'unsafe-inline\'',
);

$script_white_list = array_merge($script_white_list_default, $cspArr); // Merge the default whitelisted values with our static file


$object_src = array(
    '\'none\'',
);
$csp_err_reporting = featureFlagManager::get("csp_report_only") ?: true;
//$report_uri = "//" . $_SERVER["SERVER_NAME"] . '/api/server/2.0/reportCsp/';
$report_uri = "https://bf65561be378a1c8a901c1038694dbda.report-uri.com/r/d/csp/reportOnly";
$base_header = '';
$policy = '';

//set up policy
if($csp_err_reporting){
    $base_header = 'Content-Security-Policy-Report-Only';
}
else{
    $base_header = 'Content-Security-Policy';
}
$policy = $base_header.': ';



//base white list
$policy .= ' default-src ';
foreach ($white_list as $domain){
    $policy .= $domain .' ';
}
$policy .=';';



//script white list
$policy .= ' script-src ';
foreach ($script_white_list as $domain){
    $policy .= $domain .' ';
}
$policy .=';';



//object-src section
$policy .= 'object-src ';
foreach ($object_src as $domain){
    $policy .= $domain .' ';
}
$policy .=';';


//add err reporting url
if($report_uri != ''){
    $policy .= ' report-uri '.$report_uri;
    $policy .=';';
}
//die('<hr /><pre>' . print_r($policy, true) . '</pre><hr />');
header($policy);

?>