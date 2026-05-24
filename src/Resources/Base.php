<?php

namespace BeCLVR\AuthServerClient\Resources;

use BeCLVR\AuthServerClient\Common\HttpClient;
use BeCLVR\AuthServerClient\Common\ResponseError;
use BeCLVR\AuthServerClient\Exceptions\ServerException;
use BeCLVR\AuthServerClient\Exceptions\RequestException;

/**
 * Class Base.
 */
class Base
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string The resource endpoint as it is known at the server
     */
    protected $resourceEndpoint;

    protected $object;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getResourceEndpoint(): string
    {
        return $this->resourceEndpoint;
    }

    /**
     * @param mixed $resourceEndpoint
     */
    public function setResourceEndpoint($resourceEndpoint): void
    {
        $this->resourceEndpoint = $resourceEndpoint;
    }

    /**
     * @return \BeCLVR\AuthServerClient\Objects\Profile
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object): void
    {
        $this->object = $object;
    }

    /**
     * @param string|null $body
     * @return \BeCLVR\AuthServerClient\Objects\Profile|null
     *
     * @throws BeCLVR\AuthServerClient\Exceptions\AuthenticateException
     * @throws BeCLVR\AuthServerClient\Exceptions\RequestException
     * @throws BeCLVR\AuthServerClient\Exceptions\ServerException
     */
    public function processRequest(?string $body)
    {
        if ($body === null) {
            throw new ServerException('Got an invalid response from the server.');
        }

        try {
            $body = json_decode($body, null, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ServerException('Got an invalid JSON response from the server.');
        }

        if (!empty($body->errors) || !property_exists($body, 'data')) {
            $responseError = new ResponseError($body);
            throw new RequestException($responseError->getErrorString());
        }

        if ($body->data) {
            if (is_null($body->data)) {
                return true;
            }

            if ($this->object) {
                return $this->object->loadFromStdclass($body->data);
            }

            return $body->data;
        }
    }

    /**
     * @no-named-arguments
     *
     * @param mixed $object
     * @param array|null $query
     *
     * @return \BeCLVR\AuthServerClient\Objects\Profile|null
     *
     * @throws BeCLVR\AuthServerClient\Exceptions\AuthenticateException
     * @throws BeCLVR\AuthServerClient\Exceptions\HttpException
     * @throws BeCLVR\AuthServerClient\Exceptions\RequestException
     * @throws BeCLVR\AuthServerClient\Exceptions\ServerException
     * @throws \JsonException
     */
    public function create($object, ?array $query = null)
    {
        $body = $object->toArray();
        $data = $this->httpClient->performHttpRequest(
            HttpClient::REQUEST_POST,
            $this->resourceEndpoint,
            $query,
            $body
        );

        return $this->processRequest($data);
    }

    public function post($body, ?array $query = null)
    {
        $data = $this->httpClient->performHttpRequest(
            HttpClient::REQUEST_POST,
            $this->resourceEndpoint,
            $query,
            $body
        );

        return $this->processRequest($data);
    }

    public function get()
    {
        $data = $this->httpClient->performHttpRequest(
            HttpClient::REQUEST_GET,
            $this->resourceEndpoint
        );

        return $this->processRequest($data);
    }

    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
}
