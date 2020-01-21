<?php

// src/Service/AmbtenaarService.php

namespace App\Service;

use GuzzleHttp\Client;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as CacheInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class SchemaService
{
    private $params;

    public function __construct(ParameterBagInterface $params, MarkdownParserInterface $markdown, CacheInterface $cache)
    {
        $this->params = $params;
        $this->cash = $cache;
        $this->markdown = $markdown;
    }

    public function getSchema($schema)
    {
        $item = $this->cash->getItem('openapi_'.md5($schema));
        if ($item->isHit()) {
            return $item->get();
        }

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $schema,
            // You can set any number of default request options.
            'timeout'  => 4000.0,
        ]);

        $response = $client->request('GET', '/schema/openapi.yaml');

        try {
            $value = Yaml::parse($response->getBody());
        } catch (ParseException $exception) {
            printf('Unable to parse the YAML string: %s', $exception->getMessage());
        }

        $value['info']['description'] = $this->markdown->transformMarkdown($value['info']['description']);

        $item->set($value);
        $item->expiresAt(new \DateTime('tomorrow'));
        $this->cash->save($item);

        return $item->get();
    }
}
