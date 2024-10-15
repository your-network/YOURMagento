<?php

declare(strict_types=1);

namespace Your\Integration\Service;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Type\Config as ConfigCache;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\Store;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Model\YourApi;

class Register
{
    public const WEBHOOK_URL_PATH = 'rest/all/V1/your-integration/ClientScriptWebhook';

    /**
     * @var Session
     */
    private Session $adminSession;

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
     * @var Json
     */
    private Json $json;

    /**
     * @var ClientScriptUpdate
     */
    private ClientScriptUpdate $clientScriptUpdate;

    /**
     * @var IntegrationManager
     */
    private IntegrationManager $integrationManager;

    /**
     * @var ShopIdGenerator
     */
    private ShopIdGenerator $shopIdGenerator;

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
     * @param Information $information
     * @param TypeListInterface $cacheTypeList
     * @param StoreManagerInterface $storeManager
     * @param WriterInterface $configWriter
     * @param Json $json
     * @param ClientScriptUpdate $clientScriptUpdate
     * @param IntegrationManager $integrationManager
     * @param ShopIdGenerator $shopIdGenerator
     * @param Config $config
     * @param YourApi $yourApi
     */
    public function __construct(
        Session $adminSession,
        Information $information,
        TypeListInterface $cacheTypeList,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter,
        Json $json,
        ClientScriptUpdate $clientScriptUpdate,
        IntegrationManager $integrationManager,
        ShopIdGenerator $shopIdGenerator,
        Config $config,
        YourApi $yourApi
    ) {
        $this->adminSession = $adminSession;
        $this->information = $information;
        $this->cacheTypeList = $cacheTypeList;
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
        $this->json = $json;
        $this->clientScriptUpdate = $clientScriptUpdate;
        $this->integrationManager = $integrationManager;
        $this->shopIdGenerator = $shopIdGenerator;
        $this->config = $config;
        $this->yourApi = $yourApi;
    }

    /**
     * @return void
     * @throws LocalizedException|\Exception
     */
    public function execute(): void
    {
        $result = $this->yourApi->apiPostShopRegister($this->getRegistrationData())
            ->getResponse();

        try {
            $result = $this->json->unserialize($result);
        } catch (\Exception) {
            throw new LocalizedException(__('Invalid API Response During Registration'));
        }

        if (isset($result['success']) && $result['success'] === false) {
            throw new \Exception($result['message'] ?? __('API Responded With An Error'));
        }

        if (!isset($result['apiKey'])) {
            throw new LocalizedException(__('No API Key Provided In Registration Response'));
        }

        $this->configWriter->save(Config::XML_PATH_API_KEY, $result['apiKey']);
        $this->cacheTypeList->cleanType(ConfigCache::TYPE_IDENTIFIER);
        $this->clientScriptUpdate->execute();
    }

    /**
     * @return array
     */
    private function getRegistrationData(): array
    {
        /** @var Store $store */
        try {
            $store = $this->storeManager->getStore();
            $embedWebhookUrl = $store->getUrl(null, [
                '_direct' => self::WEBHOOK_URL_PATH
            ]);
            $adminUser = $this->adminSession->getUser();
            $magentoId = $this->shopIdGenerator->getShopId();
        } catch (\Exception) {
            return [];
        }

        $integrationCredentials = $this->integrationManager->getIntegrationCredentials();
        $storeInformation = $this->information->getStoreInformationObject($store);

        $website = $store->getBaseUrl();
        $currencyCode = $store->getBaseCurrency()->getCode();
        $languageCode = $this->config->getContentLanguage();
        $organizationName = $storeInformation->getName() ?: $website;

        return [
            'magentoId' => $magentoId,
            'magentoApiKey' => $integrationCredentials['access_token'],
            'embedWebhookUrl' => $embedWebhookUrl,
            'organization' => [
                'name' => $organizationName,
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
}
