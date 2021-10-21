<?php
namespace xingwenge\multiprocess\Common;

use Noodlehaus\Config;
use Noodlehaus\Parser\Yaml;

class ConfigReader
{
    /**
     * @var Config
     */
    private $config;

    public function __construct()
    {
        $file = __DIR__. '/../../Demo/process.yaml';

        $config = new Config(file_get_contents($file), new Yaml(), true);
        $this->config = $config;
    }

    public function getPrograms()
    {
        return $this->config->get('programs');
    }

    /**
     * @param $key
     *  workDir 运行目录
     * @return array | string | null
     */
    public function getSetting($key)
    {
        return $this->config->get('settings')[$key] ?? null;
    }

}