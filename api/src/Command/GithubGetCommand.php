<?php

// src/Command/GithubGetCommand.php

namespace App\Command;

use App\Entity\Component;
use App\Entity\Organisation;
use App\Service\GithubService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GithubGetCommand extends Command
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
        ->setName('app:github:get')
        // the short description shown while running "php bin/console list"
        ->setDescription('Gets github repositories')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command get all the repositories from github mentioning commonground.')
        ->setAliases(['app:github:getall'])
        ->setDescription('This command get all the repositories from github mentioning commonground.');
        //->addOption('location', null, InputOption::VALUE_OPTIONAL, 'Write output to files in the given location', '/srv/api/helm')
        //->addOption('spec-version', null, InputOption::VALUE_OPTIONAL, 'Helm version to use ("0.1.0")', '0.1.0');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $repositories = $this->githubService->findRepositoriesByFile('publiccode.yaml', "/");
        //var_dump($repositories);
//        $repositories = array_merge(
//        		$this->githubService->findRepositories('Common%20Ground'),
//        		$this->githubService->findRepositories('Commonground'),
//        		$this->githubService->findRepositories('commonground'),
//        		$this->githubService->findRepositories('common%20ground'),
//        		$this->githubService->findRepositories('vng'),
//        		$this->githubService->findRepositories('VNG'),
//
//        );

        $io->success(sprintf('Found %s repositories mentioning commonground.', count($repositories)));

        foreach ($repositories as $repository) {
        	// Lets see if we already have this repository
        	$components = $this->em->getRepository('App:Component')->findBy(['gitId'=>$repository['id']]);

        	// If we dont lets create one
        	if(!$components) {
        		$component = new Component();
        		$component->setCommonGround(false);
        		$feedback = 'Created';
        	}
        	else{
        		$component = $components[0];
        		$feedback = 'Updated';
        	}

        	// And then we can update it
            $component->setName($repository['name']);
            $component->setSummary($repository['description']);
            $component->setGit($repository['html_url']);
            $component->setGitType('github');
            $component->setGitId($repository['id']);
            $component->setUpdatedExternal(new \Datetime($repository['pushed_at']));
            $this->em->persist($component);
            $this->em->flush();
            $io->success(sprintf($feedback.' repository %s', $repository['name']));
        }

    }
}
