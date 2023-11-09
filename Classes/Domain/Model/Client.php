<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Domain\Model;

use FGTCLB\OAuth2Server\Domain\Repository\ClientRepository;
use FGTCLB\OAuth2Server\Session\UserSession;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * OAuth2 client
 *
 * @phpstan-import-type TClientRow from ClientRepository
 */
final class Client extends AbstractEntity implements ClientEntityInterface
{
    use EntityTrait;

    /** @var ObjectStorage<FrontendUserGroup> */
    protected ObjectStorage $frontendUserGroups;

    /** @var string */
    protected $secret = null;

    /** @var string */
    protected $name;

    /** @var string */
    protected $redirectUris = '';

    /** @var bool */
    protected $isConfidential = true;

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->frontendUserGroups = new ObjectStorage();
    }

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

    public function getFrontendUserGroups(): ObjectStorage
    {
        return $this->frontendUserGroups;
    }

    public function setFrontendUserGroups(ObjectStorage $frontendUserGroups): void
    {
        $this->frontendUserGroups = $frontendUserGroups;
    }

    public function hasUserAccess(FrontendUserAuthentication $frontendUser): bool
    {
        $usersGroupIds = array_map(function ($data) { return $data['uid']; }, $frontendUser->userGroups);

        foreach ($this->frontendUserGroups as $frontendUserGroup) {
            if (in_array($frontendUserGroup->getUid(), $usersGroupIds)) {
                return true;
            }
        }
        
        return false;
    }
}
