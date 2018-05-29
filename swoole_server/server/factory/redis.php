<?php
$config = require_once(CONFIG_PATH . 'config.php');
$redis = new \Redis();
$redis->connect('127.0.0.1',6379);
return $redis;