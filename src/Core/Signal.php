<?php
namespace xingwenge\multiprocess\Core;

class Signal
{
    private $signals = [
        1 => 'SIGHUP',
        2 => 'SIGINT',
        3 => 'SIGQUIT',
        9 => 'SIGKILL',
        15 => 'SIGTERM',
        17 => 'SIGCHLD',
        19 => 'SIGSTOP',
        20 => 'SIGTSTP',
        18 => 'SIGCONT',
        10 => 'SIGUSR1',
        12 => 'SIGUSR2',
    ];

    public function getShowName($signal)
    {
        if (isset($this->signals[$signal])) {
            return sprintf('%s(%s)', $signal, $this->signals[$signal]);
        }
        else {
            return $signal;
        }
    }
}