<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;

/**
 * deal worker quit. when master process get quit, shutdown all worker process graceful.
 */
class MasterSigUsr1
{
    /**
     * @Inject
     * @var Master
     */
    private $master;

    public function deal()
    {
        # stop worker.
        $this->master->stopWorkers(SIGTERM);

        # stop master.
        $this->master->stopMaster(SIGTERM);
    }
}