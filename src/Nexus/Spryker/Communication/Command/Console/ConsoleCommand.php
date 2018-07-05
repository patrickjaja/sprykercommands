<?php

namespace Nexus\Spryker\Communication\Command\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;

class ConsoleCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('spryker:console')
            ->setDescription('test command')
            ->addArgument('command', InputArgument::OPTIONAL, "Spryker Console Command Name", ' -h');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     * @throws \Core\Locator\Dynamic\ServiceNotParseable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getFacade()->runShell('docker exec -i php console '.$input->getArgument('command')));
    }

}