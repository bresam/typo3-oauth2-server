<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\OpenId\Repository;

interface ClaimSetRepositoryInterface
{
    public function getClaimSetByScopeIdentifier($scopeIdentifier);
}
