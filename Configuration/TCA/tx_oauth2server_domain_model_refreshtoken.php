<?php

return [
    'ctrl' => [
        'label' => 'identifier',
        'tstamp' => 'tstamp',
        'title' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_refreshtoken_tca.xlf:refreshtoken.title',
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
        'showRecordFieldList' => 'identifier,access_token,expiry_date_time'
    ],
    'columns' => [
        'identifier' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_refreshtoken_tca.xlf:refreshtoken.identifier.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 255,
                'eval' => 'trim',
            ]
        ],
        'access_token' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_refreshtoken_tca.xlf:refreshtoken.access_token.label',
            'config' => [
                'type' => 'input',
                'size' => 32,
                'max' => 255,
                'eval' => 'trim',
            ]
        ],
        'expiry_date_time' => [
            'label' => 'LLL:EXT:oauth2_server/Resources/Private/Language/locallang_refreshtoken_tca.xlf:refreshtoken.expiry_date_time.label',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'datetime',
                'default' => '0',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;details
            
        ']
    ],
    'palettes' => [
        'details' => ['showitem' => 'identifier, --linebreak--, access_token, --linebreak--, expiry_date_time'],
    ],
];
