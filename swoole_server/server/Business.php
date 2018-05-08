<?php

use Medoo\Medoo;

class Business {
    protected $db;

    public function __construct($config) {
        $db_config = [
            'database_type' => $config['database_type'],
            'database_name' => $config['database_name'],
            'server' => $config['server'],
            'username' => $config['username'],
            'password' => $config['password'],
            'charset' => $config['charset']
        ];
    
        $this->db = new Medoo($db_config);
    }

    public function add($table,$data) 
    {
        $this->db->insert($table, $data);
        return $this->db->id();
    }

    public function delete() 
    {

    }

    public function get() 
    {

    }
}