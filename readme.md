English | [中文](./README-CN.md)

### Async Task In FPM

[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg?maxAge=2592000)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.4.1-brightgreen.svg?maxAge=2592000)](https://github.com/swoole/swoole-src)
[![Hermes License](https://img.shields.io/hexpm/l/plug.svg?maxAge=2592000)](https://github.com/Hetystars/hermes/blob/master/LICENSE)



### Instructions


### Requirement

- [PHP 7.1+](https://github.com/php/php-src/releases)
- [Swoole 4.4.1+](https://github.com/swoole/swoole-src/releases)
- [Redis 3.2.0+](https://pecl.php.net/package/redis)
- [Composer](https://getcomposer.org/)

### Quick Start
```
composer require async-task/hermes
php vendor/async-task/Hermes/bin/hermes.php install
php bin/hermes start
```

### Configuration Instruction

#### Configuration File
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
#### Configuration Params Detail

> server_type: `swoole server type，only support task sever`

+ \Hermes\Core\HermesApplication::TASK_SERVER

> server_setting: `swoole setting`

+ task_worker_num,task worker num
+ worker_num, worker num
+ log_file, swoole log 
+ log_level, swoole log level
+ daemonize, daemon or not
+ response_file, response log

> server_event: `async task event map，need to extend Hermes\TaskServer\Contract\TaskEvent,and rewrite class const EVENT_NAME(taskEvent name，required),EVENT_CALLBACK_METHOD_MAP(async task callback,non-required)`

+ class map
  
> server_params: `swoole swoole server setting。depend on redis extension，only use the redis socket connect，notice the redis server is not a real redis server`
  
  + host,server host
  + port, server port
  
### Command
```
php bin/hermes start  start the server
php bin/hermes stop   stop the server
php bin/hermes restart  restart the server

```  
    
### Start Async Task
```
$config = [];
$task = new \Hermes\TaskServer\Task($config['server_params']['host'],$config['server_params']['port']);
$taskEvent = 'testEvent';//taskEvent name,that's the value of EVENT_NAME
$taskMethod = 'test';//taskEvent function name
$params = []; //taskEvent function params
$task->async($taskEvent, $taskMethod, $params);
```