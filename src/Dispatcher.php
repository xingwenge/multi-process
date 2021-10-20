<?php
namespace xingwenge\multiprocess;

class Dispatcher
{
    private $logger;
    private $master;

    public function __construct(Logger $logger, Master $master)
    {
        $this->logger = $logger;
        $this->master = $master;
    }

    /**
     * @param $param
     */
    public function run($param)
    {
//        if (array_key_exists('h', $param)) {
//            return $this->printHelpMsg();
//        }

        if (isset($param['s'])) {
            switch ($param['s']) {
                case 'start':
                    return $this->master->startAll();
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
Usage: php /usr/bin/supervisord [options]

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
}