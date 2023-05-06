<?php

return [
    'ctrl' => [
        'label' => 'identifier',
        'descriptionColumn' => 'scopes',
        'tstamp' => 'tstamp',
        'title' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_accesstoken_tca.xlf:accesstoken.title',
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
        'showRecordFieldList' => 'identifier,scopes,client_id,expiry_date,revoked'
    ],
    'columns' => [
        'identifier' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_accesstoken_tca.xlf:accesstoken.identifier.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 32,
                'eval' => 'trim,required',
            ]
        ],
        'scopes' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_accesstoken_tca.xlf:accesstoken.scopes.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 255,
                'eval' => 'trim',
            ]
        ],
        'client_id' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_accesstoken_tca.xlf:accesstoken.client_id.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 100,
                'eval' => 'trim,required'
            ]
        ],
        'expiry_date' => [
            'exclude' => false,
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_accesstoken_tca.xlf:accesstoken.expiry_date.label',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'datetime',
                'default' => '0',
            ],
        ],
        'revoked' => [
            'exclude' => false,
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_accesstoken_tca.xlf:accesstoken.revoked.label',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'datetime',
                'default' => '0',
            ],
        ]
    ],
    'types' => [
        '0' => ['showitem' => '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;details
            
        ']
    ],
    'palettes' => [
        'details' => ['showitem' => 'identifier, --linebreak--, scopes, --linebreak--, client_id, --linebreak--, expiry_date, --linebreak--, revoked'],
    ],
];
