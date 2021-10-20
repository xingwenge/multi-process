<?php
namespace xingwenge\multiprocess;

class Logger extends \Monolog\Logger
{
    public function __construct()
    {
        parent::__construct('multi-process', [
            new \Monolog\Handler\StreamHandler('/logs/run.log')
        ], [], null);
    }
}