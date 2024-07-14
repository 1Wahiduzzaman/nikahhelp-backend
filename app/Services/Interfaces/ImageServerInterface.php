<?php

namespace App\Services\Interfaces;

use App\Services\ImageServerService;

interface ImageServerInterface
{
    public function registerWithServer(): ImageServerService;

    public function loginToServer(): ImageServerService;

    public function getTokenFromResponse();

    public function mapRequest(string $requestType);

    public function saveToken(): void;
}
