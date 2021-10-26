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

    public function exitBySignal($signal)
    {
        $masterPid = $this->getMasterPid();

        if ($masterPid) {
            if (Process::kill($masterPid, $signal)) {
                $this->logger->info('Send signal to Master', ['pid' => $masterPid, 'signal' => $signal]);
            }
            else {
                $this->logger->error('Send signal to Master failure!', ['pid' => $masterPid, 'signal' => $signal]);
            }
        }
        else {
            $this->logger->warning('Master is not running');
        }
    }

    public function stopWorkers()
    {
        foreach ($this->workerList->getWorkList() as $worker) {
            $workerPid = $worker->getPid();

            if ($workerPid && self::processIsExist($workerPid)) {
                if (true == Process::kill($workerPid)) {
                    $this->logger->info('Worker stopped', ['pid' => $workerPid, 'name' => $worker->getName()]);
                }
                else {
                    $this->logger->info('Worker stop failure!', ['pid' => $workerPid, 'name' => $worker->getName()]);
                }
            }
        }
    }

    public function stopMaster()
    {
        $masterPid = $this->getMasterPid();

        if (true == Process::kill($masterPid)) {
            @unlink($this->getMasterPidFile());

            $this->logger->info('Master stopped', ['pid' => $masterPid]);
        } else {
            $this->logger->info('Master stop failure!', ['pid' => $masterPid]);
        }

        exit();
    }

    public function quitWorkers()
    {
        foreach ($this->workerList->getWorkList() as $worker) {
            if ($workerPid = $worker->getPid()) {
                $signal = SIGQUIT;
                if (Process::kill($workerPid, $signal)) {
                    $this->logger->info('Send signal to Worker', ['pid' => $workerPid, 'signal' => $signal]);
                }
                else {
                    $this->logger->error('Send signal to Worker failure!', ['pid' => $workerPid, 'signal' => $signal]);
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