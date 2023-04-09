<?php

namespace App\Services;


use App\Models\PictureServerToken;
use App\Models\User;
use App\Services\Interfaces\ImageServerInterface;

class ImageServerService implements ImageServerInterface
{

    protected $token;

    protected $res;

    protected $user;

    public function __construct(User $user, String $requestPath)
    {
        $this->user = $user;
        $this->mapRequest($requestPath);
    }

    public function registerWithServer(): ImageServerService
    {
        // TODO: Implement registerWithServer() method.
        $client = new \GuzzleHttp\Client();

        $this->res = $client->request('POST', config('chobi.chobi') . '/api/v1/register', [
            'form_params' => [
                'email' => $this->user->email,
                'password' => $this->user->password
            ]
        ]);

        return $this;
    }

    public function loginToServer(): ImageServerService
    {
        // TODO: Implement loginToServer() method.
        $client = new \GuzzleHttp\Client();

        $this->res = $client->request('POST', config('chobi.chobi') . '/api/v1/login', [
            'form_params' => [
                'email' => $this->user->email,
                'password' => $this->user->password
            ]
        ]);

        return $this;
    }

    public function setTokenFromResponse(): ImageServerService
    {
        // TODO: Implement getTokenFromResponse() method.
        $this->token = json_decode($this->res->getBody()->getContents())->data->token->access_token;
        return $this;
    }

    public function mapRequest(string $requestType)
    {
        // TODO: Implement mapRequest() method.
        $request = [
            'register' => $this->registerWithServer()->setTokenFromResponse()->saveToken(),
            'login' => $this->loginToServer()->setTokenFromResponse()->saveToken(),
        ];
        return $request[$requestType];
    }

    public function getTokenFromResponse()
    {
        // TODO: Implement getTokenFromResponse() method.
        return $this->token;
    }


    public function saveToken(): void
    {
        // TODO: Implement saveToken() method.
        PictureServerToken::updateORCreate([
            'user_id' => $this->user->id,
            'token' => $this->getTokenFromResponse()
        ]);
    }

    public static function getTokenFromDatabase(User $user)
    {
        return PictureServerToken::where('user_id', $user->id)->first()->token;
    }
}
