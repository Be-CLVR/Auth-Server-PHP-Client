<?php

namespace BrendanMacKenzie\AuthServerClient\Resources;

use BrendanMacKenzie\AuthServerClient\Objects\Profile;
use BrendanMacKenzie\AuthServerClient\Common\HttpClient;
use Exception;

/**
 * Class Auth.
 */
class Profiles extends Base
{
    public function __construct(HttpClient $httpClient)
    {
        parent::__construct($httpClient);
    }

    public function register($user, bool $initialUser = true, string $externalTid = null) : Profile
    {
        $this->setResourceEndpoint('profile/register');
        $this->setObject(new Profile());

        $this->object->email = $user->email;
        $this->object->externalId = $user->id;
        $this->object->externalTid = $externalTid;
        $this->object->initialUser = $initialUser;

        return $this->create($this->object);
    }

    public function unregister($user, int $profileId, string $externalTid = null, bool $silent = false)
    {
        $this->setResourceEndpoint('profile/unregister');
        $this->setObject(null);

        $body = [
            'id' => $profileId,
            'external_id' => (string) $user->id,
            'external_tid' => (string) $externalTid,
            'silent' => (boolean) $silent,
        ];

        return $this->post($body);
    }

    public function show(int $profileId)
    {
        $this->setResourceEndpoint('profile/'.$profileId);
        $this->setObject(new Profile());

        return $this->get();
    }

    public function updateEmail(int $profileId, string $email)
    {
        $this->setResourceEndpoint('profile/'.$profileId.'/email');
        $this->setObject(null);
        
        $body = [
            'email' => $email
        ];

        return $this->post($body);
    }
}
