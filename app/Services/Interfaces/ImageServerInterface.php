<?php

namespace App\Services\Interfaces;

use App\Models\User;
use App\Services\ImageServerService;

interface ImageServerInterface
{
   public function registerWithServer(): ImageServerService;

   public function loginToServer(): ImageServerService;

   public function getTokenFromResponse();

   public function mapRequest(String $requestType): ImageServerService;

   public function saveToken(): void;
}
