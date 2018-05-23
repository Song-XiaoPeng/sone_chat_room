<?php
namespace server\tools;
use Medoo\Medoo;

class DAO {
    static public $dao;

    private function __construct(){}
    private function __clone(){}

    static public function getSingleTon(){
        if(!(self::$dao instanceof Medoo)){
            $config = self::loadConfig();
            self::$dao = new Medoo($config['mysql']);
        }
        return self::$dao;
    }

    static public function loadConfig()
    {
        return require_once(CONFIG_PATH . '/config.php');
    }
}