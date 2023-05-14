<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\OpenId;

use DateInterval;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use FGTCLB\OAuth2Server\OpenId\Repository\IdentityProviderInterface;
use Psr\Http\Message\ServerRequestInterface;

class OpenIdConnectGrant extends AuthCodeGrant
{
    private IdTokenResponse $idTokenResponse;

    public function __construct(
        AuthCodeRepositoryInterface $authCodeRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        IdentityProviderInterface $identityRepository,
        DateInterval $authCodeTTL
    ) {
        parent::__construct($authCodeRepository, $refreshTokenRepository, $authCodeTTL);

        $this->idTokenResponse = new IdTokenResponse($identityRepository, new ClaimExtractor());
    }

    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType, DateInterval $accessTokenTTL): ResponseTypeInterface {
        return parent::respondToAccessTokenRequest($request, $this->idTokenResponse, $accessTokenTTL);
    }
}