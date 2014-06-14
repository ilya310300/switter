<?php
/**
 * Created by PhpStorm.
 * User: dmitrijdorozkin
 * Date: 14.06.14
 * Time: 12:08
 */
include_once 'model.class.php';
include_once 'view.class.php';

define('SALT', 'MegaSalt'); // !!!!!!!!!!!!!!!!!!!!!!!!!! На время тестирования !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


class api {
    private $model;


    function __construct() {

        $this->model = new model();
        $this->view = new view();
        $action = explode('/', $_SERVER['REQUEST_URI']);
        if ((count($_REQUEST)>0)&&($action[1]=="api")) {
            $action = end($action);
            if(!empty($action)) {
                if(empty($_POST)) { print view::json_err("No params post."); }
                else {
                    if(method_exists($this, $action)){
                        $func = new ReflectionMethod($this, $action);
                        $args = array();
                        foreach ($func->getParameters() as $param)
                            $args[$param->getName()] = isset($_POST[$param->getName()])?$_POST[$param->getName()]:NULL;



                            call_user_func_array(array($this, $action), $args);
                        try {
                        } catch (ErrorException $e) {
                            print view::json_err($_POST);}
                    } else {print view::json_err("The method ".$action." is undefined."); }
                }
            }
        }
    }

    private function getRandString($length){
        $char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $char = str_shuffle($char);
        for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
            $rand .= $char{mt_rand(0, $l)};
        }
        return $rand;
    }

    public function issetLogined(){
        if(!empty($_SESSION['token']))
            return true;
        else
            return false;
    }

    public function getUserInfo($UserID=null, $token=null){
        $res = $this->model->getUserInfo($UserID, $token);
        if($res == false)
            view::json_err('ErrorUserNotFound');
        else
            view::doJsonPack($res);
    }

    private function _getToken($email,$password,$action,$social = NULL) { //return (INT)||(Array)||(String)
        switch($action) {
            case "signin":
                if($login = $this->model->tryLogin($email,crypt($password,SALT)))
                    return $login;  //row from `UserPrivate`
                else
                    return ERROR_1;
                break;
            case "signup":
                if($email!=""&&$password!="") {
                    if(!$this->model->emailExists($email)) {
                        $token = "u_".md5(uniqid('', true));
                        return array($token,$this->model->doSignUp($email,crypt($password,SALT),$token)); //userID (INT)
                    }
                } else return ERROR_23;
                return ERROR_2;
                break;
        }
        print view::json_err(serialize($_REQUEST).' '.ERROR_3);
        exit;
    }

    public function signUp($email,$password) {
        view::signUp($this->_getToken($email,$password,"signup"));
    }

    public function signIn($email,$password) {
        view::signIn($this->_getToken($email,$password,"signin"));
    }

}