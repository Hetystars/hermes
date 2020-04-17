#### swoole task

#### 使用说明

1.添加配置文件

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

  
2.启动swoole 服务
```
$configFilePath = dirname(__DIR__).'/config.php';//配置文件路径
$swoole = new \Hermes\Core\HermesApplication();
$swoole->iniConfig($configFilePath)
    ->run();
```  
    
3.同步代码中开启异步任务
```
$task = new \Hermes\TaskServer\Task('127.0.0.1', 9501);//swoole服务器配置,host,port,模式，配置文件中server_params ,host,port
$taskEvent = 'test';//taskEvent 名称,即类常量EVENT_NAME的值
$taskMethod = 'test';//taskEvent 方法名称
$params = []; //方法参数
$task->async('queue', $taskEvent, $taskMethod, $params);
```