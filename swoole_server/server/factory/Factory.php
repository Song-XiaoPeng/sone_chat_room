<?php
namespace factory\tools;

class Factory {
    static public $instance;
    public function M(){
        if($instance !instanceof Factory){
            $instance = new Factory();
        }
        return $instance;
    }
}