<?php

namespace Nexus\CustomCommand\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;

class SyncCommand extends AbstractCommand
{
    private $pull = 'docker pull';
    private $volume = 'docker volume create';
    private $build = 'docker build';
    private $run = 'docker run';
    private $copy = 'docker cp';
    private $rm = 'docker rm';
    private $compose = 'docker-compose';
    private $timeout = 'timeout 5 > NULL';
    private $exec = 'docker exec -i';
    private $stop = 'docker stop';

    private $stores = ['DE','NL'];

    protected function configure()
    {
        $this->setName('spryker:lekkerland:install')
            ->setDescription('Install automation for lekkerland');
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
        $this->pullImages($input, $output);
        $this->createVolumes($input, $output);
        $this->buildLocalImages($input, $output);
        $this->pushCode($input, $output);
        $this->startContainer($input, $output);
        $this->prepareInstallApp($input, $output);
        $this->addRabbitMqUsers($input, $output);
        $this->restoreServiceData($input, $output);
        $this->installApp($input, $output);
        $this->installationComplete($input, $output);
    }

    private function pullImages(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getFacade()->runShell($this->pull . ' nexusnetsoft/spryker-php-fpm:7.1'));
        $output->writeln($this->getFacade()->runShell($this->pull . ' nexusnetsoft/jenkins-php:7.1'));
    }

    private function createVolumes(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getFacade()->runShell($this->volume . ' data-sync'));
        $output->writeln($this->getFacade()->runShell($this->volume . ' projectdata'));
        $output->writeln($this->getFacade()->runShell($this->volume . ' dbdata'));
        $output->writeln($this->getFacade()->runShell($this->volume . ' elasticdata'));
        $output->writeln($this->getFacade()->runShell($this->volume . ' redisdata'));
    }

    private function buildLocalImages(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getFacade()->runShell($this->build . ' ./env -t nxs-docker-dumper'));
    }

    private function pushCode(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getFacade()->runShell($this->run . ' data-sync:/data/shop/development --name helper busybox true'));
        $output->writeln($this->getFacade()->runShell($this->copy . ' . helper:/data/shop/development'));
        $output->writeln($this->getFacade()->runShell($this->rm . ' docker rm helper'));
    }

    private function startContainer(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getFacade()->runShell($this->compose . ' -f docker-compose.yaml -f docker-local.yaml up -d'));
        $output->writeln($this->getFacade()->runShell($this->timeout));
        $output->writeln($this->getFacade()->runShell($this->compose . ' -f docker-compose.yaml -f docker-local.yaml up -d'));
    }

    private function prepareInstallApp(InputInterface $input, OutputInterface $output) {
        foreach ($this->stores as $store) {
            $output->writeln($this->getFacade()->runShell($this->exec . ' php sh -c "export PGPASSWORD=mate20mg && dropdb -h db -p 5432 -U spryker '.$store.'_development_zed"'));
        }
        $output->writeln($this->getFacade()->runShell($this->exec . ' php composer global require hirak/prestissimo'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php composer install --prefer-dist'));
    }

    private function addRabbitMqUsers(InputInterface $input, OutputInterface $output) {
        foreach ($this->stores as $store) {
            $output->writeln($this->getFacade()->runShell($this->exec . ' rabbitmq rabbitmqctl add_vhost /'.$store.'_development_zed'));
            $output->writeln($this->getFacade()->runShell($this->exec . ' rabbitmq rabbitmqctl add_user '.$store.'_development mate20mg'));
            $output->writeln($this->getFacade()->runShell($this->exec . ' rabbitmq rabbitmqctl set_user_tags '.$store.'_development administrator'));
            $output->writeln($this->getFacade()->runShell($this->exec . ' rabbitmq rabbitmqctl set_permissions -p /'.$store.'_development_zed '.$store.'_development ".*" ".*" ".*"'));
        }
    }

    private function restoreServiceData(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getFacade()->runShell($this->stop . ' db && docker stop redis && docker stop elasticsearch'));
        $output->writeln($this->getFacade()->runShell($this->run . ' --rm -v projectdata:/data -e SSHHOST=5.9.82.139 -e SSHUSER=nxsdocker -e ENGINE=ssh -e PROJECT=$PROJECTNAME -e VERSION=project nxs-docker-dumper restore'));
        $output->writeln($this->getFacade()->runShell($this->run . ' --rm -v dbdata:/var/lib/postgresql/data -e SSHHOST=5.9.82.139 -e SSHUSER=nxsdocker -e ENGINE=ssh -e PROJECT=$PROJECTNAME -e VERSION=db -e DATAPATH=/var/lib/postgresql/data nxs-docker-dumper restore'));
        $output->writeln($this->getFacade()->runShell($this->run . ' --rm -v redisdata:/data -e SSHHOST=5.9.82.139 -e SSHUSER=nxsdocker -e ENGINE=ssh -e PROJECT=$PROJECTNAME -e VERSION=redis nxs-docker-dumper restore'));
        $output->writeln($this->getFacade()->runShell($this->run . ' --rm -v elasticdata:/usr/share/elasticsearch/data -e SSHHOST=5.9.82.139 -e SSHUSER=nxsdocker -e ENGINE=ssh -e PROJECT=$PROJECTNAME -e VERSION=elasticsearch -e DATAPATH=/usr/share/elasticsearch/data nxs-docker-dumper restore'));
    }

    private function installApp(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getFacade()->runShell($this->exec . ' php php /data/shop/development/current/vendor/bin/install'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php chmod -Rf 0777 /data/shop/development/current/data'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php ln -s /data/shop/development/current/vendor/bin/codecept /usr/local/bin/codecept'));
        $output->writeln($this->getFacade()->runShell($this->exec . ' php ln -s /data/shop/development/current/vendor/bin/console /usr/local/bin/console'));
        foreach ($this->stores as $store) {
            if ($store !== 'DE') {
                $output->writeln($this->getFacade()->runShell($this->exec . ' php sh -c "export PGPASSWORD=mate20mg && createdb -h db -p 5432 -U spryker -T DE_development_zed '.$store.'_development_zed"'));
            }
        }
    }

    private function installationComplete(InputInterface $input, OutputInterface $output) {
        $output->writeln('Now add this host entries (if you dont have already)');
        foreach ($this->stores as $store) {
            $output->writeln('127.0.0.1 zed.'.strtolower($store).'.suite.local');
            $output->writeln('127.0.0.1 www.'.strtolower($store).'.suite.local');
        }
    }

}