<?php

declare(strict_types = 1);

namespace FGTCLB\OAuth2Server\Service;

use FGTCLB\OAuth2Server\DTO\Identity;
use FGTCLB\OAuth2Server\OpenId\Repository\IdentityProviderInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileRepository;

final class IdentityProvider implements IdentityProviderInterface
{
    private const FE_USERS = 'fe_users';

    private ConnectionPool $connectionPool;
    private FileRepository $fileRepository;

    public function __construct(ConnectionPool $connectionPool, FileRepository $fileRepository)
    {
        $this->connectionPool = $connectionPool;
        $this->fileRepository = $fileRepository;
    }

    public function getUserEntityByIdentifier($identifier): Identity
    {
        $userData = $this->getUserData($identifier);

        // map profile properties to oidc specs
        if (!$userData['name']) { unset($userData['name']); }
        if (!$userData['middle_name']) { unset($userData['middle_name']); }
        if (isset($userData['lastname'])) { $userData['family_name'] = $userData['lastname']; }
        if (isset($userData['firstname'])) { $userData['given_name'] = $userData['firstname']; }
        if (isset($userData['username'])) { $userData['preferred_username'] = $userData['username']; }
        if (isset($userData['www'])) { $userData['website'] = $userData['www']; }
        if (isset($userData['tstamp'])) { $userData['updated_at'] = $userData['tstamp']; }
        if (isset($userData['telephone'])) { $userData['phone_number'] = $userData['telephone']; }

        // set address details
        $userData['address'] = [
            'street_address' => $userData['address'],
            'locality' => $userData['city'],
            'postal_code' => $userData['zip'],
            'country' => $userData['country'],
        ];

        // set profile image: uploaded profile image or gravatar url if email address is available
        if ($pictureUri = $this->getProfileImageUri($userData)) {
            $userData['picture'] = $pictureUri;
        }

        return new Identity($userData ?? []);
    }

    public function getUserInfoByIdentifier($identifier): array
    {
        $userData = $this->getUserData($identifier);

        return [
            'sub' => (string) $userData['uid'],
            'name' => (string) $userData['name'],
            'given_name' => (string) $userData['first_name'],
            'family_name' => (string) $userData['last_name'],
            'preferred_username' => (string) $userData['username'],
            'email' => (string) $userData['email'],
            'picture' => (string) $this->getProfileImageUri($userData),
        ];
    }

    private function getUserData(string $identifier): array
    {
        $qb = $this->connectionPool->getConnectionForTable(self::FE_USERS)->createQueryBuilder();

        return $qb->select('*')
            ->from(self::FE_USERS)
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($identifier))
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();
    }

    private function getProfileImageUri(array $userData): ?string
    {
        $fileReference = $this->fileRepository->findByRelation('fe_users', 'image', (int) $userData['image']);

        if (!isset($fileReference[0])) {
            if ($userData['email']) {
                // use gravatar if email address is available and no local profile image exists
                return sprintf('https://www.gravatar.com/avatar/%s', md5($userData['email']));
            }

            return null;
        }

        return sprintf(
            '%s://%s/%s',
            $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'https',
            $_SERVER['HTTP_HOST'],
            $fileReference[0]->getPublicUrl()
        );
    }
}
