<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\OpenId\Entity;

class ClaimSetEntity implements ClaimSetEntityInterface
{
    protected string $scope;
    protected array $claims;

    public function __construct(string $scope, array $claims)
    {
        $this->scope = $scope;
        $this->claims = $claims;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getClaims(): array
    {
        return $this->claims;
    }
}
