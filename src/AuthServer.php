<?php

namespace BeCLVR\AuthServerClient;

use BeCLVR\AuthServerClient\Common\HttpClient;
use BeCLVR\AuthServerClient\Resources\Profiles;
use BeCLVR\AuthServerClient\Resources\Users;

/**
 * Class Client.
 */
class AuthServer
{
    public const CLIENT_VERSION = '1.0.0';

    protected $clientId;
    protected $token;

    /**
     * @var Common\HttpClient
     */
    protected $httpClient;

    /**
     * @var Resources\Profiles
     */
    public $profiles;

    public function __construct()
    {
        $this->clientId = config('authserver.client_id');
        $this->token = config('authserver.token');
        $this->httpClient = new HttpClient(config('authserver.url'));
        $this->httpClient->addUserAgentString('AuthServer/ApiClient/'.self::CLIENT_VERSION);
        $this->httpClient->setAuthentication($this->clientId, $this->token);
        $this->httpClient->addUserAgentString($this->getPhpVersion());

        $this->profiles = new Profiles($this->httpClient);
    }

    public function register($user, bool $initialUser = true, string $externalTid = null)
    {
        return $this->profiles->register($user, $initialUser, $externalTid);
    }

    public function unregister($user, int $profileId, string $externalTid = null)
    {
        return $this->profiles->unregister($user, $profileId, $externalTid);
    }

    public function show(int $profileId)
    {
        return $this->profiles->show($profileId);
    }

    public function updateEmail(int $profileId, string $email)
    {
        return $this->profiles->updateEmail($profileId, $email);
    }

    private function getPhpVersion(): string
    {
        if (!\defined('PHP_VERSION_ID')) {
            $version = array_map('intval', explode('.', \PHP_VERSION));
            \define('PHP_VERSION_ID', $version[0] * 10000 + $version[1] * 100 + $version[2]);
        }

        return 'PHP/'.\PHP_VERSION_ID;
    }
}
