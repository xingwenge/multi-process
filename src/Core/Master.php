<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;
use Swoole\Process;
use xingwenge\multiprocess\Common\ConfigReader;
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
     * @Inject
     * @var Signal
     */
    private $signal;

    /**
     * @var string 工作目录
     */
    private $workDir;

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
    public function start()
    {
        # master run check.
        $masterPid = $this->getMasterPid();
        if ($masterPid && self::processIsExist($masterPid)) {
            throw new \Exception(sprintf('[Pid: %s] Master is running. Please stop or restart.', $masterPid));
        }


        # run as daemon.
        \Swoole\Process::daemon();


        # save master pid.
        $this->masterPid = getmypid();
        $this->setMasterPid($this->masterPid);


        # logger.
        $this->logger->info('Master start.', [
            'pid' => $this->masterPid,
        ]);


        # hunter master signal.
        MasterSignalHunter::register();


        # worker list.
        $config = $this->config->getPrograms();
        foreach ($config as $k=>$v) {
            $bin = $v['bin']? $v['bin']: [];
            $binArgs = $v['binArgs']? $v['binArgs']: [];
            $startSecs = $v['startSecs']? $v['startSecs']: 3;
            $startRetries = $v['startRetries']? $v['startRetries']: 3;

            $worker = new Worker();
            $worker->setName($k);
            $worker->setBin($bin);
            $worker->setBinArgs($binArgs);
            $worker->setLogger($this->logger);
            $worker->setStartSecs($startSecs);
            $worker->setStartRetries($startRetries);
            $this->workerList->addWorker($worker);
        }
        $this->workerList->checkWorkerList();


        # start worker.
        foreach ($this->workerList->getWorkList() as $worker) {
            $worker->start();
        }
    }

    public function exitBySignal($signal)
    {
        $masterPid = $this->getMasterPid();

        if ($masterPid) {
            if (Process::kill($masterPid, $signal)) {
                $this->logger->info('Send signal to Master', ['pid' => $masterPid, 'signal' => $this->signal->getShowName($signal)]);
            }
            else {
                $this->logger->error('Send signal to Master failure!', ['pid' => $masterPid, 'signal' => $this->signal->getShowName($signal)]);
            }
        }
        else {
            $this->logger->warning('Master is not running');
        }
    }

    public function stopMaster($signal)
    {
        $masterPid = $this->getMasterPid();

        if (true == Process::kill($masterPid, $signal)) {
            @unlink($this->getMasterPidFile());

            $this->logger->info('Master deal signal', ['pid' => $masterPid, 'signal' => $this->signal->getShowName($signal)]);
        } else {
            $this->logger->info('Master deal signal failure!', ['pid' => $masterPid, 'signal' => $this->signal->getShowName($signal)]);
        }

        exit();
    }

    public function stopWorkers($signal)
    {
        foreach ($this->workerList->getWorkList() as $worker) {
            $workerPid = $worker->getPid();

            if ($workerPid && self::processIsExist($workerPid)) {
                if (true == Process::kill($workerPid, $signal)) {
                    $this->logger->info('Worker deal signal', ['pid' => $workerPid, 'name' => $worker->getName(), 'signal' => $this->signal->getShowName($signal)]);
                }
                else {
                    $this->logger->info('Worker deal signal failure!', ['pid' => $workerPid, 'name' => $worker->getName(), 'signal' => $this->signal->getShowName($signal)]);
                }
            }
        }
    }

    /**
     * The process is exist.
     * @param $pid
     * @return bool
     */
    private static function processIsExist($pid): bool
    {
        if (@Process::kill($pid, 0)) {
            return true;
        }
        else {
            return false;
        }
    }

    private function getMasterPidFile()
    {
        return $this->workDir . '/master.pid';
    }

    private function getMasterPid()
    {
        if (!$this->masterPid) {
            $masterPidFile = $this->getMasterPidFile();
            if (file_exists($masterPidFile)) {
                $this->masterPid = intval(file_get_contents($masterPidFile));
            }
        }

        return $this->masterPid;
    }

    private function setMasterPid($masterPid)
    {
        if ($masterPid) {
            file_put_contents($this->getMasterPidFile(), $masterPid);
        }
    }
}