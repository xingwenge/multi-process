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
        $this->workList[$worker->getPid()] = $worker;
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

    public function updateWorker(Worker $worker, $oldPid)
    {
        $this->workList[$worker->getPid()] = $worker;
        unset($this->workList[$oldPid]);
    }

    public function getWorkerByPid($pid)
    {
        if (isset($this->workList[$pid])) {
            return $this->workList[$pid];
        }

        return null;
    }
}