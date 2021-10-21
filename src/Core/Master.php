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
     * @Inject
     * @var WorkerList
     */
    private $workerList;

    /**
     * 启动所有worker
     * @param string $yamlFile
     * @throws \Exception
     */

    /**
     * 启动所有worker
     * @param string $yamlFile
     * @throws \Exception
     */
    public function startAll(string $yamlFile)
    {
        $config = Container::get()->get(ConfigReader::class)->getSettingsByYaml($yamlFile);

        foreach ($config['programs'] as $k=>$v) {
            $worker = new Worker();
            $worker->setName($k);
            $worker->setBin($v['bin']??'');
            $worker->setBinArgs($v['binArgs']??[]);
            $worker->setLogger($this->logger);
            $this->workerList->addWorker($worker);
        }

        $this->workerList->checkWorkerList();

        foreach ($this->workerList->getWorkList() as $worker) {
            $worker->start();
        }
    }
}