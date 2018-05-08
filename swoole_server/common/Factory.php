<?php

use Medoo\Medoo;

class Factory
{
    public function makeModule($moduleName,$moduleOptions)
    {
        switch($moduleName){
            case 'db':
                return new Medoo($moduleOptions);
            case 'server':
                return new swoole_websocket_server($moduleOptions[0],$moduleOptions[1]);
        }



    }
}