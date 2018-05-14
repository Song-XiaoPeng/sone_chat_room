<?php
namespace server\tools;

//自定义日志记录类

class MyLog {

    protected $_prefix = '';

    public function writeLog($log){
        $base_dir = date('Y-m') . '/';
        $base_file_name = date('d');

        $dir_name = LOG_PATH . $base_dir;
        
        if(!is_dir($dir_name)){
            mkdir($dir_name,0777,true);
        }

        $file_name = $this->_prefix . $base_file_name . '.log';
        $current_time = date("Y-m-d H:i:s");
        $log = '【'.$current_time. '】 '. $log .PHP_EOL;
        file_put_contents($dir_name . $file_name, $log,FILE_APPEND);
    }
}