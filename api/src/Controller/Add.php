<?php

// api/src/Controller/AddController.php

namespace App\Controller;

use App\Service\AddService;
use Symfony\Component\HttpFoundation\RequestStack;

class Add
{
    protected $requestStack;
    protected $addService;

    public function __construct(RequestStack $requestStack, AddService $addService)
    {
        $this->requestStack = $requestStack;
        $this->addService = $addService;
    }

    public function __invoke()
    {
        $request = $this->requestStack->getCurrentRequest();
        $url = $request->query->get('url');

        return $this->addService->add($url);
    }
}
