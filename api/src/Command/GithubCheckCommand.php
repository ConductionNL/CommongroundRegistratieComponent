<?php

// src/Command/GithubGetCommand.php

namespace App\Command;

use App\Entity\Component;
use App\Service\GithubService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class GithubCheckCommand extends Command
{
    private $githubService;
    private $em;

    public function __construct(GithubService $githubService, EntityManagerInterface $em)
    {
        $this->githubService = $githubService;
        $this->em = $em;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('app:github:check')
        // the short description shown while running "php bin/console list"
        ->setDescription('Checks all not het checked github repositories')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command checks all not het checked github repositories. And determines if they are commonground components')
        ->setDescription('This command checks all not het checked github repositories. And determines if they are commonground components')
        ->addOption('component', null, InputOption::VALUE_OPTIONAL, 'update a specific component');
        //->addOption('spec-version', null, InputOption::VALUE_OPTIONAL, 'Helm version to use ("0.1.0")', '0.1.0');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $component = $input->getOption('component');

        // Lets see if we have a component id and transform it into a component opbject
        if ($component && $component = $this->em->getRepository('App:Component')->find($component)) {
            $component = $this->githubService->checkComponent($component);

            $io->text(sprintf('Component %s has been checked.', $component->getName()));
            //$io->text(var_dump($component->getCommonground()));
            $this->em->persist($component);
            $this->em->flush();

            // @todo die mag nooit naar prod
            die;
        }

        // Lets find the components to be updated
        $components = $this->em->getRepository('App:Component')->findCheckable();
        //$components = $this->em->getRepository('App:Component')->findAll();

        $io->success(sprintf('Found %s repositories to be checked.', count($components)));
        $now = new \Datetime();

        $processes = [];

        foreach ($components as $component) {
            $io->text(sprintf('starting update for component %s (%s).', $component->getName(), $component->getId()));

            $process = new Process(['bin/console', 'app:github:check', '--component', $component->getId()]);
            //$process->run();
            // start() doesn't wait until the process is finished, oppose to run()
            $process->start();
            //echo $process->getOutput();

            //$io->success(sprintf('Component %s (%s) has been updated.', $component->getName(),$component->getId()));
            // store process for later, so we evaluate it's finished
            $processes[] = $process;
        }

        // Lets wait until everything finishes
        while (count($processes)) {
            $io->success(sprintf('Currently running %s repositories checks.', count($processes)));

            foreach ($processes as $i => $runningProcess) {
                // specific process is finished, so we remove it
                if (!$runningProcess->isRunning()) {
                    unset($processes[$i]);

                    if (!$runningProcess->isSuccessful()) {
                        $io->error($runningProcess->getErrorOutput());
                    } else {
                        $io->success($runningProcess->getOutput());
                    }
                }

                // check every second
                sleep(1);
            }
        }
        // here we know that all are finished

        $io->success(sprintf('Checked %s repositories.', count($components)));
    }
}
