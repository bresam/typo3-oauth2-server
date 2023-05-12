<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Server;

use FGTCLB\OAuth2Server\Configuration;
use FGTCLB\OAuth2Server\Domain\Repository\AccessTokenRepository;
use FGTCLB\OAuth2Server\Domain\Repository\AuthorizationCodeRepository;
use FGTCLB\OAuth2Server\Domain\Repository\ClientRepository;
use FGTCLB\OAuth2Server\Domain\Repository\RefreshTokenRepository;
use FGTCLB\OAuth2Server\Domain\Repository\ScopeRepository;
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
    private Configuration $configuration;
    private ClientRepository $clientRepository;
    private AccessTokenRepository $accessTokenRepository;
    private ScopeRepository $scopeRepository;
    private AuthorizationCodeRepository $authorizationCodeRepository;
    private RefreshTokenRepository $refreshTokenRepository;

    /**
     * @param Configuration|null $configuration
     */
    public function __construct(
        Configuration $configuration,
        ClientRepository $clientRepository,
        AccessTokenRepository $accessTokenRepository,
        ScopeRepository $scopeRepository,
        AuthorizationCodeRepository $authorizationCodeRepository,
        RefreshTokenRepository $refreshTokenRepository,
    ) {
        $this->configuration = $configuration;
        $this->clientRepository = $clientRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->scopeRepository = $scopeRepository;
        $this->authorizationCodeRepository = $authorizationCodeRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * Build an instance of an OAuth2 authorization server
     *
     * @return AuthorizationServer
     */
    public function buildAuthorizationServer(): AuthorizationServer
    {
        $server = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            $this->configuration->getPrivateKeyFile(),
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']
        );

        $authCodeGrant = new AuthCodeGrant(
            $this->authorizationCodeRepository,
            $this->refreshTokenRepository,
            $this->configuration->getAuthorizationCodeLifetime()
        );
        $authCodeGrant->setRefreshTokenTTL($this->configuration->getRefreshTokenLifetime());

        $refreshTokenGrant = new RefreshTokenGrant(
            $this->refreshTokenRepository,
        );
        $refreshTokenGrant->setRefreshTokenTTL($this->configuration->getRefreshTokenLifetime());

        // Enable grants
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
        $validator = new BearerTokenValidator(
            $this->accessTokenRepository
        );
        return new ResourceServer(
            $this->accessTokenRepository,
            $this->configuration->getPublicKeyFile(),
            $validator
        );
    }
}
