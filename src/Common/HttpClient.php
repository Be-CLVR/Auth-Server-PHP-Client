<?php

namespace BeCLVR\AuthServerClient\Common;

use InvalidArgumentException;
use BeCLVR\AuthServerClient\Exceptions\HttpException;
use BeCLVR\AuthServerClient\Exceptions\AuthenticateException;
use Exception;
use GuzzleHttp\Client;

/**
 * Class Client.
 */
class HttpClient
{
    public const REQUEST_GET = 'GET';
    public const REQUEST_POST = 'POST';
    public const REQUEST_DELETE = 'DELETE';
    public const REQUEST_PUT = 'PUT';
    public const REQUEST_PATCH = 'PATCH';

    public const HTTP_NO_CONTENT = 204;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var int
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var array
     */
    protected $userAgent = [];

    /**
     * @var string
     */
    protected $token;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var array
     */
    private $headers = [];

    public function __construct(string $endpoint, int $timeout = 10, array $headers = [])
    {
        $this->client = new Client();
        $this->endpoint = $endpoint;

        if (!\is_int($timeout) || $timeout < 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Timeout must be an int > 0, got "%s".',
                    \is_object($timeout) ? \get_class($timeout) : \gettype($timeout).' '.var_export($timeout, true)
                )
            );
        }

        $this->timeout = $timeout;
        $this->headers = $headers;
    }

    /**
     * @param string $method
     * @param string|null $resourceEndpoint
     * @param mixed $query
     * @param string|null $body
     *
     * @return string
     *
     * @throws BeCLVR\AuthServerClient\Exceptions\AuthenticateException
     * @throws BeCLVR\AuthServerClient\Exceptions\HttpException
     */
    public function performHttpRequest(string $method, ?string $resourceEndpoint, $query = null, ?array $body = null): ?string
    {

        if ($this->token === null) {
            throw new AuthenticateException('Can not perform API Request without Authentication');
        }

        $headers = [
            'User-agent' => implode(' ', $this->userAgent),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-Charset' => 'utf-8',
            'Authorization' => $this->token,
            'X-Client-Id' => $this->clientId,
        ];

        $headers = array_merge($headers, $this->headers);

        try {
            $response = $this->client->request(
                $method,
                $this->getRequestUrl($resourceEndpoint, $query),
                [
                    'headers' => $headers,
                    'json' => $body,
                    'timeout' => $this->timeout,
                ]
            );
        } catch (Exception $e) {
           throw $e;
        }
    
        return $response->getBody();
    }

    public function addUserAgentString(string $userAgent): void
    {
        $this->userAgent[] = $userAgent;
    }

    public function setAuthentication(int $clientId, string $token): void
    {
        $this->clientId = $clientId;
        $this->token = $token;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @param string $resourceEndpoint
     * @param mixed $query
     *
     * @return string
     */
    public function getRequestUrl(string $resourceEndpoint, $query): string
    {
        $requestUrl = $this->endpoint.'/'.$resourceEndpoint;
        if ($query) {
            if (\is_array($query)) {
                $query = http_build_query($query);
            }
            $requestUrl .= '?'.$query;
        }

        return $requestUrl;
    }

    public function setTimeout(int $timeout): HttpClient
    {
        $this->timeout = $timeout;

        return $this;
    }
}
