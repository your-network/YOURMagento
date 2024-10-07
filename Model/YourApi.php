<?php

declare(strict_types=1);

namespace Your\Integration\Model;

use Laminas\Http\Request;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Your\Integration\Model\System\Config;

class YourApi
{
    public const ENDPOINT_PATH_PRODUCT_TITLE            = '/Magento/Product/Title';
    public const ENDPOINT_PATH_PRODUCT_DESCRIPTION      = '/Magento/Product/Description';
    public const ENDPOINT_PATH_PRODUCT_PROS_CONS        = '/Magento/Product/ProsCons';
    public const ENDPOINT_PATH_PRODUCT_IMAGES           = '/Magento/Product/Images';
    public const ENDPOINT_PATH_PRODUCT_MEDIA            = '/Magento/Product/Media';
    public const ENDPOINT_PATH_PRODUCT_BULLETS          = '/Magento/Product/Bullets';
    public const ENDPOINT_PATH_PRODUCT_REVIEWS          = '/Magento/Product/Reviews';
    public const ENDPOINT_PATH_PRODUCT_REASONS_TO_BUY   = '/Magento/Product/ReasonsToBuy';
    public const ENDPOINT_PATH_PRODUCT_SPECIFICATIONS   = '/Magento/Product/Specifications';
    public const ENDPOINT_PATH_PRODUCT_QA_QUESTIONS     = '/Magento/Product/QnA/Questions';

    public const ENDPOINT_PATH_QA_QUESTION_ANSWERS          = '/Magento/QnA/Question/{questionId}/Answers';
    public const ENDPOINT_PATH_PAYMENT_STRIPE_SETUP_INTENT  = '/Magento/Payment/Stripe/SetupIntent';
    public const ENDPOINT_PATH_ACTIVITY_SHOP_TOPIC          = '/Magento/Activity/Shop/Topic';
    public const ENDPOINT_PATH_EMBED_SNIPPET                = '/Magento/Embed/Snippet/{locale}.js';
    public const ENDPOINT_PATH_CATALOG_DOWNLOAD             = '/Magento/Catalog/Download';
    public const ENDPOINT_PATH_CONTENT_BLOCKS               = '/Magento/ContentBlocks';
    public const ENDPOINT_PATH_STYLING                      = '/Magento/Styling';

    public const ENDPOINT_PATH_SUBSCRIPTION                 = '/Magento/Subscription';
    public const ENDPOINT_PATH_SUBSCRIPTION_MODELS          = '/Magento/Subscription/Models';
    public const ENDPOINT_PATH_SUBSCRIPTION_DOWNGRADE       = '/Magento/Subscription/Downgrade';
    public const ENDPOINT_PATH_SUBSCRIPTION_COST_PREDICTION = '/Magento/Subscription/Cost/Prediction';

    public const ENDPOINT_PATH_HOME         = '/Magento';
    public const ENDPOINT_PATH_HOME_SETUP   = '/Magento/setup';
    public const ENDPOINT_PATH_HOME_SUBPATH = '/Magento/{subpath}';

    public const ENDPOINT_PATH_SHOP                 = '/Magento/Shop';
    public const ENDPOINT_PATH_SHOP_REGISTER        = '/Magento/Shop/Register';
    public const ENDPOINT_PATH_SHOP_BILLING_DETAILS = '/Magento/Shop/Billing/Details';

    public const ENDPOINT_PATH_STATS_DASHBOARD          = '/Magento/Stats/Dashboard';
    public const ENDPOINT_PATH_STATS_REQUESTS_GRAPHS    = '/Magento/Stats/Requests/Graphs';
    public const ENDPOINT_PATH_STATS_REQUESTS_PRODUCTS  = '/Magento/Stats/Requests/Products';

    public const INTEGRATION_SUBPATH_STRING = '__YOUR_INTEGRATION_SUBPATH__';
    public const REGISTRATION_API_KEY       = 'afc29d1a-8f4b-4e99-a8b9-3e23f7f7602d';

    /**
     * @var CurlFactory
     */
    private CurlFactory $curlFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param CurlFactory $curlFactory
     * @param StoreManagerInterface $storeManager
     * @param Json $json
     * @param Config $config
     */
    public function __construct(
        CurlFactory $curlFactory,
        StoreManagerInterface $storeManager,
        Json $json,
        Config $config
    ) {
        $this->curlFactory = $curlFactory;
        $this->storeManager = $storeManager;
        $this->json = $json;
        $this->config = $config;
    }

    /**
     * @param string $replaceIn
     * @param string $replaceWith
     * @return string
     */
    public function replaceIntegrationPath(string $replaceIn, string $replaceWith): string
    {
        return str_replace(self::INTEGRATION_SUBPATH_STRING, $replaceWith, $replaceIn);
    }

    /**
     * @return string
     */
    public function getMagentoApiUrl(): string
    {
        try {
            $storeCode = $this->storeManager->getStore()->getCode();
            return '/rest/' . $storeCode . '/V1/your-integration/';
        } catch (\Exception) {
            return '/rest/default/V1/your-integration/';
        }
    }

    /**
     * @param string $endpoint
     * @return string
     */
    public function getYourApiUrl(string $endpoint): string
    {
        return $this->config->getApiBaseUrl() . $endpoint;
    }

    /**
     * @param string $uri
     * @param array $params
     * @param string $apiKey
     * @param bool $jsonDecodeResponse
     * @param string $method
     * @return array|string
     * @throws LocalizedException
     */
    public function makeRequest(
        string $uri,
        array $params = [],
        string $apiKey = '',
        bool $jsonDecodeResponse = false,
        string $method = Request::METHOD_GET,
    ): array|string {
        $client = $this->curlFactory->create();
        $client->addHeader('Content-Type', 'application/json');

        if ($apiKey) {
            $client->addHeader('Authorization', 'Basic ' . $apiKey);
        }

        if ($method === Request::METHOD_POST) {
            $client->post($uri, $this->json->serialize($params));
        } else {
            $client->get($uri);
        }

        $response = $client->getBody();
        if ($client->getStatus() !== 200) {
            throw new LocalizedException(__('Non-200 Response: %1', $response));
        }

        if (!$jsonDecodeResponse) {
            return $response;
        }

        $jsonResponse = $this->json->unserialize($response);
        if (isset($jsonResponse['success']) && $jsonResponse['success'] !== true) {
            throw new LocalizedException(__('Unsuccessful API Attempt: %1', $response));
        }

        return $jsonResponse;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function apiGetHome(): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_HOME)
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductTitle(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_TITLE),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductDescription(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_DESCRIPTION),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductProsCons(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_PROS_CONS),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductImages(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_IMAGES),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductMedia(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_MEDIA),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductBullets(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_BULLETS),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductReviews(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_REVIEWS),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductReasonsToBuy(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_REASONS_TO_BUY),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductSpecifications(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_SPECIFICATIONS),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function apiGetProductQnaQuestions(): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_QA_QUESTIONS),
            [],
            $this->config->getApiKey(),
            true
        );
    }

    /**
     * @param array $payload
     * @return array
     * @throws LocalizedException
     */
    public function apiPostShopRegister(array $payload): array
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_SHOP_REGISTER),
            $payload,
            self::REGISTRATION_API_KEY,
            true,
            Request::METHOD_POST
        );
    }
}
