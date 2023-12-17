<?php
spl_autoload_register(function($className) {

    $root_path    = $_SERVER['DOCUMENT_ROOT'];
    $root_dir     = '/resource/FacebookSDK';
    $api_dir      = 'api';
    $class_prefix = '.class.php';
    $class_name   = str_replace('\\', '/', $className);
    $file_pass    = (!strpos($root_path, $root_dir)) ? $root_path . $root_dir : $root_path;
    $file         = $file_pass . '/' . $api_dir . '/' . $class_name . $class_prefix;

    try {
        if (is_file($file)) {
            require $file;
        }
    } catch(Exception $e) {
        echo $e->getMessage();
    }
});