<?php
namespace xingwenge\multiprocess\Core;

class WorkerList
{
    /**
     * @var Worker[]
     */
    private $workList = [];

    /**
     * @return Worker[]
     */
    public function getWorkList(): array
    {
        return $this->workList;
    }

    /**
     * @var Worker[]
     */
//    private $pidList = [];

    public function addWorker(Worker $worker)
    {
        $this->workList[] = $worker;
//        $this->pidList[$worker->getPid()] = $worker;
    }

    public function checkWorkerList()
    {
        $errors = [];

        foreach ($this->workList as $worker) {
            if (!$worker->getBin()) {
                $errors[] = sprintf('The worker %s bin is not set', $worker->getName());
            }
        }

        if ($errors) {
            throw new \Exception(['The config has error.', $errors]);
        }
    }

    /*public function getWorkerByPid($pid)
    {
        return isset($this->pidList[$pid])? $this->pidList[$pid]: null;
    }*/
}