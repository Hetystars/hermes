中文 | [English](./README.md)

### FPM中调用异步任务

[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg?maxAge=2592000)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.4.1-brightgreen.svg?maxAge=2592000)](https://github.com/swoole/swoole-src)
[![Hermes License](https://img.shields.io/hexpm/l/plug.svg?maxAge=2592000)](https://github.com/Hetystars/hermes/blob/master/LICENSE)



### 说明


### 依赖

- [PHP 7.1+](https://github.com/php/php-src/releases)
- [Swoole 4.4.1+](https://github.com/swoole/swoole-src/releases)
- [Redis 3.2.0+](https://pecl.php.net/package/redis)
- [Composer](https://getcomposer.org/)

### 快速开始
```
composer require async-task/hermes
php vendor/async-task/Hermes/bin/hermes install
php bin/hermes start
```

### 配置文件说明

#### 配置文件
```
APP_PATH/hermes_config.php
  [
       'server_type' => \Hermes\Core\HermesApplication::TASK_SERVER,
       'server_setting' => [
            'task_worker_num' => 4,
            'worker_num' => 1,
            'log_file' => '/tmp/swoole.log',
            'log_level' => SWOOLE_LOG_NOTICE,
            'daemonize' => 1,
            'response_file' => '/tmp/response.log'
            ],
       'server_event' => [
             prometheus\util\AjaxHandler::class
           ],
       'server_params' => [
            'host'=>'127.0.0.1',
            'port'=> 9501 
            ],
  ];
```
#### 配置参数细节

> server_type: `swoole server 类型，仅支持 task sever`

+ \Hermes\Core\HermesApplication::TASK_SERVER

> server_setting: `swoole 设置`

+ task_worker_num,task worker num
+ worker_num, worker num
+ log_file, swoole log 
+ log_level, swoole log level
+ daemonize, 是否守护进程
+ response_file, response log

> server_event: `异步任务事件，需要继承实现Hermes\TaskServer\Contract\TaskEvent,且重写类常量  EVENT_NAME(taskEvent name，必需),EVENT_CALLBACK_METHOD_MAP(异步任务回调，非必需)`

+ class map
  
> server_params: `swoole swoole server 设置。依赖 redis 扩展，仅利用redis socket连接，注意这不是一个真正的Redis服务器`
  
  + host,server host
  + port, server port
  
### Command
```
php bin/hermes start  开启服务
php bin/hermes stop   停止服务
php bin/hermes restart  重启服务

```  
    
### Start Async Task
```
$taskEvent = 'testEvent';//taskEvent 名称,EVENT_NAME的值
$taskMethod = 'test';//taskEvent 方法名称
$params = []; //taskEvent 方法参数
Hermes\TaskServer\Task::async($taskEvent, $taskMethod, $params);
```