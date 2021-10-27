<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;

/**
 * deal worker stop. when master process get stop signal, shutdown all worker process.
 */
class MasterSigUsr2
{
    /**
     * @Inject
     * @var Master
     */
    private $master;

    public function deal()
    {
        # stop worker.
        $this->master->stopWorkers(SIGKILL);

        # stop master.
        $this->master->stopMaster(SIGKILL);
    }
}