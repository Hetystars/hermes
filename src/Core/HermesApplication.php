<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Hety <Hetystars@gmail.com>
 * Date: 16/04/2020
 * Time: 16:59
 */

namespace Hermes\Core;

use Hermes\Core\Processor\ApplicationProcessor;
use Hermes\TaskServer\TaskServer;
use Throwable;

/**
 * Class HermesApplication
 * @package Hermes\Core
 */
class HermesApplication
{
    /**
     * [
     *  'server_type' => \Hermes\Core\HermesApplication::TASK_SERVER,
     *  'server_setting' => [],
     *  'server_event' => ['prometheus\util\AjaxHandler'],
     *  'server_params' => ['127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP],
     *  'app_path' => '/var/http/www/as-project/'
     * ]
     * @var
     */
    protected $config;

    /**
     * @var
     */
    protected $processor;

    /**
     * @var int
     */
    protected $serverType;

    /**
     * @var array
     */
    protected $serverParams;

    /**
     * @var array
     */
    protected $serverEvents;

    /**
     * @var array
     */
    protected $setting;


    /**
     * @var
     */
    public const
        TASK_SERVER = 1;

    /**
     * @var array
     */
    public const
        TASK_SERVER_MAP = [
        self::TASK_SERVER => TaskServer::class
    ];

    /**
     * @var string
     */
    public const
        COMMAND_INSTALL = 'install',
        COMMAND_START = 'start',
        COMMAND_RESTART = 'restart',
        COMMAND_STOP = 'stop';

    /**
     * @var array
     */
    public const
        COMMAND_MAP = [
        self::COMMAND_INSTALL,
        self::COMMAND_START,
        self::COMMAND_RESTART,
        self::COMMAND_STOP
    ];

    /**
     * HermesApplication constructor.
     */
    public function __construct()
    {

    }


    /**
     * @param array $params
     */
    public function run(array $params = []): void
    {
        $command = $params[0];
        if (!in_array($command, static::COMMAND_MAP, false)) {
            echo 'unsupported command ' . $command;
            return;
        }
        try {
            if (!$this->beforeRun()) {
                return;
            }
            $this->iniConfig()
                ->iniServer()
                ->getProcessor()
                ->handle($command, $this->serverType, $this->serverParams, $this->serverEvents, $this->setting);
        } catch (Throwable $e) {
            echo $e->getMessage(), PHP_EOL,
            $e->getTraceAsString(), PHP_EOL;
        }
        echo 'server start success';
    }

    /**
     * @return HermesApplication
     */
    private function iniServer()
    {
        return $this->setServerEvents($this->config['server_event'])
            ->setServerType($this->config['server_type'])
            ->setServerParams($this->config['server_params'])
            ->setServerSetting($this->config['server_setting']);
    }

    /**
     * @param string $configFile
     * @return $this
     */
    public function iniConfig()
    {
        $this->config = require HERMES_ROOT . 'config.php';
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setServerType($type): self
    {
        $this->serverType = $type;
        return $this;
    }

    /**
     * @param $serverParams
     * @return $this
     */
    public function setServerParams($serverParams): self
    {
        $this->serverParams = $serverParams;
        return $this;
    }


    /**
     * @param $serverEvents
     * @return $this
     */
    public function setServerEvents($serverEvents): self
    {
        $this->serverEvents = $serverEvents;
        return $this;
    }

    /**
     * @param $setting
     * @return $this
     */
    public function setServerSetting($setting): self
    {
        $this->setting = $setting;
        return $this;
    }

    /**
     * @return bool
     */
    protected function beforeRun(): bool
    {
        return true;
    }

    /**
     * @return ApplicationProcessor
     */
    protected function getProcessor(): ApplicationProcessor
    {
        return new ApplicationProcessor();
    }
}