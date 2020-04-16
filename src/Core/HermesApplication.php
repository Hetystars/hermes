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
     * @var
     */
    protected $processor;


    /**
     * @var int
     */
    protected $serverType;


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
            $this->getProcessor()->handle($this->serverType);
        } catch (Throwable $e) {
            echo $e->getTraceAsString(), "\n";
        }
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
     * @return bool
     */
    protected function beforeRun()
    {
        return true;
    }

    /**
     * @return ApplicationProcessor
     */
    protected function getProcessor()
    {
        return new ApplicationProcessor();
    }
}