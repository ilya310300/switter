<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 14.06.14
 * Time: 13:33
 */

class view{

    function __construct(){

    }

    public function json_err($err) {
        $json = array("Error" => $err);
        return json_encode($json);
    }

    public function doJsonPack($dataArray, $errormsg = 'Unknown'){
        if($dataArray == false)
            echo json_encode(array('Error' => $errormsg));
        else
            echo json_encode($dataArray);
    }
}