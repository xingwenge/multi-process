<?php
namespace xingwenge\multiprocess\Core;

use Swoole\Process;
use xingwenge\multiprocess\Common\Container;
use xingwenge\multiprocess\Common\Logger;
use function DI\get;

class Worker
{
    /** @var string 进程名称 */
    private $name;

    /** @var string 进程路径 */
    private $bin;

    /** @var array 进程参数 */
    private $binArgs;

    /** @var int 进程pid */
    private $pid;

    /** @var Process 进程对象 */
    private $process;

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

        $workerList = Container::instance()->get(WorkerList::class);
        $workerList->updateWorkerPid($this);

        $this->logger->info('Worker start', [
            'pid' => $this->pid,
            'name' => $this->name,
        ]);
    }
}