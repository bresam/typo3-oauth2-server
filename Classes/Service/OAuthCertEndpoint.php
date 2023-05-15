<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\Service;

use FGTCLB\OAuth2Server\Configuration;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint\OAuthApiEndpointInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

class OAuthCertEndpoint implements OAuthApiEndpointInterface
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getScope(): string
    {
        return '*';
    }

    public function canHandlePath(string $path): bool
    {
        return $path === '/oauth/jwks.json';
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $keyInfo = openssl_pkey_get_details(openssl_pkey_get_public(file_get_contents($this->configuration->getPublicKeyFile())));

        $jsonData = [
            'keys' => [
                (object) [
                    'kty' => 'RSA',
                    'n' => rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($keyInfo['rsa']['n'])), '='),
                    'e' => rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($keyInfo['rsa']['e'])), '='),
                ],
            ],
        ];

        return new JsonResponse($jsonData);
    }
}