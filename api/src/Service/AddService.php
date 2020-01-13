<?php

// src/Service/AmbtenaarService.php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AddService
{
    private $params;
    private $cash;
    private $markdown;
    private $em;

    private $gitlab;
    private $github;
    private $bitbucket;
    private $api;

    public function __construct(
            ParameterBagInterface $params,
            MarkdownParserInterface $markdown,
            CacheInterface $cache,
            EntityManagerInterface $em,

            GithubService $github,
            GitlabService $gitlab,
            BitbucketService $bitbucket,
            ApiService $api)
    {
        $this->params = $params;
        $this->cash = $cache;
        $this->markdown = $markdown;
        $this->em = $em;

        $this->gitlab = $gitlab;
        $this->github = $github;
        $this->bitbucket = $bitbucket;
        $this->api = $api;
    }

    public function add($url)
    {
        /*@todo this should have an error catch mechanism */
        $parse = parse_url($url);
        $host = $parse['host'];

        switch ($host) {
            case 'github.com':
                $responce = $this->github->get($url);
                break;
            case 'gitlab.com':
                $responce = $this->gitlab->get($url);
                break;
            case 'bitbucket.com':
                $responce = $this->bitbucket->get($url);
                break;
            default:
                $responce = $this->api->get($url);
        }

        // If we have a valid reponce we presumably want to save it to the database
        if ($responce) {
            $this->em->persist($responce);
            $this->em->flush();
        }

        return $responce;
    }
}
