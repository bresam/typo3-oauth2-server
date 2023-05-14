<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\OpenId\Repository;

use League\OAuth2\Server\Repositories\RepositoryInterface;

interface IdentityProviderInterface extends RepositoryInterface
{
    public function getUserEntityByIdentifier($identifier);
}
