<?php
namespace xingwenge\multiprocess\Common;

use DI\ContainerBuilder;

class Container
{
    /**
     * 获取容器
     * @return \DI\Container
     */
    public static function instance()
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