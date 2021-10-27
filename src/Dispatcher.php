<?php
namespace xingwenge\multiprocess;

use DI\Annotation\Inject;
use xingwenge\multiprocess\Core\Master;

class Dispatcher
{
    /**
     * @Inject
     * @var Master
     */
    private $master;

    /**
     * @param $param
     * @throws \Exception
     */
    public function run($param)
    {
        if (isset($param['s'])) {
            switch ($param['s']) {
                case 'quit':
                    $this->master->exitBySignal(SIGUSR1);
                    return;
                case 'stop':
                    $this->master->exitBySignal(SIGUSR2);
                    return;
            }
        }

        echo 'hello world', PHP_EOL;
    }

    public static function printHelpMsg()
    {
        $msg=<<<EOF
Usage:
    php multi-process.php [options]

Options:
    -h 
     Show this help, or workflow help for command.
     
    -c <file>
     config yaml file.
     
    -s quit
     Quit is a graceful shutdown. The master send signal to worker and wait stop.
    
    -s stop
     Stop is a quick shutdown master and workers.


EOF;
        echo $msg;
    }
}