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

class GithubService
{
    private $params;
    private $cash;
    private $markdown;
    private $em;
    private $githubToken;
    private $client;

    public function __construct(ParameterBagInterface $params, MarkdownParserInterface $markdown, CacheInterface $cache, EntityManagerInterface $em, $githubToken = null)
    {
        $this->params = $params;
        $this->cash = $cache;
        $this->markdown = $markdown;
        $this->em = $em;
        $this->githubToken = $githubToken;
        if ($this->githubToken) {
            $this->client = new Client(['base_uri' => 'https://api.github.com/', 'headers'=>['Authorization'=>'Bearer '.$user->getGithubToken()]]);
        } else {
            $this->client = new Client(['base_uri' => 'https://api.github.com/']);
        }
    }

    public function get($url)
    {
        $parse = parse_url($url);
        $path = explode('/', $parse['path']);

        // if we have more the one path part then we are dealing with a component
        if (count($path) > 2) {
            return $this->getComponentFromGitHubOnUrl($parse);
        }
        // if not then we are dealing with an organisation
        else {
            return $this->getOrganisationFromGitHub($path[1]);
        }

        return $component;
    }

    public function getUserOrganisations($id)
    {
        $organisations = [];
        $response = $this->client->get('/users/'.$user.'/organisations');
        $responses = json_decode($response->getBody(), true);

        foreach ($response as $organisation) {
            $organisations[] = [
                    'type'  => 'gitlab',
                    'link'  => $organisation['web_url'],
                    'id'    => $organisation['id'],
                    'name'  => $organisation['name'],
                    'avatar'=> $organisation['avatar_url'],
            ];
        }

        // Lets then remove all te repositories that are already on this platform
        foreach ($organisations as $organisation) {
            $components = $this->em->getRepository('App:Organisation')->findBy(['githubId' => $organisation['id']]);

            if (count($components) > 0) {
                $repository['common-ground-id'] = $components->first()->getId();
            }
        }

        return $organisations;
    }

    public function getRepositoryFromGitHub($owner, $repository)
    {
        $response = $this->client->get('repos/'.$owner.'/'.$repository);
        $response = json_decode($response->getBody(), true);

        return $response;
    }

    public function getRepositoryFromGitHubOnId($id)
    {
        $response = $this->client->get('repositories/'.$id);
        $response = json_decode($response->getBody(), true);

        return $response;
    }

    public function getComponentFromGitHubOnId($id)
    {
        $repository = $this->getRepositoryFromGitHubOnId($id);
        $component = new Component();
        $component->setName($repository['name']);
        $component->setDescription($repository['description']);
        $component->setGit($repository['html_url']);
        $component->setGitType('github');
        $component->setGitId($repository['id']);

        return $component;
    }

    public function getComponentFromGitHubOnUrl($url)
    {
        $path = explode('/', $url['path']);

        $repository = $this->getRepositoryFromGitHub($path[1], $path[2]);

        $component = new Component();
        $component->setName($repository['name']);
        $component->setDescription($repository['description']);
        $component->setGit($repository['html_url']);
        $component->setGitType('github');
        $component->setGitId($repository['id']);

        // Lets get a list of posible owners
        $organisations = $this->em->getRepository('App:Organisation')->findBy(['gitId' => $repository['owner']['login']]);

        if (count($organisations) > 0) {
            $component->addOrganisation($organisations[0]);
            $component->setOwner($organisations[0]);
        } else {
            $organisation = $this->getOrganisationFromGitHub($repository['owner']['login']);
            $component->addOrganisation($organisation);
            $component->setOwner($organisation);
        }

        return $component;
    }

    public function getOrganisationFromGitHub($id)
    {
        $response = $this->client->get('/orgs/'.$id);
        $response = json_decode($response->getBody(), true);

        $organisation = new Organisation();
        if (array_key_exists('name', $response) && $response['name']) {
            $organisation->setName($response['name']);
        } else {
            $organisation->setName($response['login']);
        }
        $organisation->setDescription($response['description']);
        $organisation->setLogo($response['avatar_url']);
        $organisation->setGit($response['html_url']);
        $organisation->setGitId($response['login']);

        return $organisation;
    }

    // Returns an array of teams for an organisation
    public function getTeamsFromGitHub($id)
    {
        $response = $this->client->get('/orgs/'.$id.'/teams');
        $responses = json_decode($response->getBody(), true);

        $teams = [];
        foreach ($responses as $response) {
            $team = new Team();
            $team->setName($response['name']);
            $team->setDescription($response['description']);
            $team->setGit($response['html_url']);
            $team->setGitType('github');
            $team->setGitId($response['id']);
            $teams[] = $team;
        }

        return $teams;
    }

    // Returns an array of repositories for an organisation
    public function getOrganisationRepositories($id)
    {
        $repositories = [];
        $response = $this->client->get('/orgs/'.$id.'/repos');
        $responses = json_decode($response->getBody(), true);

        foreach ($responses as $repository) {
            $repositories[] = [
                    'type'       => 'github',
                    'link'       => $repository['html_url'],
                    'id'         => $repository['id'],
                    'name'       => $repository['name'],
                    'description'=> $repository['description'],
                    'avatar'     => $repository['owner']['avatar_url'],
            ];
        }

        // Lets then remove all te repositories that are already on this platform
        foreach ($repositories as $key => $repository) {
            $component = $this->em->getRepository('App:Component')->findOneBy(['gitId' => $repository['id'], 'gitType' => 'github']);

            if ($component) {
                $repositories[$key]['commonGroundId'] = $component->getId();
            }
        }

        return $repositories;
    }

    // Returns an array of repositories for an user
    public function getUserRepositories($id)
    {
        $repositories = [];
        $response = $this->client->get('/users/'.$id.'/repos');
        $responses = json_decode($response->getBody(), true);

        foreach ($responses as $repository) {
            $repositories[] = [
                    'type'       => 'github',
                    'link'       => $repository['html_url'],
                    'id'         => $repository['id'],
                    'name'       => $repository['name'],
                    'description'=> $repository['description'],
                    'avatar'     => $repository['owner']['avatar_url'],
            ];
        }

        return $repositories;
    }

    // Lets get the content of a public github file
    public function getFileContent(Component $component, $file)
    {
        $git = str_replace('https://github.com/', '', $component->getGit());
        $client = new Client(['base_uri' => 'https://raw.githubusercontent.com/'.$git.'/master/', 'http_errors' => false]);

        $response = $client->get($file);

        // Lets see if we can get the file
        if ($response->getStatusCode() == 200) {
            return strval($response->getBody());
        }

        return false;
    }
    
    
    // Finds all the repositories that mention a keyphrase
    public function findRepositories($file)
    {
    
    }
}
