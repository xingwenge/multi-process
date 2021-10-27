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
        while (true) {
            try {
                # wait worker signal
                $ret = Process::wait(false);
                // {pid:123,code:0,signal:0}  exit normally.
                // false
                // {"pid":298,"code":255,"signal":0}  worker process program Fatal error.

                if ($ret) {
                    $this->logger->info('Worker signal', $ret);

                    if (isset($ret['pid'])) {
                        if ($ret['code']==0) {
                            $this->startWorker($ret['pid']);
                        }
                    }
                }

            } catch (\Exception $e) {
                $this->logger->error('Deal worker exit error.', [$e->getMessage()]);
            }

            break;
        }
    }

    private function startWorker($pid)
    {
        $worker = $this->workerList->getWorkerByPid($pid);

        if (!$worker) {
            return;
        }

        $timeDiff = microtime(true) - $worker->getStartTime();
        if ($timeDiff < $worker->getStartSecs()) {
            $worker->decrStartRetries();
        }

        if ($worker->getStartRetries()>0) {
            $worker->start();
        }
    }
}