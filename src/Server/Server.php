<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 15:01
 */

namespace Hermes\Server;


use Hermes\Server\Contract\ServerInterface;
use Swoole\Process;
use Swoole\Server as CoServer;

/**
 * Class Server
 * @package Hermes\Server
 */
abstract class Server implements ServerInterface
{

    /**
     * Hermes server
     *
     * @var Server
     */
    private static $server;

    /**
     * Server type name. eg: http, ws, tcp ...
     *
     * @var string
     */
    protected static $serverType = 'TCP';

    /**
     * Default port
     *
     * @var int
     */
    protected $port = 80;

    /**
     * Default host address
     *
     * @var string
     */
    protected $host = '0.0.0.0';

    /**
     * Default mode type
     *
     * @var int
     */
    protected $mode = SWOOLE_BASE;

    /**
     * Default socket type
     *
     * @var int
     */
    protected $type = SWOOLE_SOCK_TCP;

    /**
     * Server setting for swoole. (@see swooleServer->setting)
     *
     * @link https://wiki.swoole.com/wiki/page/274.html
     * @var array
     */
    protected $setting = [];

    /**
     * The server unique name
     *
     * @var string
     */
    protected $pidName = 'Hermes';

    /**
     * Pid file
     *
     * @var string
     */
    protected $pidFile = 'runtime/Hermes.pid';

    /**
     * @var string
     */
    protected $commandFile = 'runtime/Hermes.command';

    /**
     * Record started server PIDs and with current workerId
     *
     * @var array
     */
    private $pidMap = [
        'masterPid' => 0,
        'managerPid' => 0,
        // if = 0, current is at master/manager process.
        'workerPid' => 0,
        // if < 0, current is at master/manager process.
        'workerId' => -1,
    ];

    /**
     * Server event for swoole event
     *
     * @var array
     *
     * @example
     * [
     *     'serverName' => new SwooleEventListener(),
     *     'serverName' => new SwooleEventListener(),
     *     'serverName' => new SwooleEventListener(),
     * ]
     */
    protected $on = [];

    /**
     * Add port listener
     *
     * @var array
     * @example
     * [
     *    'name' => ServerInterface,
     *    'name2' => ServerInterface,
     * ]
     */
    protected $listener = [];

    /**
     * Add process
     *
     * @var array
     *
     * @example
     * [
     *     'name' => UserProcessInterface,
     *     'name2' => UserProcessInterface,
     * ]
     */
    protected $process = [];

    /**
     * Script file
     *
     * @var string
     */
    protected $scriptFile = '';

    /**
     * Swoole Server
     *
     * @var CoServer|\Swoole\Http\Server|\Swoole\Websocket\Server
     */
    protected $swooleServer;

    /**
     * Debug level
     *
     * @var integer
     */
    private $debug = 0;

    /**
     * Server id
     *
     * @var string
     */
    private $mangerPid = '';

    /**
     * Server unique id
     *
     * @var string
     */
    private $uniqid = '';

    /**
     * @var string
     */
    private $fullCommand = '';

    /**
     * @var string
     */
    public const RESPONSE_LOG = '/tmp/log/response.log';

    /**
     *
     */
    protected function startSwoole(): void
    {
    }

    /**
     * @param $host
     * @param $port
     * @param $mode
     * @param $type
     */
    public function init($host, $port, $mode, $type = SWOOLE_SOCK_TCP)
    {
        $this->host = $host;
        $this->port = $port;
        $this->mode = $mode;
        $this->type = $type;
    }

    /**
     * Shutdown server
     */
    public function stop(): bool
    {
        $pid = $this->getPid();
        Process::kill((int)$pid[0], 15);
        usleep(100000);
        return true;
    }


    /**
     * manger pid,worker pid
     * @return array
     */
    public function getPid(): array
    {
        $pidStr = file_get_contents(HERMES_ROOT . '/' . $this->pidFile);
        return explode(',', $pidStr);
    }

    /**
     * manger pid,worker pid
     * @param $mangerPid
     * @param $workerPid
     * @return void
     */
    public function setPid($mangerPid, $workerPid): void
    {
        $filePath = HERMES_ROOT . '/runtime';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777);
            chmod($filePath, 0777);
        }
        file_put_contents(HERMES_ROOT . '/' . $this->pidFile, $mangerPid . ',' . $workerPid);
    }

}