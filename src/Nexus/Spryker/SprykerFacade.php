<?php


namespace Nexus\Spryker;

use Xervice\Core\Facade\AbstractFacade;

/**
 * @method \Nexus\Spryker\SprykerFactory getFactory()
 * @method \Nexus\Spryker\SprykerConfig getConfig()
 * @method \Nexus\Spryker\SprykerClient getClient()
 */
class SprykerFacade extends AbstractFacade
{
    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->getFactory()->getCommandList();
    }

    /**
     * @param string $command
     *
     * @return string
     */
    public function runShell(string $command): string
    {
        return $this->getFactory()->getShellFacade()->runCommand($command);
    }
}
