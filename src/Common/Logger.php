<?php
namespace xingwenge\multiprocess\Common;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{
    public function __construct()
    {
        // the default date format is "Y-m-d\TH:i:sP"
        $dateFormat = "Y-m-d H:i:s";

        // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        // we now change the default output format according our needs.
        $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

        // finally, create a formatter
        $formatter = new LineFormatter($output, $dateFormat);

        $streamConsole = new StreamHandler('php://stdout');
        $streamConsole->setFormatter($formatter);

        $streamFile = new StreamHandler('/logs/run.log');
        $streamFile->setFormatter($formatter);

        parent::__construct('multi-process', [
            $streamConsole,
            $streamFile,
        ], [], new \DateTimeZone('Asia/Shanghai'));
    }
}