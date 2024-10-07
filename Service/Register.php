<?php

declare(strict_types=1);

namespace Your\Integration\Service;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Type\Config as ConfigCache;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Model\YourApi;

class Register
{
    /**
     * @var Session
     */
    private Session $adminSession;

    /**
     * @var Resolver
     */
    private Resolver $localeResolver;

    /**
     * @var Information
     */
    private Information $information;

    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var WriterInterface
     */
    private WriterInterface $configWriter;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param Session $adminSession
     * @param Resolver $localeResolver
     * @param Information $information
     * @param TypeListInterface $cacheTypeList
     * @param StoreManagerInterface $storeManager
     * @param WriterInterface $configWriter
     * @param Config $config
     * @param YourApi $yourApi
     */
    public function __construct(
        Session $adminSession,
        Resolver $localeResolver,
        Information $information,
        TypeListInterface $cacheTypeList,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter,
        Config $config,
        YourApi $yourApi
    ) {
        $this->adminSession = $adminSession;
        $this->localeResolver = $localeResolver;
        $this->information = $information;
        $this->cacheTypeList = $cacheTypeList;
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
        $this->config = $config;
        $this->yourApi = $yourApi;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute(): void
    {
        if ($this->config->getApiKey()) {
            return;
        }

        $result = $this->yourApi->apiPostShopRegister(
            $this->getRegistrationData()
        );

        if (isset($result['apiKey'])) {
            $this->configWriter->save(Config::XML_PATH_API_KEY, $result['apiKey']);
            $this->cacheTypeList->cleanType(ConfigCache::TYPE_IDENTIFIER);
        }
    }

    /**
     * @return array
     */
    public function getRegistrationData(): array
    {
        /** @var Store $store */
        try {
            $store = $this->storeManager->getStore();
            $adminUser = $this->adminSession->getUser();
        } catch (\Exception) {
            return [];
        }

        $storeInformation = $this->information
            ->getStoreInformationObject($store);

        $magentoId = 'y92sorilnvujbfhvh42t5ztz3uchn4dj';
        $magentoApiKey = 'y92sorilnvujbfhvh42t5ztz3uchn4dj';
        $embedWebhookUrl = '';

        $website = $store->getBaseUrl();
        $locale = $this->localeResolver->getLocale();
        $currencyCode = $store->getBaseCurrency()->getCode();
        $languageCode = strtolower(strstr($locale, '_', true));

        return [
            'magentoId' => $magentoId,
            'magentoApiKey' => $magentoApiKey,
            'embedWebhookUrl' => $embedWebhookUrl,
            'organization' => [
                'name' => $storeInformation->getName(),
                'city' => $storeInformation->getCity(),
                'address' => $storeInformation->getData('street_line1'),
                'houseNumber' => $storeInformation->getData('street_line2'),
                'zipCode' => $storeInformation->getPostcode(),
                'country' => $storeInformation->getCountryId(),
                'phoneNumber' => $storeInformation->getPhone(),
                'vatNumber' => $storeInformation->getVatNumber(),
                'currencyCode' => $currencyCode,
                'contentLanguage' => $languageCode,
                'website' => $website,
                'notifications' => [
                    'email' => true
                ],
            ],
            'user' => [
                'website' => $website,
                'email' => $adminUser->getEmail(),
                'personalName' => $adminUser->getName(),
            ]
        ];
    }

    /**
     * @return array
     */
    public function createIntegration(): array
    {
        return [];
    }
}
