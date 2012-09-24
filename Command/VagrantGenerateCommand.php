<?php

namespace EC\Bundle\VagrantBundle\Command;

use EC\Bundle\VagrantBundle\Generator\VagrantGenerator;
use EC\Bundle\VagrantBundle\Repository\BoxRepository;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VagrantGenerateCommand extends ContainerAwareCommand
{
    protected $boxRepository;

    protected $generator;

    protected function configure()
    {
        $this
            ->setName('generate:vagrant')
            ->setDescription('Interactively generate Vagranfile configuration')
            ->setDefinition(array(
                new InputOption('host', '', InputOption::VALUE_REQUIRED, 'Hostname of VM'),
                new InputOption('ip',   '', InputOption::VALUE_REQUIRED, 'Local IP address of VM'),
                new InputOption('box',  '', InputOption::VALUE_REQUIRED, 'Name of Vagrant box image'),
                new InputOption('url',  '', InputOption::VALUE_OPTIONAL, 'URL of Vagrant box image'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!trim(`which vagrant`)) {
            throw new \Exception('Vagrant cannot be found.  Is it in your PATH?');
        }

        exec('vagrant', $testVagrantOutput, $error);

        if ($error) {
            throw new \Exception('There is an issue running Vagrant.  Check your Vagrantfile for errors.');
        }

        $host   = Validators::validateHost($input->getOption('host'));
        $ip     = Validators::validateIp($input->getOption('ip'));
        $url    = Validators::validateUrl($input->getOption('url'));

        if (!$input->getOption('box')) {
            $input->setOption('box', pathinfo($url, PATHINFO_FILENAME));
        }

        $box = Validators::validateBox($input->getOption('box'));

        $generated = $this->getGenerator()->generate(getcwd(), compact('host', 'ip', 'box', 'url'));

        foreach ($generated as $file) {
            $output->writeln(sprintf('Generated <info>%s</info>', $file));
        }
    }

    protected function getBoxRepository()
    {
        if (null === $this->boxRepository) {
            $this->boxRepository = new BoxRepository();
        }

        return $this->boxRepository;
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

    protected function getGenerator()
    {
        if (null === $this->generator) {
            $this->generator = new VagrantGenerator(__DIR__.'/../Resources/skeleton');
        }

        return $this->generator;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Symfony2 Vagrantfile generator');

        $output->writeln(array(
            '',
            'The Vagrantfile is used by Vagrant to install a VM for local development',
            'and its dependencies.',
            '',
        ));

        // Host
        $output->writeln(array(
            '',
            'First, your VM should have a hostname',
            '',
        ));

        $defaultHost    = $input->getOption('host') ?: 'vagrant';
        $host           = $dialog->ask($output, $dialog->getQuestion('Vagrant Hostname', $defaultHost), $defaultHost);

        $input->setOption('host', $host);

        // IP
        $output->writeln(array(
            '',
            'Second, your VM will need a local IP address.',
            'You may override the default provided.',
            '',
        ));
        $defaultIp  = $input->getOption('ip') ?: VagrantGenerator::generateIp();
        $ip         = $dialog->askAndValidate($output, $dialog->getQuestion('Vagrant IP address', $defaultIp), function($ip) {
            return Validators::validateIp($ip);
        }, false, $defaultIp);

        $input->setOption('ip', $ip);

        // Box Name/URL
        $output->writeln(array(
            '',
            'Next, your VM will need a starter box image to build off of.',
            'You may choose an existing box on your system, enter a URL,',
            'or leave it blank to get a list of recommended boxes.',
            ''
        ));

        $repo   = $this->getBoxRepository();
        $boxes  = $repo->findLocal();

        if ($boxes) {
            $output->writeln("Existing Vagrant boxes:\n");
        }

        $choices = array();

        do {
            if ($boxes) {
                $choices = array_keys($boxes);

                foreach ($choices as $i => $choice) {
                    $output->writeln(sprintf('[%s] <info>%s</info>', $i + 1, $choice));
                }

                $output->writeln('');
            }

            $box = $dialog->askAndValidate($output, '<info>Box Number, Name or URL</info> [<comment>ENTER to list boxes</comment>]: ', function($box) use ($boxes) {
                return $box === null ? false : Validators::validateBox($box, $boxes);
            });

            if (!$box) {
                $output->writeln('');

                $boxes = $repo->findAll();
            }
        } while (!$box);

        $input->setOption('box', $box);

        $output->writeln('');
    }
}
