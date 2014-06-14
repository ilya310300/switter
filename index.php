<?php
/**
 * Created by PhpStorm.
 * User: dmitrijdorozkin
 * Date: 14.06.14
 * Time: 11:48
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_error_handler('handleError');
header('Content-type: text/html; charset=UTF-8');
session_start();
date_default_timezone_set("UTC");

function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    if (0 === error_reporting()) return false;
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler('handleError');

$action = explode('/', $_SERVER['REQUEST_URI']);
$api = null;

if($action[1]=="api") {
    header('Content-type: application/json; charset=UTF-8');
    require_once 'api/api.class.php';
    new api();
} else {

    function __autoload($method) {
        $section = strtolower($method);


        if (file_exists(getcwd() .'/frontend/sections/' . $section . '/controller.php') == false) {
            die('404');
        } else {
            include_once(getcwd() . '/api/api.class.php');
            include_once(getcwd() . '/frontend/libs/templates.class.php');
            global $api;
            $api = new api;
            include (getcwd() .'/frontend/sections/' . $section . '/controller.php');
        }


    }

    $action = explode('/', $_SERVER['REQUEST_URI']);

    $method = empty($action[1])?'main':$action[1];

    $content = new $method;

}