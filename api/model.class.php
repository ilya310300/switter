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



}