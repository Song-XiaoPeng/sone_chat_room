<?php

require_once "../vendor/autoload.php";
$config = require_once('../config/config.php');

new Swoole($config);