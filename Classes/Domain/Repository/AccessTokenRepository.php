<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Repository;

use FGTCLB\OAuth2Server\Domain\Model\AccessToken;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

/**
 * Repository for OAuth2 access tokens
 */
final class AccessTokenRepository extends AbstractRepository implements AccessTokenRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new AccessToken();
        $accessToken->setClient($clientEntity);
        $accessToken->setUserIdentifier($userIdentifier);

        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        return $accessToken;
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @throws UniqueTokenIdentifierConstraintViolationException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $this->add($accessTokenEntity);
        $this->persistenceManager->persistAll();
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId): void
    {
        $accessToken = $this->getAccessTokenByIdentifier($tokenId);

        if (!$accessToken) {
            return;
        }

        $accessToken->setRevoked(new \DateTimeImmutable());
        $this->update($accessToken);
        $this->persistenceManager->persistAll();
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        $accessToken = $this->getAccessTokenByIdentifier($tokenId);

        if (!$accessToken) {
            return true;
        }

        return (bool) $accessToken->getRevoked();
    }

    private function getAccessTokenByIdentifier(string $identifier): ?AccessToken
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('identifier', $identifier)
            )
        );

        return $query->execute()->getFirst();
    }
}
