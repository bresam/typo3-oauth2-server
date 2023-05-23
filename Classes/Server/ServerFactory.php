<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Server;

use FGTCLB\OAuth2Server\Configuration;
use FGTCLB\OAuth2Server\Domain\Repository\AccessTokenRepository;
use FGTCLB\OAuth2Server\Domain\Repository\AuthorizationCodeRepository;
use FGTCLB\OAuth2Server\Domain\Repository\ClientRepository;
use FGTCLB\OAuth2Server\Domain\Repository\RefreshTokenRepository;
use FGTCLB\OAuth2Server\Domain\Repository\ScopeRepository;
use FGTCLB\OAuth2Server\OpenId\ClaimExtractor;
use FGTCLB\OAuth2Server\OpenId\IdTokenResponse;
use FGTCLB\OAuth2Server\Service\IdentityProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Factory for OAuth2 authorization servers
 */
final class ServerFactory
{
    protected Configuration $configuration;

    /**
     * @param Configuration|null $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        $this->configuration = $configuration ?: GeneralUtility::makeInstance(Configuration::class);
    }

    /**
     * Build an instance of an OAuth2 authorization server
     *
     * @return AuthorizationServer
     */
    public function buildAuthorizationServer(): AuthorizationServer
    {
        $clientRepository = GeneralUtility::makeInstance(ClientRepository::class);
        $accessTokenRepository = GeneralUtility::makeInstance(AccessTokenRepository::class);
        $authorizationCodeRepository = GeneralUtility::makeInstance(AuthorizationCodeRepository::class);
        $refreshTokenRepository = GeneralUtility::makeInstance(RefreshTokenRepository::class);
        $scopeRepository = new ScopeRepository();

        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $this->configuration->getPrivateKeyFile(),
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'],
            new IdTokenResponse(
                GeneralUtility::makeInstance(IdentityProvider::class),
                new ClaimExtractor(),
                $authorizationCodeRepository)
        );

//        $idTokenGrant = new OpenIdConnectGrant(
//            new AuthorizationCodeRepository(),
//            GeneralUtility::makeInstance(RefreshTokenRepository::class),
//            GeneralUtility::makeInstance(IdentityProvider::class),
//            $this->configuration->getAuthorizationCodeLifetime()
//        );
//        $idTokenGrant->setRefreshTokenTTL($this->configuration->getRefreshTokenLifetime());

        $authCodeGrant = new AuthCodeGrant(
            $authorizationCodeRepository,
            $refreshTokenRepository,
            $this->configuration->getAuthorizationCodeLifetime()
        );
        $authCodeGrant->setRefreshTokenTTL($this->configuration->getRefreshTokenLifetime());

        $refreshTokenGrant = new RefreshTokenGrant($refreshTokenRepository);
        $refreshTokenGrant->setRefreshTokenTTL($this->configuration->getRefreshTokenLifetime());

        // Enable grants
//        $server->enableGrantType($idTokenGrant, $this->configuration->getAccessTokenLifetime());
        $server->enableGrantType($authCodeGrant, $this->configuration->getAccessTokenLifetime());
        $server->enableGrantType($refreshTokenGrant, $this->configuration->getAccessTokenLifetime());

        return $server;
    }

    /**
     * Build an instance of an OAuth2 resource server
     *
     * @return ResourceServer
     */
    public function buildResourceServer(): ResourceServer
    {
        $accessTokenRepository = GeneralUtility::makeInstance(AccessTokenRepository::class);
        $validator = new BearerTokenValidator(
            $accessTokenRepository
        );
        return new ResourceServer(
            $accessTokenRepository,
            $this->configuration->getPublicKeyFile(),
            $validator
        );
    }
}
