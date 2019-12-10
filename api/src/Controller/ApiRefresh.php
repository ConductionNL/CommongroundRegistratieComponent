<?php

// api/src/Controller/ApiController.php

namespace App\Controller;

use App\Entity\Api;
use App\Service\ApiService;

class ApiRefresh
{
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function __invoke(Api $data): Book
    {
        $data = $this->apiService->refresh($data);

        return $data;
    }
}
