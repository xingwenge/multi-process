<?php
namespace xingwenge\multiprocess;

use Monolog\Logger;
use Swoole\Process;

class Worker
{
    /** @var string 进程名称 */
    private $name;

    /** @var string 进程路径 */
    private $bin;

    /** @var array 进程参数 */
    private $binArgs;

    /** @var int 进程pid */
    private $runPid;

    /** @var Process 进程对象 */
    private $process;

    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

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
                echo $e->getMessage(), PHP_EOL;
            }
        });

        $this->pid = $this->process->start();

        $this->logger->info('Process start', [
            'name' => $this->name,
            'Pid' => $this->pid,
        ]);

        return $this->pid;
    }
}