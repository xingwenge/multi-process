<?php
namespace xingwenge\multiprocess\Core;

class WorkerList
{
    /**
     * @var Worker[]
     */
    private $workList = [];

    /**
     * @var array {pid:worker}
     */
    private $mapPid = [];

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
        $key = md5($worker->getName());
        $this->workList[$key] = $worker;
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

    public function updateWorkerPid(Worker $worker)
    {
        $key = md5($worker->getName());
        $this->mapPid[$worker->getPid()] = $key;
    }

    public function getWorkerByPid($pid)
    {
        if (isset($this->mapPid[$pid])) {
            if (isset($this->workList[$this->mapPid[$pid]])) {
                return $this->workList[$this->mapPid[$pid]];
            }
        }

        return null;
    }
}