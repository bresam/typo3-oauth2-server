<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\Service;

use FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint\OAuthApiEndpointInterface;
use FGTCLB\OAuth2Server\Domain\Repository\ClientRepository;
use FGTCLB\OAuth2Server\Server\ServerFactory;
use League\OAuth2\Server\Exception\OAuthServerException;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OAuthIntrospectionEndpoint implements OAuthApiEndpointInterface
{
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
        return $path === '/token/introspect';
    }

    public function handle(ServerRequest $request): Response
    {
        $this->clientSecretAuth($request);

        try {
            $token = $request->getParsedBody()['token'];
            (new ServerFactory())
                ->buildResourceServer()
                ->validateAuthenticatedRequest($request->withHeader('authorization', 'Bearer '.$token));
        } catch (OAuthServerException $e) {
            return new JsonResponse([
                'active' => false,
            ]);
        }

        return new JsonResponse([
            'active' => true,
        ]);
    }

    /** @throws OAuthServerException */
    private function clientSecretAuth(ServerRequest $request): void
    {
        if ($request->hasHeader('authorization')) {
            [$clientId, $clientSecret] = explode(':', base64_decode(substr($request->getHeader('authorization')[0], 6)));

            if (GeneralUtility::makeInstance(ClientRepository::class)->validateClient($clientId, $clientSecret, 'openid')) {
                return;
            }
        }

        throw OAuthServerException::accessDenied('client id / secret auth invalid!');
    }
}