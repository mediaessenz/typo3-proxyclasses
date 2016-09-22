<?php
namespace MediaEssenz\Proxyclasses\Utility;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ClassLoader
 */
class ClassLoader implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend
     */
    protected $cacheInstance;

    /**
     * @var array
     */
    protected $vendorExtensionkeys;

    public function __construct()
    {
        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['proxyclasses']);
        $this->vendorExtensionkeys = GeneralUtility::trimExplode(',', $extensionConfiguration['ProxyClasses'], true);
    }

    /**
     * Register instance of this class as spl autoloader
     *
     * @return void
     */
    public static function registerAutoloader()
    {
        spl_autoload_register([new self(), 'loadClass'], true, true);
    }

    /**
     * Initialize cache
     * @param string $extensionkey
     *
     * @return \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend
     */
    public function initializeCache($extensionkey)
    {
        if (is_null($this->cacheInstance)) {
            /** @var CacheManager $cacheManager */
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            $this->cacheInstance = $cacheManager->getCache($extensionkey);
        }
        return $this->cacheInstance;
    }

    /**
     * Loads php files containing classes or interfaces part of the
     * classes directory of an extension.
     *
     * @param string $className Name of the class/interface to load
     * @return bool
     */
    public function loadClass($className)
    {
        $className = ltrim($className, '\\');

        if (!$this->isValidClassName($className)) {
            return false;
        }

        $extensionKey = self::getExtensionKey($className);

        $cacheEntryIdentifier = 'tx_' . $extensionKey . '_' . strtolower(str_replace('/', '_', self::changeClassName($className, self::getNamespacePart($className))));

        $classCache = $this->initializeCache($extensionKey);
        if (!empty($cacheEntryIdentifier) && !$classCache->has($cacheEntryIdentifier)) {

            /** @var ClassCacheManager $classCacheManager */
            $classCacheManager = GeneralUtility::makeInstance(ClassCacheManager::class, $extensionKey);
            $classCacheManager->reBuild($extensionKey);
        }

        if (!empty($cacheEntryIdentifier) && $classCache->has($cacheEntryIdentifier)) {
            $classCache->requireOnce($cacheEntryIdentifier);
        }

        return true;
    }

    /**
     * Get extension key from namespaced classname
     *
     * @param string $className
     * @return string
     */
    protected static function getExtensionKey($className)
    {
        $extensionKey = null;

        if (strpos($className, '\\') !== false) {
            $namespaceParts = GeneralUtility::trimExplode('\\', $className, 0,
                (substr($className, 0, 9) === 'TYPO3\\CMS' ? 4 : 3));
            array_pop($namespaceParts);
            $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored(array_pop($namespaceParts));
        }

        return $extensionKey;
    }

    /**
     * @param string $className
     * @return string
     */
    protected static function getNamespacePart($className)
    {
        $namespaceParts = GeneralUtility::trimExplode('\\', $className, 0, (substr($className, 0, 9) === 'TYPO3\\CMS' ? 4 : 3));

        return $namespaceParts[0] . '\\' . $namespaceParts[1] . '\\';
    }

    /**
     * Find out if a class name is valid
     *
     * @param string $className
     * @return bool
     */
    protected function isValidClassName($className)
    {
        foreach ($this->vendorExtensionkeys as $vendorExtensionkey) {
            if (GeneralUtility::isFirstPartOfStr($className, $vendorExtensionkey)) {
                $modifiedClassName = self::changeClassName($className, $vendorExtensionkey);
                if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT'][self::getExtensionKey($vendorExtensionkey)]['classes'][$modifiedClassName])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $className
     * @param string $vendorExtensionkey
     * @return string
     */
    protected static function changeClassName($className, $vendorExtensionkey)
    {
        return str_replace('\\', '/', str_replace($vendorExtensionkey, '', $className));
    }
}