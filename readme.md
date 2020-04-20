#### swoole task

#### 使用说明


## Quick Start
```
composer require easyswoole/easyswoole=3.x
php vendor/bin/hermes.php install
php bin/hermes.php start
```

## 配置说明

```
// /config.php
 如：$config = [
       'server_type' => \Hermes\Core\HermesApplication::TASK_SERVER,//swoole server 类型，目前仅支持TASK_SERVER类型
       'server_setting' => [],//swoole 设置
       'server_event' => ['prometheus\util\AjaxHandler'],//需要异步调用的任务，需异步调用的任务需继承Hermes\TaskServer\Contract\TaskEvent,并重写类常量const EVENT_NAME(taskEvent名称，必写),EVENT_CALLBACK_METHOD_MAP(异步任务回调设置，可不写)
       'server_params' => ['127.0.0.1', 9501, SWOOLE_BASE],//swoole服务器配置,host,port,模式
       'app_path' => dirname(__DIR__) //项目根目录
   ];
```

  
## command
```
php bin/hermes.php start  启动
php bin/hermes.php stop   停止
php bin/hermes.php restart  重启

```  
    
## 同步代码中开启异步任务
```
$task = new \Hermes\TaskServer\Task();
$taskEvent = 'test';//taskEvent 名称,即类常量EVENT_NAME的值
$taskMethod = 'test';//taskEvent 方法名称
$params = []; //方法参数
$task->async('queue', $taskEvent, $taskMethod, $params);
```