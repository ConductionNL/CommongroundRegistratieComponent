<?php

// src/Service/AmbtenaarService.php

namespace App\Service;

use App\Entity\Component;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\RST\Parser as ReStructuredText;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
// your user entity
use Symfony\Component\Yaml\Yaml; // your user entity

class ComponentService
{
    private $params;
    private $cash;
    private $markdown;
    private $em;
    private $gitlab;
    private $github;
    private $bitbucket;

    public function __construct(
            ParameterBagInterface $params,
            MarkdownParserInterface $markdown,
            CacheInterface $cache,
            EntityManagerInterface $em,
            GithubService $github,
            GitlabService $gitlab,
            BitbucketService $bitbucket)
    {
        $this->params = $params;
        $this->cash = $cache;
        $this->markdown = $markdown;
        $this->em = $em;
        $this->gitlab = $gitlab;
        $this->github = $github;
        $this->bitbucket = $bitbucket;
    }

    public function getHelm(Component $component)
    {
        $lookIn = [
            'helm/',
            'api/helm/',
        ];

        foreach ($lookIn as $location) {
            if ($responce = $this->github->getFileContent($component, $location.'values.yaml')) {
                return Yaml::parse($responce);
            }
        }

        // If nothing is found
        return false;
    }

    public function getOpenApi(Component $component)
    {
        $lookIn = [
            '',
            'src/',
            'schema/',
            'public/',
            'public/schema/',
            'api/',
            'api/schema/',
            'api/public/',
            'api/public/schema/',
        ];

        foreach ($lookIn as $location) {
            if ($responce = $this->github->getFileContent($component, $location.'openapi.yaml')) {
                return Yaml::parse($responce);
            }
            if ($responce = $this->github->getFileContent($component, $location.'openapi.json')) {
                return json_decode($responce, true);
            }
        }

        // If nothing is found
        return false;
    }

    public function getComponentFromGit(Component $component)
    {
        /*@todo we should filter for html here */
        $reStructuredText = new ReStructuredText();

        $git = [];
        $git['docs'] = [];
        $git['version'] = false;
        $git['tags'] = [];
        $git['servers'] = [];
        $git['links'] = [];

        $git['links']['repository'] = $component->getGit();

        /* should be moved to git service */
        if ($component->getGitType() == 'github') {
            $git['docs']['readme'] = $this->github->getFileContent($component, 'README.md');
            if ($git['docs']['readme']) {
                $git['docs']['readme'] = $this->markdown->transformMarkdown($git['docs']['readme']);
            } else {
                $git['docs']['readme'] = $this->github->getFileContent($component, 'README.rst');
                //if($git['docs']['readme']){$git['docs']['readme'] = $reStructuredText->parse($git['docs']['readme'])->render();}
            }

            $git['docs']['license'] = $this->github->getFileContent($component, 'LICENSE.md');
            if ($git['docs']['license']) {
                $git['docs']['license'] = $this->markdown->transformMarkdown($git['docs']['license']);
            } else {
                $git['docs']['license'] = $this->github->getFileContent($component, 'LICENSE.rst');
                if ($git['docs']['license']) {
                    $git['docs']['license'] = $reStructuredText->parse($git['docs']['license'])->render();
                }
            }

            $git['docs']['changelog'] = $this->github->getFileContent($component, 'CHANGELOG.md');
            if ($git['docs']['changelog']) {
                $git['docs']['changelog'] = $this->markdown->transformMarkdown($git['docs']['changelog']);
            } else {
                $git['docs']['changelog'] = $this->github->getFileContent($component, 'CHANGELOG.rst');
                //if($git['docs']['changelog'] ){$git['docs']['changelog'] = $reStructuredText->parse($git['docs']['changelog'])->render();}
            }

            $git['docs']['contributing'] = $this->github->getFileContent($component, 'CONTRIBUTING.md');
            if ($git['docs']['contributing']) {
                $git['docs']['contributing'] = $this->markdown->transformMarkdown($git['docs']['contributing']);
            } else {
                $git['docs']['contributing'] = $this->github->getFileContent($component, 'CONTRIBUTING.rst');
                if ($git['docs']['contributing']) {
                    $git['docs']['contributing'] = $reStructuredText->parse($git['docs']['contributing'])->render();
                }
            }

            $git['docs']['installation'] = $this->github->getFileContent($component, 'INSTALLATION.md');
            if ($git['docs']['installation']) {
                $git['docs']['installation'] = $this->markdown->transformMarkdown($git['docs']['installation']);
            } else {
                $git['docs']['installation'] = $this->github->getFileContent($component, 'INSTALLATION.rst');
                if ($git['docs']['installation']) {
                    $git['docs']['installation'] = $reStructuredText->parse($git['docs']['installation'])->render();
                }
            }

            $git['docs']['code-of-conduct'] = $this->github->getFileContent($component, 'CODE_OF_CONDUCT.md');
            if ($git['docs']['code-of-conduct']) {
                $git['docs']['code-of-conduct'] = $this->markdown->transformMarkdown($git['docs']['code-of-conduct']);
            } else {
                $git['docs']['code-of-conduct'] = $this->github->getFileContent($component, 'CODE_OF_CONDUCT.rst');
                if ($git['docs']['code-of-conduct']) {
                    $git['docs']['code-of-conduct'] = $reStructuredText->parse($git['docs']['code-of-conduct']);
                }
            }
        }

        // Lets transform das shizle

        $git['openapi'] = $this->getOpenApi($component);
        $git['helm'] = $this->getHelm($component);

        // Lets do something with this info
        if ($git['openapi']['info']['version']) {
            $git['version'] = $git['openapi']['info']['version'];
        }

        // Lets copy some component info
        $git['name'] = $component->getName();
        $git['description'] = $component->getDescription();
        $git['logo'] = $component->getLogo();
        $git['git'] = $component->getGit();
        $git['gitType'] = $component->getGitType();
        $git['gitId'] = $component->getGitId();
        $git['id'] = $component->getId();

        // The we need to determine the type of te componet
        if ($git['openapi']) {
            $git['type'] = 'source';
        } else {
            $git['type'] = 'application';
        }

        return $git;
    }

    /**
     * @param Component $component The component for wich git info is requested
     * @param bool      $force     If set to true will force a cash reset
     */
    public function getComponentGit(Component $component, $force = false)
    {
        $item = $this->cash->getItem('component_'.$component->getId());
        if ($item->isHit() && !$force) {
            return $item->get();
        }

        $value = $this->getComponentFromGit($component);

        $item->set($value);
        $item->expiresAt(new \DateTime('tomorrow'));
        $this->cash->save($item);

        return $item->get();
    }
}
