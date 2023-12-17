<?php
/**
 * Created by PhpStorm.
 * User: Shavit
 * Date: 4/26/2021
 * Time: 10:55 AM
 */

$cp='qq978ARXjVyMdd';

include_once($_SERVER['DOCUMENT_ROOT']."/_inc/class/Database.class.inc.php"); // load database connection

if(!isset($_REQUEST["cp"]) || $_REQUEST["cp"] != $cp){
    die("No permission!");
}

$Db = Database::getInstance();
$Db->query("TRUNCATE tb_desktop__serialized_sessions");

echo "Done";
