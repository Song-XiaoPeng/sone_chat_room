<?php

namespace server\common;

class BaseStorage
{
    protected $modules = [];

    public function __construct()
    {
        $this->initModule();
    }

    protected function initModule()
    {
        $this->db = $this->loadModule('db');
        $this->redis = $this->loadModule('redis');
    }

    public function __get($property)
    {
        $methods = 'get' . $property;
        if (method_exists($this, $property)) {
            return $this->$methods();
        }
    }

    public function loadModule($module)
    {
        if (empty($this->modules[$module])) {
            //延迟加载
            $this->modules[$module] = include(SERVER_PATH . 'factory/' . $module . '.php');
        }
        return $this->modules[$module];
    }
}