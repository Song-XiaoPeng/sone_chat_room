<?php
$redis = new \Redis();      
$redis->connect(getConfig('redis'));
return $redis;