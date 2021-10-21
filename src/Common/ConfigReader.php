<?php
namespace xingwenge\multiprocess\Common;

use Noodlehaus\Config;
use Noodlehaus\Parser\Yaml;

class ConfigReader
{
    public function getSettingsByYaml($settingsYaml)
    {
        if (!file_exists($settingsYaml)) {
            throw new \Exception('The yaml file not exist. %s', $settingsYaml);
        }

        return new Config(file_get_contents($settingsYaml), new Yaml(), true);
    }
}