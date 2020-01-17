<?php

// src/Command/GithubGetCommand.php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml; 

use App\Service\GithubService;
use App\Entity\Component;

class GithubCheckCommand extends Command
{
	private $githubService; 
	private $em;

	public function __construct(GithubService $githubService, EntityManagerInterface $em)
    {
    	$this->githubService= $githubService;
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
        ->setHelp('This command checks all not het checked github repositories. And determins if they are commonground components')
        ->setDescription('This command checks all not het checked github repositories. And determins if they are commonground components');
        //->addOption('location', null, InputOption::VALUE_OPTIONAL, 'Write output to files in the given location', '/srv/api/helm')
        //->addOption('spec-version', null, InputOption::VALUE_OPTIONAL, 'Helm version to use ("0.1.0")', '0.1.0');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $repositories = $this->em->getRepository('App:Component')->findBy(["checked"=>null]);
			
		$io->success(sprintf('Found %s repositories to be checked.', count($repositories)));
		$now = New \Datetime;
		
		foreach($repositories as $repository){ 
			$repository->setChecked($now);			
			
			// Lets see if we already have this repro
			if($publicCode = $this->githubService->checkPublicCode($repository)){
				$repository->setCommonground(true); 
				$repository->setPubliccode(Yaml::parse($publicCode));
				
				$io->success(sprintf('Repository %s is a common ground repository', $repository->getName()));
				$this->em->persist($repository);
				continue;
			}
			
			$repository->setCommonground(false);
			$io->warning(sprintf('Repository %s is not a common ground repository', $repository->getName()));
			
			$this->em->persist($repository);
		}	
		
		$this->em->flush();
		
		
    }
}
