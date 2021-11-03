<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;
use Swoole\Process;
use xingwenge\multiprocess\Common\Logger;

/**
 * deal worker exit. when worker process stop or exit, parent master process will get.
 */
class MasterSigchld
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

    public function deal()
    {
        while ($ret = Process::wait(false)) {
            // {pid:123,code:0,signal:0}  exit normally.
            // {"pid":298,"code":255,"signal":0}  worker process program Fatal error.

            $this->logger->info('Worker signal', $ret);

            if ($ret['pid'] && $ret['code']==0) {
                $this->restartWorker($ret['pid']);
            }
            else {
                $this->logger->error('Deal worker exit error.', $ret);
                break;
            }
        }
    }

    private function restartWorker($oldPid)
    {
        $worker = $this->workerList->getWorkerByPid($oldPid);

        if (!$worker) {
            $this->logger->error('Can not find worker.', [$oldPid, $this->workerList]);
            return;
        }

        $this->logger->info('Worker signal info.', ['pid' => $oldPid, 'name' => $worker->getName()]);

        $timeDiff = microtime(true) - $worker->getStartTime();
        if ($timeDiff < $worker->getStartSecs()) {
            $worker->decrStartRetries();
        }

        if ($worker->getStartRetries()>0) {
            $worker->start();
        }
    }
}