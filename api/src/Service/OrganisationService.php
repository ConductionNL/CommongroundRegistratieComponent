<?php

// src/Service/AmbtenaarService.php

namespace App\Service;

use App\Entity\Organisation;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

class OrganisationService
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

    /* @todo lets force a organisation here */
    public function getOrganisationRepositories($organisation)
    {
        $repositories = [];
        if ($organisation->getGithubId()) {
            $repositories = array_merge($repositories, $this->github->getOrganisationRepositories($organisation->getGithubId()));
        }
        if ($organisation->getGitlabId()) {
            $repositories = array_merge($repositories, $this->gitlab->getGroupRepositories($organisation->getGitlabId()));
        }
        if ($organisation->getBitbucketId()) {
            $repositories = array_merge($repositories, $this->bitbucket->getOrganisationRepositories($organisation->getBitbucketId()));
        }

        return $repositories;
    }

    /* @todo lets force a user here */
    public function getUserSocialOrganisations()
    {
        // We need a user to find organisations
        if (!$this->user) {
            return [];
        }
        /* @todoonderstaadne code verplaatsen naar de respectiefelijke service providers */

        $organisations = [];

        // Lets see if we have github organisations
        if ($this->user->getGithubToken()) {
            // We set HTTP errors to false here to prevent the application throwing an error when an token has expired
            $client = new Client(['base_uri' => 'https://api.github.com', 'headers'=>['Authorization'=>'Bearer '.$this->user->getGithubToken()], 'http_errors' => false]);
            $serverResponse = $client->get('/user/orgs');
            $response = json_decode($serverResponse->getBody(), true);

            // Lets catch expired or invalid tokens
            if ($serverResponse->getStatusCode() == 401) {
                $this->user->setGithubToken(null);
                $this->em->persist($this->user);
                $this->em->flush($this->user);
            }

            foreach ($response as $organisation) {
                $organisations[] = [
                    'type'  => 'github',
                    'link'  => $organisation['url'],
                    'id'    => strtolower($organisation['login']),
                    'name'  => $organisation['login'],
                    'avatar'=> $organisation['avatar_url'],
                ];
            }

            // Lets then remove all te repositories that are already on this platform
            foreach ($organisations as $key => $organisation) {
                $components = $this->em->getRepository('App:Organisation')->findBy(['githubId' => $organisation['id']]);

                if (count($components) > 0) {
                    $organisation['commonGroundId'] = $components[0]->getId();
                }
            }
        }
        // Lets see if we have gitlab groups
        if ($this->user->getGitlabToken()) {
            // We set HTTP errors to false here to prevent the application throwing an error when an token has expired
            $client = new Client(['base_uri' => 'https://gitlab.com', 'headers'=>['Authorization'=>'Bearer '.$this->user->getGitlabToken()], 'http_errors' => false]);
            $serverResponse = $client->get('/api/v4/groups');
            $response = json_decode($serverResponse->getBody(), true);

            // Lets catch expired or invalid tokens
            if ($serverResponse->getStatusCode() == 401) {
                $this->user->setGitlabToken(null);
                $this->em->persist($this->user);
                $this->em->flush($this->user);
            }

            foreach ($response as $organisation) {
                $organisations[] = [
                    'type'  => 'gitlab',
                    'link'  => $organisation['web_url'],
                    'id'    => strtolower($organisation['path']),
                    'name'  => $organisation['name'],
                    'avatar'=> $organisation['avatar_url'],
                ];
            }

            // Lets then remove all te repositories that are already on this platform
            foreach ($organisations as $key => $organisation) {
                $components = $this->em->getRepository('App:Organisation')->findBy(['gitlabId' => $organisation['id']]);

                if (count($components) > 0) {
                    $organisation['commonGroundId'] = $components[0]->getId();
                }
            }
        }
        // Lets see if we have bitbucket teams
        if ($this->user->getBitbucketToken()) {
            // We set HTTP errors to false here to prevent the application throwing an error when an token has expired
            $client = new Client(['base_uri' => 'https://api.bitbucket.org/', 'headers'=>['Authorization'=>'Bearer '.$this->user->getBitbucketToken()], 'http_errors' => false]);

            $serverResponse = $client->get('/2.0/teams?role=member');
            $response = json_decode($serverResponse->getBody(), true);

            // Lets catch expired or invalid tokens
            if ($serverResponse->getStatusCode() == 401) {
                $this->user->setBitbucketToken(null);
                $this->em->persist($this->user);
                $this->em->flush($this->user);
            }

            /* @todo error
            foreach($response['values'] as $organisation ){
                $organisations[]= [
                        "type"=>"bitbucket",
                        "link"=>$organisation['links']['html']['href'],
                        "id"=> strtolower($organisation['uuid']),
                        "name"=>$organisation['display_name'],
                        "avatar"=>$organisation['links']['avatar']['href']
                ];
            }
            */
        }

        return $organisations;
    }

    public function enrichOrganisations($organisations)
    {
        $results = [];

        // Lets see if we already know these organisations
        foreach ($organisations as $organisation) {
            $type = $organisation['type'];
            $commonground = false;
            switch ($type) {
                case 'github':
                    $commonground = $this->em->getRepository('App:Organisation')->findOneBy(['githubId' => $organisation['name']]);
                    break;
                case 'bitbucket':
                    $commonground = $this->em->getRepository('App:Organisation')->findOneBy(['bitbucket' => $organisation['link']]);
                    break;
                case 'gitlab':
                    $commonground = $this->em->getRepository('App:Organisation')->findOneBy(['gitlabId' => $organisation['id']]);
                    break;
            }

            if ($commonground) {
                $organisation['commonground'] = $commonground;
                $organisation['isMember'] = $commonground->getUsers()->contains($this->user);
                $organisation['isAdmin'] = $commonground->getAdmins()->contains($this->user);
            }

            $results[] = $organisation;
        }

        return $results;
    }

    public function getOrganisationOnSlug($slug)
    {
        $repository = $this->em->getRepository(Organisation::class);

        return $repository->findOneBy(['slug' => $slug]);
    }

    public function getOrganisationFromGithub($slug)
    {
    }
}
