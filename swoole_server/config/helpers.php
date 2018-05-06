<?php

//辅助函数

$config = require_once(CONFIG_PATH . 'config.php');


//读取配置文件
function getConfig($key){
    $key_arr = explode('.',$key);
    if(count($key_arr) > 1){
        return $config[$key_arr[0]][$key_arr[1]] ?? '';
    }
    return $config[$key] ?? '';
}
