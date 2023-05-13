<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Repository;

use FGTCLB\OAuth2Server\Domain\Model\RefreshToken;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

/**
 * Repository for OAuth2 refresh tokens
 */
final class RefreshTokenRepository extends AbstractRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getNewRefreshToken()
    {
        return new RefreshToken();
    }

    /**
     * @inheritDoc
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $this->add($refreshTokenEntity);
        $this->persistenceManager->persistAll();
    }

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     */
    public function revokeRefreshToken($tokenId): void
    {
        // TODO: Revoke persisted token
    }

    /**
     * @inheritDoc
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        // TODO: Check if persisted token is revoked
        return false;
    }
}
