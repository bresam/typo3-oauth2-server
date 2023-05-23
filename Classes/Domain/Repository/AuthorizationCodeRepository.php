<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Repository;

use FGTCLB\OAuth2Server\Domain\Model\AuthorizationCode;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

/**
 * Repository for OAuth2 authorization codes
 */
final class AuthorizationCodeRepository extends AbstractRepository implements AuthCodeRepositoryInterface
{
    public function getAuthCodeByIdentifier(string $identifier): ?AuthorizationCode
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('identifier', $identifier)
            )
        );

        return $query->execute()->getFirst();
    }

    /**
     * @inheritDoc
     */
    public function getNewAuthCode()
    {
        $authCode = new AuthorizationCode();
        $authCode->setNonce($_REQUEST['nonce'] ?? null);

        return $authCode;
    }

    /**
     * @inheritDoc
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $this->add($authCodeEntity);
        $this->persistenceManager->persistAll();
    }

    /**
     * @inheritDoc
     */
    public function revokeAuthCode($codeId): void
    {
        $this->remove($this->getAuthCodeByIdentifier($codeId));
        $this->persistenceManager->persistAll();
    }

    /**
     * @inheritDoc
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        return ! $this->getAuthCodeByIdentifier($codeId);
    }
}
