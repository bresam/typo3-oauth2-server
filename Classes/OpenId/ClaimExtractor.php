<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\OpenId;

use FGTCLB\OAuth2Server\OpenId\Entity\ClaimSetEntity;
use FGTCLB\OAuth2Server\OpenId\Entity\ClaimSetEntityInterface;
use FGTCLB\OAuth2Server\OpenId\Exception\InvalidArgumentException;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class ClaimExtractor
{
    protected array $claimSets;
    protected array $protectedClaims = ['profile', 'email', 'address', 'phone'];

    /**
     * @param ClaimSetEntity[] $claimSets
     * @throws InvalidArgumentException
     */
    public function __construct(array $claimSets = [])
    {
        // Add Default OpenID Connect Claims
        // @see http://openid.net/specs/openid-connect-core-1_0.html#ScopeClaims
        $this->addClaimSet(
            new ClaimSetEntity('profile', [
                'name',
                'family_name',
                'given_name',
                'middle_name',
                'nickname',
                'preferred_username',
                'profile',
                'picture',
                'website',
                'gender',
                'birthdate',
                'zoneinfo',
                'locale',
                'updated_at',
            ])
        );
        $this->addClaimSet(
            new ClaimSetEntity('email', [
                'email',
                'email_verified'
            ])
        );
        $this->addClaimSet(
            new ClaimSetEntity('address', [
                'address'
            ])
        );
        $this->addClaimSet(
            new ClaimSetEntity('phone', [
                'phone_number',
                'phone_number_verified'
            ])
        );

        foreach ($claimSets as $claimSet) {
            $this->addClaimSet($claimSet);
        }
    }

    /** @throws InvalidArgumentException */
    public function addClaimSet(ClaimSetEntityInterface $claimSet): self
    {
        $scope = $claimSet->getScope();

        if (in_array($scope, $this->protectedClaims) && !empty($this->claimSets[$scope])) {
            throw new InvalidArgumentException(
                sprintf("%s is a protected scope and is pre-defined by the OpenID Connect specification.", $scope)
            );
        }

        $this->claimSets[$scope] = $claimSet;

        return $this;
    }

    public function getClaimSet(string $scope): ?ClaimSetEntity
    {
        if (!$this->hasClaimSet($scope)) {
            return null;
        }

        return $this->claimSets[$scope];
    }

    public function hasClaimSet(string $scope): bool
    {
        return array_key_exists($scope, $this->claimSets);
    }

    /**
     * For given scopes and aggregated claims get all claims that have been configured on the extractor.
     *
     * @param string[] $scopes
     */
    public function extract(array $scopes, array $claims): array
    {
        $claimData  = [];
        $keys       = array_keys($claims);

        foreach ($scopes as $scope) {
            $scopeName = ($scope instanceof ScopeEntityInterface) ? $scope->getIdentifier() : $scope;

            $claimSet = $this->getClaimSet($scopeName);
            if (null === $claimSet) {
                continue;
            }

            $intersected = array_intersect($claimSet->getClaims(), $keys);

            if (empty($intersected)) {
                continue;
            }

            $data = array_filter($claims,
                function($key) use ($intersected) {
                    return in_array($key, $intersected);
                },
                ARRAY_FILTER_USE_KEY
            );

            $claimData = array_merge($claimData, $data);
        }

        return $claimData;
    }
}
