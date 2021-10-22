<?php
namespace xingwenge\multiprocess\Core;

use Swoole\Process;
use xingwenge\multiprocess\Common\Container;

class MasterSignal
{
    public static function registerTrigger()
    {
        Process::signal(SIGCHLD, function(){
            Container::instance()->get(MasterSigchld::class)->deal();
        });

        Process::signal(SIGTERM, function () {
            Container::instance()->get(MasterSigTerm::class)->deal();
        });
    }
}