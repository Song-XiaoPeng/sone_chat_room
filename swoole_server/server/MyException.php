<?php

//自定义异常处理类

class MyException extends Exception {

    public function __toString(){

        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function exceptionHandler($error)
    {
        (new MyLog()) -> writeLog($error);
    }
}