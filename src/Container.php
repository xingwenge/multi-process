<?php
namespace xingwenge\multiprocess;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class Container
{
    /**
     * 获取容器
     * @return \DI\Container
     */
    public static function get()
    {
        static $container;

        if( !$container ){
            $builder = new ContainerBuilder();
            $builder->addDefinitions([
                # 定义配置
                # 定义对象
                /*'Logger' => function () {
                    $logger = new \Monolog\Logger('multi-process');
                    $logger->pushHandler(new \Monolog\Handler\StreamHandler('/logs/run.log'));
                    return $logger;
                },*/
            ]);
            $builder->useAnnotations(true);

            $container = $builder->build();
        }

        return $container;
    }
}