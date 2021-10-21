<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;
use xingwenge\multiprocess\Common\ConfigReader;
use xingwenge\multiprocess\Common\Container;
use xingwenge\multiprocess\Common\Logger;

class Master
{
    /**
     * @Inject
     * @var Logger
     */
    private $logger;

    /**
     * @var ConfigReader
     */
    private $config;

    /**
     * @Inject
     * @var WorkerList
     */
    private $workerList;

    /**
     * @var string 工作目录
     */
    private $workDir;

    /**
     * @var int 主进程id
     */
    private $masterPid;

    public function __construct(ConfigReader $config)
    {
        $this->config = $config;

        # workdir.
        $this->workDir = $this->config->getSetting('workDir');

        if (!file_exists($this->workDir)) {
            mkdir($this->workDir, 0777, true);
        }
    }

    /**
     * 启动所有worker
     * @param string $yamlFile
     * @throws \Exception
     */
    public function startAll()
    {
        # master run check.
        $masterPidFile = $this->getMasterPidFile();
        if (file_exists($masterPidFile)) {
            $masterPid = file_get_contents($masterPidFile);

            if ($masterPid && $this->processIsExist($masterPid)) {
                throw new \Exception(sprintf('[Pid: %s] Master is running. Please stop or restart.', $masterPid));
            }
        }

        # run as daemon.
        \Swoole\Process::daemon();

        # save master pid.
        $this->masterPid = getmypid();
        file_put_contents($masterPidFile , $this->masterPid);

        $this->logger->info('Master start.', [
            'pid' => $this->masterPid,
        ]);

        # todo: signal







        # worker list.
        $config = $this->config->getPrograms();

        foreach ($config as $k=>$v) {
            $worker = new Worker();
            $worker->setName($k);
            $worker->setBin($v['bin']??'');
            $worker->setBinArgs($v['binArgs']??[]);
            $worker->setLogger($this->logger);
            $this->workerList->addWorker($worker);
        }

        $this->workerList->checkWorkerList();

        # start worker.
        foreach ($this->workerList->getWorkList() as $worker) {
            $worker->start();
        }
    }

    private function getMasterPidFile()
    {
        return $this->workDir . '/master.pid';
    }

    /**
     * @param $pid
     * @return bool
     */
    private function processIsExist($pid)
    {
        return @\Swoole\Process::kill($pid, 0);
    }
}