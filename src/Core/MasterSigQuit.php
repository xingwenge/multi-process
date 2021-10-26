<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;

/**
 * deal worker quit. when master process get quit signal, close all worker process graceful.
 */
class MasterSigQuit
{
    /**
     * @Inject
     * @var Master
     */
    private $master;

    public function deal()
    {
        # stop worker.
        $this->master->quitWorkers();

        # stop master.
//        $this->master->stopMaster();
    }
}