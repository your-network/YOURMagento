<?php

declare(strict_types=1);

namespace Your\Integration\Service;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\Resolver;
use Magento\Store\Model\Store;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
use Your\Integration\Model\ApiRequest;

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
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ApiRequest
     */
    private ApiRequest $apiRequest;

    /**
     * @param Session $adminSession
     * @param Resolver $localeResolver
     * @param Information $information
     * @param StoreManagerInterface $storeManager
     * @param ApiRequest $apiRequest
     */
    public function __construct(
        Session $adminSession,
        Resolver $localeResolver,
        Information $information,
        StoreManagerInterface $storeManager,
        ApiRequest $apiRequest
    ) {
        $this->adminSession = $adminSession;
        $this->localeResolver = $localeResolver;
        $this->information = $information;
        $this->storeManager = $storeManager;
        $this->apiRequest = $apiRequest;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute(): void
    {
        $registrationData = $this->getRegistrationData();
        $apiKey = $this->apiRequest->postShopRegister($registrationData);
        $this->saveApiKey($apiKey);
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
                'currencyCode' => $store->getBaseCurrency()->getCode(),
                'contentLanguage' => $this->localeResolver->getLocale(),
                'website' => $store->getBaseUrl(),
                'notifications' => [
                    'email' => true
                ],
            ],
            'user' => [
                'personalName' => $adminUser->getName(),
                'website' => $store->getBaseUrl(),
                'email' => $adminUser->getEmail(),
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

    /**
     * @param string $apiKey
     */
    public function saveApiKey(string $apiKey): void
    {

    }
}
