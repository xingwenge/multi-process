<?php
namespace xingwenge\multiprocess\Core;

use Swoole\Process;
use xingwenge\multiprocess\Common\Container;

class MasterSignalHunter
{
    public static function register()
    {
        # 子进程退出信号
        Process::signal(SIGCHLD, function(){
            Container::instance()->get(MasterSigchld::class)->deal();
        });

        # 进程 quit
        Process::signal(SIGUSR1, function () {
            Container::instance()->get(MasterSigUsr1::class)->deal();
        });

        # 进程 stop
        Process::signal(SIGUSR2, function () {
            Container::instance()->get(MasterSigUsr2::class)->deal();
        });
    }
}