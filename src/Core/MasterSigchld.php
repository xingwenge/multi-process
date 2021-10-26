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
                // {pid:123,code:0,signal:0}
                // false
                // {"pid":298,"code":255,"signal":0} worker process program Fatal error.

                if ($ret && isset($ret['pid'])) {
                    $this->logger->info('Worker exit', $ret);

                    if ($ret['code']==0 && $ret['signal']==0) {
                        $worker = $this->workerList->getWorkerByPid($ret['pid']);
                        if ($worker) {
                            $worker->start();
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->logger->error('Deal worker exit error.', [$e->getMessage()]);
            }

            break;
        }
    }
}