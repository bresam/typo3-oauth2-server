<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\OpenId\Entity;

interface ClaimSetInterface
{
    public function getClaims(): array;
}
