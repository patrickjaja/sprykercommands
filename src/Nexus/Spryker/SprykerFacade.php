<?php


namespace Nexus\Spryker;


use Xervice\Core\Facade\AbstractFacade;

/**
 * @method \Nexus\SprykerInstall\SprykerInstallFactory getFactory()
 * @method \Nexus\SprykerInstall\SprykerInstallConfig getConfig()
 * @method \Nexus\SprykerInstall\SprykerInstallClient getClient()
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
}