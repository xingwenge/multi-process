<?php
namespace xingwenge\multiprocess;

class WorkerList
{
    private $pidList;

    public function addWorker(Worker $worker)
    {
        $this->pidList[$worker->getPid()] = $worker;
    }

    public function getWorkerByPid($pid)
    {
        return isset($this->pidList[$pid])? $this->pidList[$pid]: null;
    }
}