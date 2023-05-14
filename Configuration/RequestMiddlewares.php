<?php

declare(strict_types = 1);

use FGTCLB\OAuth2Server\Middleware\OAuth2AccessToken;
use FGTCLB\OAuth2Server\Middleware\OAuth2Authorization;

return [
    'frontend' => [
        'fgtclb/typo3-oauth-server/authorization' => [
            'target' => OAuth2Authorization::class,
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
            'before' => [
                'typo3/cms-frontend/static-route-resolver',
            ],
        ],
        // "typo3/cms-frontend/maintenance-mode" is the earliest non-internal middleware
        'fgtclb/typo3-oauth-server/token' => [
            'target' => OAuth2AccessToken::class,
            'after' => [
                'typo3/cms-frontend/maintenance-mode',
            ],
            'before' => [
                'typo3/cms-frontend/static-route-resolver',
            ],
        ],
    ],
];
