<?php
namespace xingwenge\multiprocess\Common;

use Noodlehaus\Config;
use Noodlehaus\Parser\Yaml;

class ConfigReader
{
    /**
     * @param string $settingsYaml
     * @return Config
     * @throws \Exception
     */
    public function getSettingsByYaml(string $settingsYaml)
    {
        if (!file_exists($settingsYaml)) {
            throw new \Exception('The yaml file not exist. %s', $settingsYaml);
        }

        return new Config(file_get_contents($settingsYaml), new Yaml(), true);
    }
}