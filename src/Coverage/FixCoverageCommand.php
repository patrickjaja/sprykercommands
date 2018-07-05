<?php

namespace SprykerCli\Coverage;

use SimpleXMLElement;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;

class FixCoverageCommand extends AbstractCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('spryker:coverage:fix')
            ->setDescription('Fix Coverage XML Paths for PHPStorm inline coverage.')
            ->addArgument('path', InputArgument::OPTIONAL, "To your coverage.xml file", './coverage.xml');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fix($input, $output);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \SprykerCli\Install\InstallCommand
     */
    private function fix(InputInterface $input, OutputInterface $output): FixCoverageCommand
    {
        $xml = new SimpleXMLElement(file_get_contents($input->getArgument('path')));

        $result = $xml->xpath('/coverage/project//file/@name');

        foreach ($result as $filePath) {
            $filePath->name = str_replace('/', '\\', $filePath->name);
            $filePath->name = str_replace(
                "\data\shop\development\current\src",
                dirname(__FILE__ . '..' . DIRECTORY_SEPARATOR . '..') . "\current\src",
                $filePath->name
            );
        }
        $fileName = 'newFile' . time() . '.xml';
        $xml->asXML($fileName);
        $output->writeln('CoverageFile prepared for your IDE ' . $fileName . '.');
        return $this;
    }
}
