<?php
$redis = new Redis();
//$redis->connect('127.0.0.1', 6379);
$redis->connect('51talk_redis', 6379);
echo "Connection to server successfully", PHP_EOL;

$key = 'queue';
$value = 'v-'. rand(100,999);
$redis->rPush($key, $value);
echo 'Push redis queue message:'. $value, PHP_EOL;

