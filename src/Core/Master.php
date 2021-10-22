<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;
use Swoole\Process;
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

            if ($masterPid && self::processIsExist($masterPid)) {
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



        # deal worker exist.
        Process::signal(SIGCHLD, function(){
            self::dealWorkerExist();
        });


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
    private static function processIsExist($pid)
    {
        return @Process::kill($pid, 0);
    }

    private static function dealWorkerExist()
    {
        $logger = Container::instance()->get(Logger::class);
        $workerList = Container::instance()->get(WorkerList::class);

        while (true) {
            try {
                $ret = Process::wait(false); // {pid:123,code:0,signal:0} | false

                if ($ret && isset($ret['pid'])) {
                    $logger->info('Worker exist', $ret);

                    if ($ret['code']==0 && $ret['signal']==0) {
                        $worker = $workerList->getWorkerByPid($ret['pid']);
                        if ($worker) {
                            $worker->start();
                        }
                    }
                }
            } catch (\Exception $e) {
                $logger->error('Deal worker exist error.', [$e->getMessage()]);
            }

            break;
        }
    }
}