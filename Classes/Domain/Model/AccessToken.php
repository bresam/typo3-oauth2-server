<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Model;

use DateTimeImmutable;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * OAuth2 access token
 */
final class AccessToken extends AbstractEntity implements AccessTokenEntityInterface
{
    use EntityTrait;
    use AccessTokenTrait;

    /** @var ScopeEntityInterface[] */
    protected $scopes = [];

    /** @var string|null */
    protected ?string $userIdentifier = null;

    /** @var Client|null */
    protected ?Client $client = null;

    /** @var DateTimeImmutable|null */
    protected ?DateTimeImmutable $expiryDateTime = null;

    /** @var DateTimeImmutable|null */
    protected ?DateTimeImmutable $revoked = null;

    /**
     * Associate a scope with the token.
     *
     * @param ScopeEntityInterface $scope
     */
    public function addScope(ScopeEntityInterface $scope): void
    {
        $this->scopes[$scope->getIdentifier()] = $scope;
    }

    /**
     * Return an array of scopes associated with the token.
     *
     * @return ScopeEntityInterface[]
     */
    public function getScopes()
    {
        return \array_values($this->scopes);
    }

    /** Get the token's expiry date time. */
    public function getExpiryDateTime(): ?DateTimeImmutable
    {
        return $this->expiryDateTime;
    }

    /** Set the date time when the token expires. */
    public function setExpiryDateTime(DateTimeImmutable $dateTime): void
    {
        $this->expiryDateTime = $dateTime;
    }

    /**
     * Set the identifier of the user associated with the token.
     *
     * @param string $identifier The identifier of the user
     */
    public function setUserIdentifier($identifier): void
    {
        $this->userIdentifier = $identifier ? (string) $identifier : null;
    }

    /** Get the token user's identifier.*/
    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    /** Get the client that the token was issued to.*/
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /** @param Client $client */
    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }

    public function getRevoked(): ?DateTimeImmutable
    {
        return $this->revoked;
    }

    public function setRevoked(?DateTimeImmutable $revoked): void
    {
        $this->revoked = $revoked;
    }
}
