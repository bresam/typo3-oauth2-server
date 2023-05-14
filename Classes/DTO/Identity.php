<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\DTO;

use FGTCLB\OAuth2Server\OpenId\Entity\ClaimSetInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;

final class Identity implements ClaimSetInterface, UserEntityInterface
{
    protected array $userData;

    public function __construct(array $userData)
    {
        $this->userData = $userData;
    }

    public function getClaims(): array
    {
        return $this->userData;
    }

    public function getIdentifier(): string
    {
        return (string) $this->userData['uid'];
    }
}
