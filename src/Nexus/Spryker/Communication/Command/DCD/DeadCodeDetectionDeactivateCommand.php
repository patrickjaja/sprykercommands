<?php

namespace Nexus\Spryker\Communication\Command\DCD;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;

class DeadCodeDetectionDeactivateCommand extends AbstractCommand
{
    private $copy = 'docker cp';

    private $exec = 'docker exec -i';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('spryker:dcd:deactivate')
            ->setDescription('Deactivate DeadCode Collection');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->deactivate($output);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \SprykerCli\DCD\DeadCodeDetectionCommand
     */
    private function deactivate(OutputInterface $output): DeadCodeDetectionCommandDeactivate
    {
        $output->writeln($this->getFacade()->runShell($this->exec . ' php mv /data/shop/development/current/public/Zed/index.php.disabled /data/shop/development/current/public/Zed/index.php'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php mv /data/shop/development/current/public/Yves/index.php.disabled /data/shop/development/current/public/Yves/index.php'));
        $output->writeln('DeadCodeDetection activated.');
        return $this;
    }
}
