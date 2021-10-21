<?php
namespace xingwenge\multiprocess\Common;

use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{
    public function __construct()
    {
        parent::__construct('multi-process', [
            new StreamHandler('php://stdout'),
            new StreamHandler('/logs/run.log'),

        ], [], null);
    }
}