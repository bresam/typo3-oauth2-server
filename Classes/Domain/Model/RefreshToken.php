<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Model;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * OAuth2 refresh token
 */
final class RefreshToken extends AbstractEntity implements RefreshTokenEntityInterface
{
    use EntityTrait;
    use RefreshTokenTrait;
}
