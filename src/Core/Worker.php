<?php
namespace xingwenge\multiprocess\Core;

use Swoole\Process;
use xingwenge\multiprocess\Common\Container;
use xingwenge\multiprocess\Common\Logger;

class Worker
{
    /** @var string process name */
    private $name;

    /** @var string bin path */
    private $bin;

    /** @var array bin args */
    private $binArgs;

    /** @var int process id */
    private $pid;

    /** @var Process process obj */
    private $process;

    /** @var int of secs worker must stay up to be running */
    private $startSecs;

    /** @var int max of serial start failures when starting */
    private $startRetries;



    /** @var float startTime */
    private $startTime;

//    private $status; // ready | running | stopped | error

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $bin
     */
    public function setBin(string $bin)
    {
        $this->bin = $bin;
    }

    /**
     * @param array $binArgs
     */
    public function setBinArgs(array $binArgs)
    {
        $this->binArgs = $binArgs;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param int $startSecs
     */
    public function setStartSecs(int $startSecs): void
    {
        $this->startSecs = $startSecs;
    }

    /**
     * @param int $startRetries
     */
    public function setStartRetries(int $startRetries): void
    {
        $this->startRetries = $startRetries;
    }

    public function decrStartRetries()
    {
        $this->startRetries--;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBin(): string
    {
        return $this->bin;
    }

    /**
     * @return array
     */
    public function getBinArgs(): array
    {
        return $this->binArgs;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @return int
     */
    public function getStartSecs(): int
    {
        return $this->startSecs;
    }

    /**
     * @return int
     */
    public function getStartRetries(): int
    {
        return $this->startRetries;
    }

    /**
     * @return float
     */
    public function getStartTime(): float
    {
        return $this->startTime;
    }

    /**
     * 启动进程
     */
    public function start()
    {
        $bin = $this->bin;
        $binArgs = $this->binArgs;

        $this->process = new Process(function ($worker) use ($bin, $binArgs) {
            /** @var \Swoole\Process $worker */
            try {
                $worker->exec($bin, $binArgs);
            }  catch (\Throwable $e) {
                $this->logger->error('Process error', [$e->getMessage()]);
            }
        });

        $this->pid = $this->process->start();
        $this->startTime = microtime(true);

        Container::instance()->get(WorkerList::class)->updateWorker($this);

        $this->logger->info('Worker start', [
            'pid' => $this->pid,
            'name' => $this->name,
        ]);
    }
}