#### swoole task

#### 使用说明


## Quick Start
```
composer require async-task/hermes
php vendor/bin/hermes install
php bin/hermes start
```

## 配置说明

```
// /config.php
 如：$config = [
       'server_type' => \Hermes\Core\HermesApplication::TASK_SERVER,//swoole server 类型，目前仅支持TASK_SERVER类型
       'server_setting' => [
            'task_worker_num' => 4,
            'worker_num' => 1,
            'log_file' => '/tmp/swoole.log',
            'log_level' => SWOOLE_LOG_NOTICE,
            'daemonize' => 1,
            'enable_coroutine' => false,
            'response_file' => '/tmp/response.log'
],//swoole 设置
       'server_event' => ['prometheus\util\AjaxHandler'],//异步调用的任务，需继承Hermes\TaskServer\Contract\TaskEvent,并重写类常量const EVENT_NAME(taskEvent名称，必写),EVENT_CALLBACK_METHOD_MAP(异步任务回调设置，可不写)
       'server_params' => [
            'host'=>'127.0.0.1',
            'port'=> 9501,
            'type'=> SWOOLE_BASE
        ],//swoole服务器配置,host,port,模式
   ];
```

  
## command
```
php bin/hermes start  启动
php bin/hermes stop   停止
php bin/hermes restart  重启

```  
    
## 同步代码中开启异步任务
```
$config = [];
$task = new \Hermes\TaskServer\Task($config['server_params']['host'],$config['server_params']['port']);
$taskEvent = 'testEvent';//taskEvent 名称,即类常量EVENT_NAME的值
$taskMethod = 'test';//taskEvent 方法名称
$params = []; //方法参数
$task->async($taskEvent, $taskMethod, $params);
```