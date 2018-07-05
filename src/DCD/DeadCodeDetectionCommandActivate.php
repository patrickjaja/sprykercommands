<?php

namespace SprykerCli\DCD;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;

class DeadCodeDetectionCommandActivate extends AbstractCommand
{
    private $copy = 'docker cp';

    private $exec = 'docker exec -i';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('spryker:dcd:activate')
            ->setDescription('Activate DeadCode Collection');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->activate($output);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \SprykerCli\DCD\DeadCodeDetectionCommand
     */
    private function activate(OutputInterface $output): DeadCodeDetectionCommandActivate
    {
        $output->writeln($this->getFacade()->runShell($this->exec . ' php rm -rf /tmp/cov'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php mkdir /tmp/cov'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php chmod 777 /tmp/cov'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php mv /data/shop/development/current/public/Zed/index.php /data/shop/development/current/public/Zed/index.php.disabled'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php mv /data/shop/development/current/public/Yves/index.php /data/shop/development/current/public/Yves/index.php.disabled'));
        $output->writeln($this->getFacade()->runShell($this->copy . ' ./Zed php:/data/shop/development/current/public/Zed'));
        $output->writeln($this->getFacade()->runShell($this->copy . ' ./Yves php:/data/shop/development/current/public/Yves'));
        $output->writeln('DeadCodeDetection activated.');
        return $this;
    }
}
