<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Model;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * OAuth2 scope
 */
final class Scope extends AbstractEntity implements ScopeEntityInterface
{
    use EntityTrait;

    public function jsonSerialize(): mixed
    {
        return $this->identifier;
    }
}
