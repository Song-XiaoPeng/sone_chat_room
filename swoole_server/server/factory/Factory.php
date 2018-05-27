<?php
namespace server\factory;

class Factory {
    static public $instance;
    public static function M(){
        if(!(self::$instance instanceof self)){
            $instance = new Factory();
        }
        return $instance;
    }
}