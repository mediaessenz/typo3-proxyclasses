<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "proxyclasses".
 *
 * Auto generated 22-09-2016 22:04
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Proxyclasses for TYPO3',
    'description' => 'Proxyclasses for TYPO3',
    'category' => 'misc',
    'version' => '1.0.0',
    'priority' => 'top',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'Alexander Grein',
    'author_email' => 'alexander.grein@gmail.com',
    'author_company' => 'MEDIA::ESSENZ',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-7.6.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'MediaEssenz\\Proxyclasses\\' => 'Classes',
        ],
    ],
];
