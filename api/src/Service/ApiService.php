<?php

// src/Service/AmbtenaarService.php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

class ApiService
{
    private $params;
    private $cash;
    private $markdown;
    private $em;
    private $gitlab;
    private $github;
    private $bitbucket;
    private $user;

    public function __construct(
        ParameterBagInterface $params,
        MarkdownParserInterface $markdown,
        CacheInterface $cache,
        EntityManagerInterface $em,
        GithubService $github,
        GitlabService $gitlab,
        BitbucketService $bitbucket,
        Security $security
    )
    {
        $this->params = $params;
        $this->cash = $cache;
        $this->markdown = $markdown;
        $this->em = $em;
        $this->gitlab = $gitlab;
        $this->github = $github;
        $this->bitbucket = $bitbucket;
        $this->user = $security->getUser();
    }

    public function get($url)
    {
    }
}
