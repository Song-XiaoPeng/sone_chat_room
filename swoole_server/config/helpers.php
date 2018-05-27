<?php

//辅助函数


//读取配置文件
function getConfig($key){
    $config = require_once(CONFIG_PATH . 'config.php');
    $key_arr = explode('.',$key);
    if(count($key_arr) > 1){
        return $config[$key_arr[0]][$key_arr[1]] ?? '';
    }
    return $config[$key] ?? '';
}
