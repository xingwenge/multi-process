<?php
namespace xingwenge\multiprocess;

use DI\Annotation\Inject;
use xingwenge\multiprocess\Common\Logger;
use xingwenge\multiprocess\Core\Master;

class Dispatcher
{
    /**
     * @Inject
     * @var Logger
     */
    private $logger;

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
        if (isset($param['h'])) {
            $this->printHelpMsg();
            return;
        }

        if (isset($param['s'])) {
            switch ($param['s']) {
                case 'start':
                    $this->master->startAll();
                    return;
                case 'stop':
                    $this->master->stopAll();
                    return;
            }
        }

        $this->master->startAll();





        /*switch ($opt) {
            case 'start':
//                (new Master())->startAll();
                break;
            case 'stop':
//                (new Master())->stopAll(SIGTERM);
                break;
            case 'help':
            default:
                $this->printHelpMsg();
                break;
        }*/
    }

    private function printHelpMsg()
    {
        $msg=<<<EOF
Usage:
    php multi-process.php [options]

Options:
//    -c 
//    configuration yaml file.
    
    -h 
     Show this help, or workflow help for command.
    
    -s start 
     Start multi-process master and workers.
    
    -s stop
     Stop is a quick shutdown master and workers.
    
    -s quit
     Quit is a graceful shutdown. Multi-process send signal to worker and wait stop.


EOF;
        echo $msg;
    }
}