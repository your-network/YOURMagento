<?php

declare(strict_types=1);

namespace Your\Integration\Service;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Type\Config as ConfigCache;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Model\YourApi;

class ClientScriptUpdate
{
    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @var WriterInterface
     */
    private WriterInterface $configWriter;

    /**
     * @var StoreRepositoryInterface
     */
    private StoreRepositoryInterface $storeRepository;

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
     * @param WriterInterface $configWriter
     * @param StoreRepositoryInterface $storeRepository
     * @param Config $config
     * @param YourApi $yourApi
     */
    public function __construct(
        TypeListInterface $cacheTypeList,
        WriterInterface $configWriter,
        StoreRepositoryInterface $storeRepository,
        Config $config,
        YourApi $yourApi
    ) {
        $this->cacheTypeList = $cacheTypeList;
        $this->configWriter = $configWriter;
        $this->storeRepository = $storeRepository;
        $this->config = $config;
        $this->yourApi = $yourApi;
    }

    /**
     * @param bool $cleanConfigCache
     * @return bool
     */
    public function execute(bool $cleanConfigCache = true): bool
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

            $configPath = implode('/', [Config::XML_PATH_CLIENT_SCRIPT_PREFIX, $locale]);
            $this->configWriter->save($configPath, $clientScript);
        }

        if ($cleanConfigCache) {
            $this->cacheTypeList->cleanType(ConfigCache::TYPE_IDENTIFIER);
        }

        return true;
    }
}
