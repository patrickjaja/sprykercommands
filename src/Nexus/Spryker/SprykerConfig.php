<?php


namespace Nexus\Spryker;

use Xervice\Config\XerviceConfig;
use Xervice\Core\Config\AbstractConfig;

class SprykerConfig extends AbstractConfig
{
    public const COMMAND_PATH = 'command.path';
    public const PHP_CONTAINER_NAME = 'php';

    /**
     * @return string
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function getCommandPath(): string
    {
        return $this->get(
            self::COMMAND_PATH,
            $this->get(XerviceConfig::APPLICATION_PATH) . '/commands'
        );
    }

    /**
     * @return string
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function getPhpContainerName(): string
    {
        return $this->get(
            self::PHP_CONTAINER_NAME,
            $this->get(self::PHP_CONTAINER_NAME)
        );
    }
}
