<?php

namespace BeCLVR\AuthServerClient\Objects;

use stdClass;

/**
 * Class User.
 */
class User extends Base
{
    public $created_at;
    public $updated_at;
    public $email;
    public $email_verified_at;

    protected $id;

    public function getId(): string
    {
        return $this->id;
    }

    public function loadFromStdclass(stdClass $object): self
    {
        return parent::loadFromStdclass($object);
    }

    public function toArray() : array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'email' => (string)$this->email,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
