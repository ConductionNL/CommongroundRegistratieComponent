<?php

// api/src/Controller/OrganisationController.php

namespace App\Controller;

use App\Entity\Organisation;
use App\Service\OrganisationService;

class OrganisationRefresh
{
    private $organisationService;

    public function __construct(OrganisationService $organisationService)
    {
        $this->organisationService = $organisationService;
    }

    public function __invoke(Organisation $data): Organisation
    {
        $this->organisationService->refresh($data);

        return $data;
    }
}
