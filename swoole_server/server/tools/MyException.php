<?php
namespace server\tools;

//自定义异常处理类

class MyException extends Exception {

    public function __toString(){

        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getLogHandler()
    {
        return new MyLog();
    }

    public function exceptionHandler($error)
    {
        $this->getLogHandler()->writeLog($error);
    }
}