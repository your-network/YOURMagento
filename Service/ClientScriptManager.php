<?php

declare(strict_types=1);

namespace Your\Integration\Service;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Type\Config as ConfigCache;
use Magento\Framework\App\Cache\Type\Block as BlockCache;
use Magento\PageCache\Model\Cache\Type as FullPageCache;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\App\CacheInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Model\YourApi;

class ClientScriptManager
{
    public const CACHE_KEY_CLIENT_SCRIPT_PREFIX = 'your_integration_client_script';
    public const CACHE_LIFETIME = 604800; // 7 days in seconds

    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @var StoreRepositoryInterface
     */
    private StoreRepositoryInterface $storeRepository;

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param TypeListInterface $cacheTypeList
     * @param StoreRepositoryInterface $storeRepository
     * @param CacheInterface $cache
     * @param Config $config
     * @param YourApi $yourApi
     */
    public function __construct(
        TypeListInterface $cacheTypeList,
        StoreRepositoryInterface $storeRepository,
        CacheInterface $cache,
        Config $config,
        YourApi $yourApi
    ) {
        $this->cacheTypeList = $cacheTypeList;
        $this->storeRepository = $storeRepository;
        $this->cache = $cache;
        $this->config = $config;
        $this->yourApi = $yourApi;
    }

    /**
     * @param bool $cleanCache
     * @return bool
     */
    public function update(bool $cleanCache = true): bool
    {
        if (!$this->config->getApiKey()) {
            return false;
        }

        $localesUsed = [];
        foreach ($this->storeRepository->getList() as $store) {
            if ($storeId = (int)$store->getId()) { // Skip admin store, ID 0
                $localesUsed[] = $this->config->getContentLanguage($storeId);
            }
        }

        if ($localesUsed && $cleanCache) {
            $this->cache->clean(self::CACHE_KEY_CLIENT_SCRIPT_PREFIX);
            $this->cacheTypeList->cleanType(BlockCache::TYPE_IDENTIFIER);
            $this->cacheTypeList->cleanType(FullPageCache::TYPE_IDENTIFIER);
        }

        foreach (array_unique($localesUsed) as $locale) {
            $apiResponse = $this->yourApi->apiGetEmbedSnippet([
                'locale' => $locale
            ]);

            if ($apiResponse->getHttpStatus() !== 200) {
                return false;
            }

            $clientScript = $apiResponse->getResponse();
            if (!$clientScript) {
                return false;
            }

            $cacheKey = $this->getCacheKey($locale);
            $this->cache->save(
                $clientScript,
                $cacheKey,
                [
                    self::CACHE_KEY_CLIENT_SCRIPT_PREFIX,
                    ConfigCache::TYPE_IDENTIFIER,
                ],
                self::CACHE_LIFETIME
            );
        }

        return true;
    }

    /**
     * @return string
     */
    public function getClientScript(): string
    {
        $locale = $this->config->getContentLanguage();
        $cacheKey = $this->getCacheKey($locale);
        $clientScript = $this->cache->load($cacheKey);

        if (!$clientScript) {
            $apiResponse = $this->yourApi->apiGetEmbedSnippet([
                'locale' => $locale
            ]);

            if ($apiResponse->getHttpStatus() !== 200) {
                return '';
            }

            $clientScript = $apiResponse->getResponse();
            if (!$clientScript) {
                return '';
            }

            $this->cache->save(
                $clientScript,
                $cacheKey,
                [
                    self::CACHE_KEY_CLIENT_SCRIPT_PREFIX,
                    ConfigCache::TYPE_IDENTIFIER,
                ],
                self::CACHE_LIFETIME
            );
        }

        return $clientScript;
    }

    /**
     * @param string $locale
     * @return string
     */
    private function getCacheKey(string $locale): string
    {
        return implode('_', [self::CACHE_KEY_CLIENT_SCRIPT_PREFIX, $locale]);
    }
}
