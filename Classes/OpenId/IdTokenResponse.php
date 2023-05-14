<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\OpenId;

use FGTCLB\OAuth2Server\DTO\Identity;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use FGTCLB\OAuth2Server\OpenId\Repository\IdentityProviderInterface;
use FGTCLB\OAuth2Server\OpenId\Entity\ClaimSetInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Encoding\JoseEncoder;

class IdTokenResponse extends BearerTokenResponse
{
    protected IdentityProviderInterface $identityProvider;
    protected ClaimExtractor $claimExtractor;

    public function __construct(IdentityProviderInterface $identityProvider, ClaimExtractor $claimExtractor)
    {
        $this->identityProvider = $identityProvider;
        $this->claimExtractor = $claimExtractor;
    }

    protected function getBuilder(AccessTokenEntityInterface $accessToken, Identity $userEntity): Builder
    {
        $claimsFormatter = ChainedFormatter::withUnixTimestampDates();
        $builder = new Builder(new JoseEncoder(), $claimsFormatter);

        // Since version 8.0 league/oauth2-server returns \DateTimeImmutable
        $expiresAt = $accessToken->getExpiryDateTime();
        if ($expiresAt instanceof \DateTime) {
            $expiresAt = \DateTimeImmutable::createFromMutable($expiresAt);
        }

        // Add required id_token claims
        return $builder
            ->permittedFor($accessToken->getClient()->getIdentifier())
            ->issuedBy('https://' . $_SERVER['HTTP_HOST'])
            ->issuedAt(new \DateTimeImmutable())
            ->expiresAt($expiresAt)
            ->relatedTo($userEntity->getIdentifier())
            ->withClaim('auth_time', $userEntity->getClaims()['lastlogin']);
    }

    protected function getExtraParams(AccessTokenEntityInterface $accessToken): array
    {
        if (false === $this->isOpenIDRequest($accessToken->getScopes())) {
            return [];
        }

        $userEntity = $this->identityProvider->getUserEntityByIdentifier($accessToken->getUserIdentifier());

        if (false === is_a($userEntity, UserEntityInterface::class)) {
            throw new \RuntimeException('UserEntity must implement UserEntityInterface');
        } else if (false === is_a($userEntity, ClaimSetInterface::class)) {
            throw new \RuntimeException('UserEntity must implement ClaimSetInterface');
        }

        // Add required id_token claims
        $builder = $this->getBuilder($accessToken, $userEntity);

        // Need a claim factory here to reduce the number of claims by provided scope.
        $claims = $this->claimExtractor->extract($accessToken->getScopes(), $userEntity->getClaims());

        foreach ($claims as $claimName => $claimValue) {
            $builder = $builder->withClaim($claimName, $claimValue);
        }

        if (
            method_exists($this->privateKey, 'getKeyContents')
            && !empty($this->privateKey->getKeyContents())
        ) {
            $key = InMemory::plainText($this->privateKey->getKeyContents(), (string)$this->privateKey->getPassPhrase());
        } else {
            $key = LocalFileReference::file($this->privateKey->getKeyPath(), (string)$this->privateKey->getPassPhrase());
        }

        $token = $builder->getToken(new Sha256(), $key);

        return [
            'id_token' => $token->toString()
        ];
    }

    /** @param ScopeEntityInterface[] $scopes */
    private function isOpenIDRequest(array $scopes): bool
    {
        // Verify scope and make sure openid exists.
        $valid = false;

        foreach ($scopes as $scope) {
            if ($scope->getIdentifier() === 'openid') {
                $valid = true;
                break;
            }
        }

        return $valid;
    }
}
