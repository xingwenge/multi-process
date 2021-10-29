# multi-process
基于swoole实现的多进程管理器，类似于supervisor.

# 为什么要写multi-process
## php业务服务器不用搭建其他多进程服务
能够基于php的环境实现多进程管理，而不另外搭建其他多进程管理服务

## php长进程存在的风险
基于php实现的长进程脚本，大部分业务是基于I/O的消息阻塞。

程序运行中存在一些隐患
- 程序不当或者扩展带来的内存泄漏，长时间运行会导致内存崩溃
- php程序依赖的服务代码更新
解决这个隐患的一个有效办法是能够定时重启进程。 
如在I/O的消息阻塞的程序中，设置阻塞超时时间，阻塞超时后退出进程，通过multi-process再次启动程序达到自动重启。

# 引入
composer require xingwenge/multi-process

# 依赖
- swoole

# 示例程序
配置文件
```./bin/demo.yaml```

```php
settings:
  workDir: /logs/multi-process          # 工作目录，存放进程id、日志

programs:                               
  Demo:                                 # 进程名称
    bin: /usr/local/php7/bin/php        # 进程运行路径
    binArgs:                            # 参数
      - /data/www/overseas_online/src/Console/console.php
      - App\Console\Firehose\Demo
    startSecs: 3                        # 进程运行最小时长
    startRetries: 3                     # 程序运行失败重试的最大次数

```

启动
```./bin/multiprocessd -c ./bin/demo.yaml```

平滑结束
```./bin/multiprocessctl -c ./bin/demo.yaml -s quit```

停止
```./bin/multiprocessctl -c ./bin/demo.yaml -s stop```