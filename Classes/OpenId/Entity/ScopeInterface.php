<?php

declare(strict_types=1);

namespace FGTCLB\OAuth2Server\OpenId\Entity;

interface ScopeInterface
{
    public function getScope(): string;
}
