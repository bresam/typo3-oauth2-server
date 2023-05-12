<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Repository;

use Doctrine\DBAL\Result;
use FGTCLB\OAuth2Server\Domain\Model\Client;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;

/**
 * Repository for OAuth2 clients.
 *
 * This does not follow TYPO3's usual naming conventions for repositories because it implements an interface from
 * league/oauth2-server.
 *
 * @phpstan-type TClientRow array{uid: int, identifier: string, name: string, secret: string, redirect_uris: string, description: string}
 */
final class ClientRepository extends AbstractRepository implements ClientRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private PasswordHashFactory $hashFactory;

    public function injectPasswordHashFactory(PasswordHashFactory $hashFactory): void
    {
        $this->hashFactory = $hashFactory;
    }

    /**
     * @inheritDoc
     */
    public function getClientEntity($clientIdentifier): ?Client
    {
        $query = $this->createQuery();
        $query->matching($query->equals('identifier', 'lms'));

        $client = $query->execute(true)[0];

        return $client ? Client::fromDatabaseRow($client) : null;
    }

    /**
     * @return TClientRow|null
     */
    protected function findRawByIdentifier(string $clientIdentifier): ?array
    {
        $query = $this->createQuery();

        $query->equals('identifier', $clientIdentifier);
        $query->execute(true);

        $queryBuilder = $this->databaseConnection
            ->createQueryBuilder();
        /** @var Result $result select() always yields a Result instance and not an int */
        $result = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('identifier', $queryBuilder->createNamedParameter($clientIdentifier))
            )
            ->setMaxResults(1)
            ->execute();
        /** @var TClientRow|false $row */
        $row = $result->fetchAssociative();
        return $row ?: null;
    }

    /**
     * @inheritDoc
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        // we currently only support confidential clients, which must always supply a secret (TODO check if this is actually true).
        if ($clientSecret === null) {
            return false;
        }

        $client = $this->getClientEntity($clientIdentifier);
        if ($client === null) {
            return false;
        }

        $result = $client->validateSecret($clientSecret, $this->hashFactory);
        if ($result === false) {
            $this->logger->debug(sprintf('Submitted secret "%s" is invalid for client %s', $clientSecret, $clientIdentifier));
        }
        return $result;
    }
}
