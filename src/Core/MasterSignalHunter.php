<?php
namespace xingwenge\multiprocess\Core;

use Swoole\Process;
use xingwenge\multiprocess\Common\Container;

class MasterSignalHunter
{
    public static function register()
    {
        Process::signal(SIGCHLD, function(){
            Container::instance()->get(MasterSigchld::class)->deal();
        });

        Process::signal(SIGTERM, function () {
            Container::instance()->get(MasterSigTerm::class)->deal();
        });

//        Process::signal(SIGQUIT, function () {
//            Container::instance()->get(MasterSigQuit::class)->deal();
//        });

        Process::signal(SIGUSR1, function () {
            Container::instance()->get(MasterSigQuit::class)->deal();
        });
    }
}