<?php
/**
 * Created by PhpStorm.
 * User: dmitrijdorozkin
 * Date: 17.05.14
 * Time: 23:30
 */
class model {

    private $_db;

    function __construct() {
        try {
            include_once 'config.php';
            $this->_db->exec('SET NAMES utf8mb4');
        } catch (PDOException $e) {}
    }

    function __destruct() {
        $this->_db = NULL;
    }

    protected function catch_db_error($query) {
        $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        try {
            $dbh = $this->_db->query($query);
        } catch (Exception $e) {
            echo $e->getMessage();die();
        }
        if(!$dbh){
            print $query;
            print_r(array("Error" => "Mysql syntax error."));die();
        }
        return $dbh;
    }

    protected function select_one($query) {
        return $this->catch_db_error($query)->fetch(PDO::FETCH_ASSOC);
    }

    private function select($query) {
        return $this->catch_db_error($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function replace($query) {
        $this->catch_db_error($query);
        return $this->_db->lastInsertId();
    }

    private function insert($query) {
        $this->catch_db_error($query);
        return $this->_db->lastInsertId();
    }

    private function update($query) {
        return $this->catch_db_error($query);
    }

    public function getUserInfo($UserID){
        $res = $this->select_one('SELECT * FROM `UserPrivate` WHERE `id` = "'.$UserID.'"');

        if(count($res) == 0)
            return false;
        else
            return $res;
    }

    public function doReg($registerVars){

        if(count($this->select_one('SELECT `id` FROM `UserPrivate` WHERE `Login` = "'.addslashes(trim($registerVars['login'])).'" OR `email` = "'.addslashes(trim($registerVars['email'])).'"')) != 0)
            return false; // Проверка есть ли уже пользователь с таким логином или мылом

        $names = null;
        $values = null;

        foreach($registerVars as $key => $value){
            $names .= '`'.addslashes($key).'`,';
            $values .= '"'.addslashes(htmlspecialchars(trim($value))).'",';
        }

        $names = substr($names, 0, strlen($names)-1); // Обрезаем запятые на концах
        $values = substr($values, 0, strlen($values)-1); // Обрезаем запятые на концах

        $id = $this->insert('INSERT INTO `UserPrivate` '.$names.' VALUES ('.$values.')');

        return $id;
    }

    public function doLogin($login, $pass){
        $pass = md5($pass.SALT);
        $row = $this->select_one('SELECT * FROM `UserPrivate` WHERE `Login` = "'.addslashes($login).'" AND `passhash` = "'.$pass.'"');
        if(count($row) == 0)
            return false;
        else
            return $row;
    }

    public function getFeed($UserID){
        if(!$this->getUserInfo($UserID)){
            return array('ErrorUserNotFound');
        }

        $res = $this->select('SELECT * FROM `swites` WHERE `OwnerID` = "'.$UserID.'"');

        if(count($res)){
            $res[0] = 'ErrorFeedNotFound';
        }


    }

}