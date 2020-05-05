<?php

declare(strict_types=1);

namespace Only6;

use JsonException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class Client
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var GuzzleClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $authToken;

    /**
     * @var string
     */
    private $username;
    
    /**
     * @var string
     */
    private $password;

    /**
     * @param string $domain
     * @param string $username
     * @param string $password
     * @param GuzzleClient|null $client
     */
    public function __construct(string $domain, string $username, string $password, GuzzleClient $client = null)
    {
        $this->domain = $domain;
        $this->httpClient = $client ?: new GuzzleClient();
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param string $url
     * @return array<string, string>|null
     */
    public function shorten(string $url): ?array
    {
        if (!$this->authToken) {
            $login = $this->login($this->username, $this->password);
            if (!$login) {
                return null;
            }
        }
        $request = new Request('POST', $this->domain . '/api/v1/shorten');
        $request->getBody()->write(
            (string)json_encode(
                [
                    'url' => $url
                ]
            )
        );
        $request = $request->withHeader('Authorization', 'Bearer ' . $this->authToken);

        try {
            $response = $this->httpClient->send($request);
        } catch (ClientException $e) {
            return null;
        }

        try {
            return json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return null;
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @return string|null
     */
    private function login(string $username, string $password): ?string
    {
        $request = new Request('POST', $this->domain . '/api/v1/login');
        $request->getBody()->write(
            (string)json_encode(
                [
                    'username' => $username,
                    'password' => $password
                ]
            )
        );

        try {
            $response = $this->httpClient->send($request);
        } catch (ClientException $e) {
            return null;
        }

        try {
            $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return null;
        }
        if (!isset($data['token'])) {
            return null;
        }
        $this->authToken = $data['token'];
        return $this->authToken;
    }
}
