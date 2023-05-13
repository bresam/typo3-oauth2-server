<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Model;

use FGTCLB\OAuth2Server\Domain\Repository\ClientRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * OAuth2 client
 *
 * @phpstan-import-type TClientRow from ClientRepository
 */
final class Client extends AbstractEntity implements ClientEntityInterface
{
    use EntityTrait;

    /** @var string */
    protected $secret = null;

    /** @var string */
    protected $name;

    /** @var string */
    protected $redirectUris = '';

    /** @var bool */
    protected $isConfidential = true;

    /**
     * Get the client's name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Return an indexed array of redirect URIs.
     *
     * @return string[]
     */
    public function getRedirectUri(): array
    {
        return $this->redirectUris ? GeneralUtility::trimExplode("\n", $this->redirectUris) : [];
    }

    /** Returns true if the client is confidential.*/
    public function isConfidential(): bool
    {
        return $this->isConfidential;
    }

    public function validateSecret(string $secret, PasswordHashFactory $hashFactory): bool
    {
        return $hashFactory
            ->get($this->secret, 'FE')
            ->checkPassword($secret, $this->secret);
    }
}
