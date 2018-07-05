<?php

namespace Nexus\Spryker\Communication\Command\DCD;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;

class DeadCodeDetectionGenerateCommand extends AbstractCommand
{
    private $copy = 'docker cp';

    private $exec = 'docker exec -i';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('spryker:dcd:generate')
            ->setDescription('Generate DeadCode Report');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->pullImages($output);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \SprykerCli\Install\InstallCommand
     */
    private function pullImages(OutputInterface $output): DeadCodeDetectionCommand
    {
        $output->writeln($this->getFacade()->runShell($this->copy . ' ./Helper php:/data/shop/development'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php /data/shop/development/Helper/helper.php'));
        $output->writeln('DeadCodeDetection Report generated in your Container.');
        return $this;
    }
}
