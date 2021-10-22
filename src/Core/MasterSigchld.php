<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;
use Swoole\Process;
use xingwenge\multiprocess\Common\Logger;

class MasterSigchld extends MasterSignalBase
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
     * deal worker exist. when child process stop or exit, parent process will get.
     */
    public function deal()
    {
        while (true) {
            try {
                # wait worker signal
                $ret = Process::wait(false); // {pid:123,code:0,signal:0} | false

                if ($ret && isset($ret['pid'])) {
                    $this->logger->info('Worker exist', $ret);

                    if ($ret['code']==0 && $ret['signal']==0) {
                        $worker = $this->workerList->getWorkerByPid($ret['pid']);
                        if ($worker) {
                            $worker->start();
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->logger->error('Deal worker exist error.', [$e->getMessage()]);
            }

            break;
        }
    }
}