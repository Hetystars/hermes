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
     * @var string
     */
    protected $appPath;

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
     * HermesApplication constructor.
     */
    public function __construct()
    {

    }


    /**
     * Run application
     */
    public function run(): void
    {
        try {
            if (!$this->beforeRun()) {
                return;
            }
            $this->iniServer()
                ->getProcessor()
                ->handle($this->serverType, $this->serverParams, $this->serverEvents, $this->setting, $this->appPath);
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
            ->setAppPath($this->config['app_path'])
            ->setServerSetting($this->config['server_setting']);
    }

    /**
     * @param string $configFile
     * @return $this
     */
    public function iniConfig(string $configFile)
    {
        require_once $configFile;
        $this->config = $config;
        return $this;
    }

    /**
     * @param $appPath
     * @return $this
     */
    public function setAppPath($appPath): self
    {
        $this->appPath = $appPath;
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
    protected function beforeRun()
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