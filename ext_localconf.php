<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

call_user_func(
    function ($_EXTKEY) {
        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);

        if (trim($extensionConfiguration['ProxyClasses']) !== '') {

            $vendorExtensionkeys = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',',
                $extensionConfiguration['ProxyClasses'], true);

            foreach ($vendorExtensionkeys as $vendorExtensionkey) {
                if (strpos($vendorExtensionkey, '\\') !== false) {
                    $namespaceParts = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('\\', $vendorExtensionkey, 0,
                        (substr($vendorExtensionkey, 0, 9) === 'TYPO3\\CMS' ? 4 : 3));
                    array_pop($namespaceParts);
                    $extensionKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored(array_pop($namespaceParts));
                    // Register cache frontend for proxy class generation
                    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$extensionKey] = [
                        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
                        'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
                        'groups' => [
                            'all',
                            'system',
                        ],
                        'options' => [
                            'defaultLifetime' => 0,
                        ]
                    ];
                }
            }

            \MediaEssenz\Proxyclasses\Utility\ClassLoader::registerAutoloader();
        }
    },
    $_EXTKEY
);
