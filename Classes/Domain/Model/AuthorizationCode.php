<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Model;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * OAuth2 authorization code
 */
final class AuthorizationCode extends AbstractEntity implements AuthCodeEntityInterface
{
    use EntityTrait;
    use AuthCodeTrait;
    use TokenEntityTrait;
}
