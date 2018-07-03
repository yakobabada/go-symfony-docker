<?php

namespace App\Service;

class TokenService
{
    /**
     * @var array
     */
    private $apiKeys;

    public function __construct(array $apiKeys)
    {
        $this->apiKeys = $apiKeys;
    }

    /**
     * @param $apikey
     * @return bool
     */
    public function isTokenExist($apikey): bool
    {
        return in_array($apikey, $this->apiKeys);
    }
}