<?php

// api/src/Controller/ComponentController.php

namespace App\Controller;

use App\Entity\Component;
use App\Service\ComponentService;

class ComponentRefresh
{
    private $componentService;

    public function __construct(ComponentService $componentService)
    {
        $this->componentService = $componentService;
    }

    public function __invoke(Component $data): Component
    {
        $data = $this->componentService->refresh($data);

        return $data;
    }
}
