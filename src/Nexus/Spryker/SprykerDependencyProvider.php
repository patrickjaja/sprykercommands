<?php


namespace Nexus\Spryker;


use Nexus\Spryker\Communication\Command\Console\ConsoleCommand;
use Nexus\Spryker\Communication\Command\Coverage\FixCoverageCommand;
use Nexus\Spryker\Communication\Command\DCD\DeadCodeDetectionActivateCommand;
use Nexus\Spryker\Communication\Command\DCD\DeadCodeDetectionDeactivateCommand;
use Nexus\Spryker\Communication\Command\DCD\DeadCodeDetectionGenerateCommand;
use Nexus\Spryker\Communication\Command\Install\InstallCommand;
use Nexus\Spryker\Communication\Command\Test\TestCommand;
use Xervice\Core\Dependency\DependencyProviderInterface;
use Xervice\Core\Dependency\Provider\AbstractProvider;

/**
 * @method \Xervice\Core\Locator\Locator getLocator()
 */
class SprykerDependencyProvider extends AbstractProvider
{
    const COMMAND_LIST = 'spryker.command.list';
    const SHELL_FACADE = 'shell.facade';

    /**
     * @param \Xervice\Core\Dependency\DependencyProviderInterface $container
     */
    public function handleDependencies(DependencyProviderInterface $container)
    {
        $container[self::COMMAND_LIST] = function(DependencyProviderInterface $container) {
            return $this->getCommandList();
        };
        $this->addShellFacade($container);
    }

    /**
     * @return array
     */
    protected function getCommandList()
    {
        return [
            new ConsoleCommand(),
            new FixCoverageCommand(),
            new DeadCodeDetectionActivateCommand(),
            new DeadCodeDetectionDeactivateCommand(),
            new DeadCodeDetectionGenerateCommand(),
            new InstallCommand(),
            new TestCommand(),
        ];
    }
    /**
     * @param \Xervice\Core\Dependency\DependencyProviderInterface $container
     */
    private function addShellFacade(DependencyProviderInterface $container): void
    {
        $container[self::SHELL_FACADE] = function(DependencyProviderInterface $container) {
            return $container->getLocator()->shell()->facade();
        };
    }
}