<?php
namespace xingwenge\multiprocess;

use Monolog\Logger;

class Master
{
    private $logger;
    private $workerList;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->workerList = new WorkerList();
    }

    /**
     * 启动所有worker
     */
    public function startAll()
    {

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
        $worker = new Worker($this->logger);
        $worker->setName($name);
        $worker->setBin($bin);
        $worker->setBinArgs($binArgs);
        $worker->start();

        $this->workerList->addWorker($worker);
    }
}