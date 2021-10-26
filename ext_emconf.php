<?php declare(strict_types = 1);
$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 OAuth2 server',
    'description' => 'OAuth2 server implementation for TYPO3 frontend users',
    'category' => 'fe',
    'state' => 'alpha',
    'author' => 'FGTCLB',
    'author_email' => 'info@fgtclb.com',
    'version' => '0.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.99.99',
            'frontend' => '9.5.0-10.99.99',
        ],
    ],
];
