<?php

return [
    'ctrl' => [
        'label' => 'identifier',
        'tstamp' => 'tstamp',
        'title' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_authorizationcode_tca.xlf:authorizationcode.title',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'adminOnly' => true,
        'rootLevel' => 1,
        'default_sortby' => 'identifier',
        'enablecolumns' => [],
        'searchFields' => 'identifier'
    ],
    'interface' => [
        'showRecordFieldList' => 'identifier,redirect_uri,user_identifier,scopes,client,expiry_date_time,nonce'
    ],
    'columns' => [
        'identifier' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_authorizationcode_tca.xlf:authorizationcode.identifier.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 32,
                'eval' => 'trim,required',
            ]
        ],
        'redirect_uri' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_authorizationcode_tca.xlf:authorizationcode.redirect_uri.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 255,
                'eval' => 'trim',
            ]
        ],
        'user_identifier' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_authorizationcode_tca.xlf:authorizationcode.user_identifier.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 255,
                'eval' => 'trim',
            ]
        ],
        'scopes' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_authorizationcode_tca.xlf:authorizationcode.scopes.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 255,
                'eval' => 'trim',
            ]
        ],
        'client' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_authorizationcode_tca.xlf:authorizationcode.client.label',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_oauth2server_domain_model_client',
                'maxitems' => 1,
                'minitems' => 1,
            ],
            'exclude' => false,
        ],
        'expiry_date_time' => [
            'exclude' => false,
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_authorizationcode_tca.xlf:authorizationcode.expiry_date_time.label',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'datetime',
                'default' => '0',
            ],
        ],
        'nonce' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_authorizationcode_tca.xlf:authorizationcode.nonce.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 255,
                'eval' => 'trim',
            ]
        ]
    ],
    'types' => [
        '0' => ['showitem' => '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;details
            
        ']
    ],
    'palettes' => [
        'details' => ['showitem' => 'identifier, --linebreak--, redirect_uri, --linebreak--, user_identifier, --linebreak--, scopes, --linebreak--, client, --linebreak--, expiry_date_time --linebreak--, nonce'],
    ],
];
