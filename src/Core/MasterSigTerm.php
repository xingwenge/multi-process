<?php
namespace xingwenge\multiprocess\Core;

use DI\Annotation\Inject;

class MasterSigTerm extends MasterSignalBase
{
    /**
     * @Inject
     * @var Master
     */
    private $master;

    public function deal()
    {
        # stop worker.
        $this->master->stopWorkers();

        # stop master.
        $this->master->stopMaster();
    }
}