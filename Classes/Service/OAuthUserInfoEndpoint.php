<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\Service;

use FGTCLB\OAuth2Server\DependencyInjection\ApiEndpoint\OAuthApiEndpointInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;

class OAuthUserInfoEndpoint implements OAuthApiEndpointInterface
{
    private IdentityProvider $identityProvider;

    public function __construct(IdentityProvider $identityProvider)
    {
        $this->identityProvider = $identityProvider;
    }

    public function isPublic(): bool
    {
        return false;
    }

    public function getScope(): string
    {
        return 'openid';
    }

    public function canHandlePath(string $path): bool
    {
        return $path === '/connect/userinfo';
    }

    public function handle(ServerRequest $request): Response
    {
        $uid = $request->getAttribute('oauth_user_id');

        return new JsonResponse(
            $this->identityProvider->getUserInfoByIdentifier($uid),
            200,
            [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}