<?php
/**
 * Created by PhpStorm.
 * User: Itay Bardugo
 * Date: 03/25/2021
 * Time: 4:04 PM
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/class/AutoLoader.class.inc.php");

AutoLoader::get_loader()->load_from([
    $_SERVER['DOCUMENT_ROOT'] . '/_inc/class',
    $_SERVER['DOCUMENT_ROOT'] . '/_inc/interface',
    $_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc',
    $_SERVER['DOCUMENT_ROOT'] . '/_crons',
]);

/**
 * php files must be named in the following template:
 * 	{classname}.php
{classname}.interface.inc.php
{classname}.trate.inc.php
{classname}.class.inc.php
{classname}.inc.class.php
{classname}.enum.inc.php
{classname}.class.php
 *
 *
you can extend the templates list by running this code:
AutoLoader::get_loader()->extend_templates([
".test.php",
".test2.php",
]);

 *  the final templates list will be set to:
{classname}.php
{classname}.interface.inc.php
{classname}.trate.inc.php
{classname}.class.inc.php
{classname}.inc.class.php
{classname}.enum.inc.php
{classname}.class.php
{classname}.test.php
{classname}.test2.php
 *
 *
 */

