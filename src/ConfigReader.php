<?php
namespace xingwenge\multiprocess;

use Noodlehaus\Config;
use Noodlehaus\Parser\Yaml;

class ConfigReader
{
    public function getSettingsByYaml($settingsYaml)
    {
        return new Config($settingsYaml, new Yaml(), true);
    }
}