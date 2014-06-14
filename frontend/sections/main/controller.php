<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 14.06.14
 * Time: 12:42
 */

class main {

    function __construct(){
        global $api;

        if($api->issetLogined()){
            $info = $api->getUserInfo();
            header('Location: /user/'.$info['username']);
            exit();
        }

        $content = new Template(getcwd() . '/frontend/main.tpl');
        $regForm = new Template(getcwd() . '/frontend/sections/main/regForm.tpl');
        $loginForm = new Template(getcwd() . '/frontend/sections/main/loginForm.tpl');

        $mergeForms = $regForm->output().'<hr>'.$loginForm->output();

        $content->set('title', 'Главная');
        $content->set('content', $mergeForms);

        echo $content->output();


    }

}