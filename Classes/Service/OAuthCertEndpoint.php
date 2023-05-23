<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\Service;

use FGTCLB\OAuth2Server\Configuration;
use FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint\OAuthApiEndpointInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;

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
        return $path === '/connect/jwks.json';
    }

    public function handle(ServerRequest $request): Response
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

        return new JsonResponse($jsonData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}