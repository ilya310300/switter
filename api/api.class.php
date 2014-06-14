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
                if(empty($_POST)) { print $this->view->json_err("No params post."); }
                else {
                    if(method_exists($this, $action)){
                        $func = new ReflectionMethod($this, $action);
                        $args = array();
                        foreach ($func->getParameters() as $param)
                            $args[$param->getName()] = isset($_POST[$param->getName()])?$_POST[$param->getName()]:NULL;



                            call_user_func_array(array($this, $action), $args);
                        try {
                        } catch (ErrorException $e) {
                            print $this->view->json_err($_POST);}
                    } else {print $this->view->json_err("The method ".$action." is undefined."); }
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

    public function getUserInfo($UserID=null){
        $UserID = $UserID == null?$_SESSION['id']:$UserID;
        $res = $this->model->getUserInfo($UserID);
        if($res == false)
            $this->view->json_err('ErrorUserNotFound');
        else
            $this->view->doJsonPack($res);
    }

    public function doReg($registerVars){ // Input array('Login' => 'ivanpetrovich', ..) как в базе $key укзывать
        $res = $this->model->doReg($registerVars); // Возвращает  id зарегистрированного юзера∂
        if($res == false)
            $this->view->json_err('User register before');
        else
            $this->view->doJsonPack($res);
    }

    public function doLogin($login, $pass){
        $login = trim($login);
        $pass = trim($pass);
        $res = $this->model->doLogin($login, $pass);

        if($res == false){
            $this->view->json_err('ErrorUserNotFound');
        }
        else{
            $_SESSION['id'] = $res['id'];
            $_SESSION['login'] = $res['login'];
            $_SESSION['token'] = md5($res['passhash'].SALT_FOR_TOKEN);
            $this->view->doJsonPack($res);
        }
    }

    private function validToken($token){
        //Придумай тут что нибудь
        return true;
    }

    public function getFeed($token, $UserID){
        if(!$this->validToken($token)){
            $this->view->json_err('InvalidToken');
            return false;
        }

        $res = $this->model->getFeed($UserID);

        if($res[0] == 'ErrorUserNotFound'){
            $this->view->json_err('ErrorUserNotFound');
        }
        elseif($res[0] == 'ErrorFeedNotFound'){
            $this->view->json_err('ErrorFeedNotFound');
        }
        else{
            $this->view->doJsonPack($res);
        }

    }

}