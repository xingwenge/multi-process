<?php
require_once __DIR__. '/../vendor/autoload.php';

use xingwenge\multiprocess\Common\Container;
use xingwenge\multiprocess\Dispatcher;

// php7 Demo/demo.php -h
// php7 Demo/demo.php -s start

try {
    $param = getopt('s:c:h');

//    print_r($param);exit;

    $dispatcher = Container::instance()->get(Dispatcher::class);
    $dispatcher->run($param);
} catch (\Exception $e) {
    echo $e->getMessage(), PHP_EOL;
}