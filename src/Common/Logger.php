<?php
namespace xingwenge\multiprocess\Common;

class Logger extends \Monolog\Logger
{
    public function __construct()
    {
        parent::__construct('multi-process');
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}