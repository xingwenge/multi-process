<?php
$redis = new Redis();
$redis->connect('172.18.0.4', 6379);
echo "Connection to server successfully", PHP_EOL;

$key = 'queue';
$value = 'v-'. rand(100,999);
$redis->rPush($key, $value);
echo 'Push redis queue message:'. $value, PHP_EOL;

