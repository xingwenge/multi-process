<?php
namespace xingwenge\multiprocess;

use DI\Annotation\Inject;
use xingwenge\multiprocess\Common\ConfigReader;
use xingwenge\multiprocess\Common\Container;
use xingwenge\multiprocess\Common\Logger;
use xingwenge\multiprocess\Core\Master;
use xingwenge\multiprocess\Core\Worker;
use xingwenge\multiprocess\Core\WorkerList;

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
     * @Inject
     * @var WorkerList
     */
    private $workerList;

    /**
     * @param $param
     * @return void
     */
    public function run($param)
    {
//        if (array_key_exists('h', $param)) {
//            return $this->printHelpMsg();
//        }

        if (isset($param['h'])) {
            return $this->printHelpMsg();
        }

        if (isset($param['s'])) {
            switch ($param['s']) {
                case 'start':
                    $this->initWorkerList();
                    return $this->master->startWorkerList($this->workerList);
            }
        }





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
Usage: php multi-process.php [options]

Options:
    -c 
    configuration yaml file.
    
    -h 
    Show this help, or workflow help for command.

    -s start 
    Start multi-process master and workers.





NAME
      php multi-process - manage multi-process.

SYNOPSIS
      php multi-process command [options]
          Manage multi-process daemons.


WORKFLOWS
      -h
      Show this help, or workflow help for command.

      -s restart
      Stop, then start multi-process master and workers.

      -s start 
      Start multi-process master and workers.
      -s start -c=./config
      Start multi-process with special config file.

      -s stop
      Wait all running workers smooth exit, please check multi-process status for a while.

      -s exit
      Kill all running workers and master.


EOF;
        echo $msg;
    }

    /**
     * @throws \Exception
     */
    private function initWorkerList()
    {
        $file = __DIR__. '/../Demo/process.yaml';

        $config = Container::get()->get(ConfigReader::class)->getSettingsByYaml($file);
        foreach ($config['programs'] as $k=>$v) {
            $worker = new Worker();
            $worker->setName($k);
            $worker->setBin($v['bin']??'');
            $worker->setBinArgs($v['binArgs']??[]);
            $worker->setLogger($this->logger);
            $this->workerList->addWorker($worker);
        }

        $this->workerList->checkWorkerList();
    }
}