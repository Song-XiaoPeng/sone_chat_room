<?php
use Medoo\Medoo;

class Test
{
    public function __construct()
    {
        $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'timeline',
            'server' => 'localhost',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8'
        ]);
        echo '<pre>';
        var_dump($database->select('user','*'));
    }
}