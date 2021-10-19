<?php
namespace xingwenge\multiprocess;

class Dispatcher
{
    public function run($opt)
    {
        switch ($opt) {
            case 'start':
                (new Master())->startAll();
                break;
            case 'stop':
                (new Master())->stopAll(SIGTERM);
                break;
//            case 'stop':
//                (new Sun())->stop(SIGTERM);
//                break;
            case 'help':
            default:
                $this->printHelpMsg();
                break;
        }
    }

    private function printHelpMsg()
    {
        $msg=<<<EOF
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