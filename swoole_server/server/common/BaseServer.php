<?php

namespace server\common;

use server\tools\MyLog;

class BaseServer
{
    protected $modules = [];
    protected $log;
    protected $config;

    public function __construct()
    {
        $this->initModule();
    }

    protected function initModule()
    {
        //加载配置
        $this->config = $this->loadConfig();

        //加载日志处理对象
        $this->log = new MyLog();

        //注册自定义异常处理函数
        set_exception_handler([$this, 'Log']);
    }

    public function __get($property)
    {
        $methods = 'get' . $property;
        if (method_exists($this, $property)) {
            return $this->$methods();
        }
    }

    protected function loadConfig()
    {
        return require_once(CONFIG_PATH . '/config.php');
    }

    public function Log($error){
        $this->log->writeLog($error);
    }
}