<?php

// src/Service/AmbtenaarService.php

namespace App\Service;

use App\Entity\Component;
use App\Entity\Organisation;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GitlabService
{
    private $params;
    private $cash;
    private $markdown;
    private $em;
    private $gitlabToken;
    private $client;

    public function __construct(ParameterBagInterface $params, MarkdownParserInterface $markdown, CacheInterface $cache, EntityManagerInterface $em, $githubToken = null)
    {
        $this->params = $params;
        $this->cash = $cache;
        $this->markdown = $markdown;
        $this->em = $em;
        $this->githubToken = $githubToken;
        if ($this->githubToken) {
            $this->client = new Client(['base_uri' => 'https://gitlab.com/api/v4/', 'headers'=>['Authorization'=>'Bearer '.$user->getGitlabToken()]]);
        } else {
            $this->client = new Client(['base_uri' => 'https://gitlab.com/api/v4/']);
        }
    }

    public function get($url)
    {
    }

    public function getComponentFromGitLab($id)
    {
        $response = $this->client->get('/api/v4/projects/'.$id);
        $response = json_decode($response->getBody(), true);

        $component = new Component();
        $component->setName($response['name']);
        $component->setDescription($response['description']);
        $component->setGitlab($response['web_url']);
        $component->setGitlabId($response['id']);
        $component->setAvatar($response['avatar_url']);

        return $component;
    }

    // we always use a user to ask api qoustions
    public function getOrganisationFromGitlab($id)
    {
        $response = $this->client->get('/api/v4/groups/'.$id);
        $response = json_decode($response->getBody(), true);

        $organisation = new Organisation();
        $organisation->setName($response['name']);
        $organisation->setDescription($response['description']);
        $organisation->setLogo($response['avatar_url']);
        $organisation->setGithub($response['web_url']);
        $organisation->setGithubId($response['path']);

        return $organisation;
    }

    // Returns an array of repositories for an organisation
    public function getGroupRepositories($id)
    {
        $repositories = [];
        $response = $this->client->get('groups/'.$id.'/projects');
        $responses = json_decode($response->getBody(), true);

        foreach ($responses as $repository) {
            $repositories[] = [
                'type'       => 'gitlab',
                'link'       => $repository['web_url'],
                'id'         => $repository['id'],
                'name'       => $repository['name'],
                'description'=> $repository['description'],
                'logo'       => $repository['avatar_url'],
            ];
        }

        return $repositories;
    }

    // Returns an array of repositories for an user
    public function getUserRepositories($id)
    {
        $repositories = [];
        $response = $this->client->get('users/'.$id.'/projects');
        $responses = json_decode($response->getBody(), true);

        foreach ($responses as $repository) {
            $repositories[] = [
                'type'       => 'gitlab',
                'link'       => $repository['web_url'],
                'id'         => $repository['id'],
                'name'       => $repository['name'],
                'description'=> $repository['description'],
                'logo'       => $repository['avatar_url'],
            ];
        }

        // Lets then remove all te repositories that are already on this platform
        foreach ($repositories as $repository) {
            $component = $this->em->getRepository('App:Component')->findOneBy(['gitId' => $repository['id'], 'gitType' => $repository['type']]);

            if ($component) {
                $repositories[$key]['commonGroundId'] = $component->getId();
            }
        }

        return $repositories;
    }
}
