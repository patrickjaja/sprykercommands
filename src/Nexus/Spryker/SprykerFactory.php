<?php


namespace Nexus\Spryker;


use Xervice\Core\Factory\AbstractFactory;

/**
 * @method \Nexus\Spryker\SprykerConfig getConfig()
 */
class SprykerFactory extends AbstractFactory
{
    /**
     * @return array
     */
    public function getCommandList()
    {
        return $this->getDependency(SprykerDependencyProvider::COMMAND_LIST);
    }
}