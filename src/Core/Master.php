<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;
use xingwenge\multiprocess\Common\Logger;

class Master
{
    /**
     * @Inject
     * @var Logger
     */
    private $logger;

    /**
     * @Inject
     * @var WorkerList
     */
    private $workerList;

    /**
     * 启动所有worker
     */
    public function startWorkerList(WorkerList $workerList)
    {
        foreach ($this->workerList->getWorkList() as $worker) {
            $worker->start();
        }
    }

    /**
     * 停止所有进程
     */
    public function stopAll($signal)
    {

    }

    public function restartByName($name)
    {

    }

    private function startWorker($name, $bin, $binArgs)
    {
        $worker = new Worker();
        $worker->setName($name);
        $worker->setBin($bin);
        $worker->setBinArgs($binArgs);
        $worker->start();

        $this->workerList->addWorker($worker);
    }
}