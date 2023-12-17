<?php

//date_default_timezone_set('Asia/Jerusalem');
ini_set('MEMORY_LIMIT', '256MB');
set_time_limit(540);

$cp = 'qq978ARXjVyMdd';
if ($_REQUEST['cp'] != $cp) {
    die('...');
}
//mail('oleg@inmanage.net','mail - '.__FILE__,print_r(array('GEOIP CRON START OK!'),true),'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
// includes
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path . $_includes_path . "dblayer.inc.php"); // load database connection
include_once($_project_server_path . $_includes_path . "modules.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "site.array.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "modules.functions.inc.php");  // load module functions
include_once($_project_server_path . $_includes_path . "html.functions.inc.php");  // load module functions
include_once($_project_server_path . $_salat_path . $_includes_path . "functions.inc.php"); // load various functions
//$Seo = new Seo();
// /includes

function destroy_dir($dir)
{
    if ($dir == '' || $dir == $_SERVER['DOCUMENT_ROOT'] || strpos($dir, $_SERVER['DOCUMENT_ROOT']) === false) {
        return;
    }

    if (!is_dir($dir) || is_link($dir)) return unlink($dir);
    foreach (scandir($dir) as $file) {
        if ($file == '.' || $file == '..') continue;
        if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) {
            chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
            if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                return false;
            }
        };
    }
    return rmdir($dir);
}


$ts = time();
//initialize paths
$download_url = 'http://download.maxmind.com/app/geoip_download?edition_id=108&suffix=zip&license_key=NbVUPLJo0J8q'; //csv version
$archive_dir_all = $_SERVER['DOCUMENT_ROOT'] . '/_static/geoip/archives';
$unziped_dir_all = $_SERVER['DOCUMENT_ROOT'] . '/_static/geoip/unziped';
//calculated paths
$filename = date('YmdHis'); //this second
$archive_file = $archive_dir_all . '/' . $filename . '.zip';
$unziped_dir = $unziped_dir_all . '/' . $filename;

//download csv
$file = file_get_contents($download_url);
if (!$file) {
    mail('daniel@inmanage.net', 'Salat GeoIP Error', 'download');
    die('download err');
}
file_put_contents($archive_file, $file);
if (!is_file($archive_file)) {
    mail('daniel@inmanage.net', 'Salat GeoIP Error', 'file creation');
    die('file creation');
}

//make unziped dir
mkdir($unziped_dir);
chmod($unziped_dir, 0777);

//extract from the zip
$zip = new ZipArchive;
$res = $zip->open($archive_file);
$zip->extractTo($unziped_dir);
$zip->close();
//get csv data
$csv_folders = glob($unziped_dir . '/GeoIP*');
$csv_folder = reset($csv_folders);
$csv_files = glob($csv_folder . '/*.csv');
$csv_file = reset($csv_files);

//remove more than 3 archives
$arcive_files = glob($unziped_dir_all . '/*');
rsort($arcive_files);
$i = 0;
foreach ($arcive_files as $val) {
    if ($i > 2) {
        $pathArr = explode('/', $val);
        $key_to_remove = end($pathArr);
        //remove archive file
        unlink($archive_dir_all . '/' . $key_to_remove . '.zip');
        //remove unziped dir
        destroy_dir($unziped_dir_all . '/' . $key_to_remove);
    }
    $i++;
}

//make lines array from csv
$csvLinesArr = file($csv_file, FILE_IGNORE_NEW_LINES);
$csvLinesArr = array_slice($csvLinesArr, 2); //remove "Copyright (c) 2011 MaxMind Inc.  All Rights Reserved." and headers

if (empty($csvLinesArr)) {
    mail('daniel@inmanage.net', 'Salat GeoIP Error', 'csv');
    die('csv');
}

//empty tb_geoip table
$Db->query('TRUNCATE TABLE `tb_geoip`');

//insert data to db
foreach ($csvLinesArr as $line) {
    list($db_fields['beginIp'], $db_fields['endIp'], $db_fields['beginIpNum'], $db_fields['endIpNum'], $db_fields['countryCode'], $db_fields['countryName']) = str_getcsv($line);
    foreach ($db_fields AS $key => $value) {
        $db_fields[$key] = $Db->make_escape($value);
    }
    $db_fields['last_update'] = $ts;
    $res = $Db->insert('tb_geoip', $db_fields);
}