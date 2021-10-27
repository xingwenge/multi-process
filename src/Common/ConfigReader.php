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

    /**
     * @param $file
     * @return $this
     * @throws \Exception
     */
    public function setConfigByYaml($file)
    {
        if (!file_exists($file)) {
            throw new \Exception('Can not find config file. '. $file);
        }

        $config = new Config(file_get_contents($file), new Yaml(), true);
        $this->config = $config;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function checkConfig()
    {
        if (!isset($this->config['programs'])) {
            throw new \Exception('Config can not read programs.');
        }

        if (!isset($this->config['settings'])) {
            throw new \Exception('Config can not read error.');
        }

        return $this;
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