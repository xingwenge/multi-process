<?php
$redis = new Redis();
$redis->connect('172.18.0.4', 6379);
echo "Connection to redis server successfully", PHP_EOL;

$key = 'queue';
while ($r = $redis->brPop($key, 10)) {
    echo 'GET redis queue message:', PHP_EOL;
    print_r($r);
    echo PHP_EOL;
}

echo 'Bye bye!', PHP_EOL;

