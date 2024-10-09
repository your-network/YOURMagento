<?php

declare(strict_types=1);

namespace Your\Integration\Model;

use Laminas\Http\Request;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Your\Integration\Model\System\Config;

class YourApi
{
    public const ENDPOINT_PATH_ACTIVITY_SHOP_TOPIC = '/Magento/Activity/Shop/Topic';
    public const ENDPOINT_PATH_CATALOG_DOWNLOAD    = '/Magento/Catalog/Download';
    public const ENDPOINT_PATH_CONTENT_BLOCKS      = '/Magento/ContentBlocks';

    public const ENDPOINT_PATH_HOME          = '/Magento';
    public const ENDPOINT_PATH_HOME_SETUP    = '/Magento/setup';
    public const ENDPOINT_PATH_HOME_SUBPATH  = '/Magento/{subpath}';
    public const ENDPOINT_PATH_EMBED_SNIPPET = '/Magento/Embed/Snippet/{locale}.js';

    public const ENDPOINT_PATH_PRODUCT_TITLE          = '/Magento/Product/Title';
    public const ENDPOINT_PATH_PRODUCT_DESCRIPTION    = '/Magento/Product/Description';
    public const ENDPOINT_PATH_PRODUCT_IMAGES         = '/Magento/Product/Images';
    public const ENDPOINT_PATH_PRODUCT_MEDIA          = '/Magento/Product/Media';
    public const ENDPOINT_PATH_PRODUCT_PROS_CONS      = '/Magento/Product/ProsCons';
    public const ENDPOINT_PATH_PRODUCT_BULLETS        = '/Magento/Product/Bullets';
    public const ENDPOINT_PATH_PRODUCT_REASONS_TO_BUY = '/Magento/Product/ReasonsToBuy';
    public const ENDPOINT_PATH_PRODUCT_SPECIFICATIONS = '/Magento/Product/Specifications';
    public const ENDPOINT_PATH_PRODUCT_REVIEWS        = '/Magento/Product/Reviews';
    public const ENDPOINT_PATH_PRODUCT_QA_QUESTIONS   = '/Magento/Product/QnA/Questions';
    public const ENDPOINT_PATH_QA_QUESTION_ANSWERS    = '/Magento/QnA/Question/{questionId}/Answers';

    public const ENDPOINT_PATH_SHOP                 = '/Magento/Shop';
    public const ENDPOINT_PATH_SHOP_REGISTER        = '/Magento/Shop/Register';
    public const ENDPOINT_PATH_SHOP_BILLING_DETAILS = '/Magento/Shop/Billing/Details';

    public const ENDPOINT_PATH_STATS_DASHBOARD         = '/Magento/Stats/Dashboard';
    public const ENDPOINT_PATH_STATS_REQUESTS_GRAPH    = '/Magento/Stats/Requests/Graph';
    public const ENDPOINT_PATH_STATS_REQUESTS_PRODUCTS = '/Magento/Stats/Requests/Products';

    public const ENDPOINT_PATH_STYLING                      = '/Magento/Styling';
    public const ENDPOINT_PATH_SUBSCRIPTION                 = '/Magento/Subscription';
    public const ENDPOINT_PATH_SUBSCRIPTION_MODELS          = '/Magento/Subscription/Models';
    public const ENDPOINT_PATH_SUBSCRIPTION_DOWNGRADE       = '/Magento/Subscription/Downgrade';
    public const ENDPOINT_PATH_SUBSCRIPTION_COST_PREDICTION = '/Magento/Subscription/Cost/Prediction';
    public const ENDPOINT_PATH_PAYMENT_STRIPE_SETUP_INTENT  = '/Magento/Payment/Stripe/SetupIntent';

    public const INTEGRATION_SUBPATH_STRING = '__YOUR_INTEGRATION_SUBPATH__';
    public const REGISTRATION_API_KEY       = 'afc29d1a-8f4b-4e99-a8b9-3e23f7f7602d';

    /**
     * What URL is requested in Magento -> What YOUR API URL must be called
     */
    private const API_REQUEST_URL_MAPPING = [
        'Activity/Shop/Topic' => self::ENDPOINT_PATH_ACTIVITY_SHOP_TOPIC,
        'Catalog/Download' => self::ENDPOINT_PATH_CATALOG_DOWNLOAD,
        'ContentBlocks' => self::ENDPOINT_PATH_CONTENT_BLOCKS,
        'Stats/Dashboard' => self::ENDPOINT_PATH_STATS_DASHBOARD,
        'Stats/Requests/Graph' => self::ENDPOINT_PATH_STATS_REQUESTS_GRAPH,
        'Stats/Requests/Products' => self::ENDPOINT_PATH_STATS_REQUESTS_PRODUCTS,
        'Shop/Billing/Details' => self::ENDPOINT_PATH_SHOP_BILLING_DETAILS,
        'Styling' => self::ENDPOINT_PATH_STYLING,
        'Subscription' => self::ENDPOINT_PATH_SUBSCRIPTION,
        'Subscription/Models' => self::ENDPOINT_PATH_SUBSCRIPTION_MODELS,
        'Subscription/Downgrade' => self::ENDPOINT_PATH_SUBSCRIPTION_DOWNGRADE,
        'Subscription/Cost/Prediction' => self::ENDPOINT_PATH_SUBSCRIPTION_COST_PREDICTION,
        'Payment/Stripe/SetupIntent' => self::ENDPOINT_PATH_PAYMENT_STRIPE_SETUP_INTENT,
    ];

    /**
     * @var UrlInterface
     */
    private UrlInterface $backendUrl;

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
     * @param UrlInterface $backendUrl
     * @param CurlFactory $curlFactory
     * @param StoreManagerInterface $storeManager
     * @param Json $json
     * @param Config $config
     */
    public function __construct(
        UrlInterface $backendUrl,
        CurlFactory $curlFactory,
        StoreManagerInterface $storeManager,
        Json $json,
        Config $config
    ) {
        $this->backendUrl = $backendUrl;
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
    public function getMagentoAdminApiUrl(): string
    {
        $this->backendUrl->turnOffSecretKey();
        $url = $this->backendUrl->getUrl('your_integration/apiProxy/request');
        $this->backendUrl->turnOnSecretKey();

        return parse_url($url, PHP_URL_PATH);
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
     * @param string $endpoint
     * @return string
     */
    public function getMappedApiUrl(string $endpoint): string
    {
        $mappedUrl = self::API_REQUEST_URL_MAPPING[$endpoint] ?? '';

        if ($mappedUrl) {
            return $this->getYourApiUrl($mappedUrl);
        }

        return '';
    }

    /**
     * @param string $uri
     * @param array $params
     * @param string $apiKey
     * @param string $method
     * @param bool $returnClient
     * @return string|Curl
     */
    public function makeRequest(
        string $uri,
        array $params = [],
        string $apiKey = '',
        string $method = Request::METHOD_GET,
        bool $returnClient = false
    ): string|Curl {
        $client = $this->curlFactory->create();
        $client->addHeader('Content-Type', 'application/json');

        if ($apiKey) {
            $client->addHeader('Authorization', 'Basic ' . $apiKey);
        }

        if ($method === Request::METHOD_POST) {
            $client->post($uri, $this->json->serialize($params));
        } else {
            $client->get($params ? $uri . '?' . http_build_query($params) : $uri);
        }

        if ($returnClient) {
            return $client;
        }

        return $client->getBody();
    }

    /**
     * @return string
     */
    public function apiGetHome(): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_HOME)
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiPostShopRegister(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_SHOP_REGISTER),
            $params,
            self::REGISTRATION_API_KEY,
            Request::METHOD_POST
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetEmbedSnippet(array $params): string
    {
        $url = $this->getYourApiUrl(self::ENDPOINT_PATH_EMBED_SNIPPET);
        $url = str_replace('{locale}.js', $params['locale'] ?? '', $url);

        return $this->makeRequest(
            $url,
            [],
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductTitle(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_TITLE),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductDescription(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_DESCRIPTION),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductProsCons(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_PROS_CONS),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductImages(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_IMAGES),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductMedia(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_MEDIA),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductBullets(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_BULLETS),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductReviews(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_REVIEWS),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductReasonsToBuy(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_REASONS_TO_BUY),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductSpecifications(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_SPECIFICATIONS),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetProductQnAQuestions(array $params): string
    {
        return $this->makeRequest(
            $this->getYourApiUrl(self::ENDPOINT_PATH_PRODUCT_QA_QUESTIONS),
            $params,
            $this->config->getApiKey()
        );
    }

    /**
     * @param array $params
     * @return string
     */
    public function apiGetQnAQuestionAnswers(array $params): string
    {
        $url = $this->getYourApiUrl(self::ENDPOINT_PATH_QA_QUESTION_ANSWERS);
        $url = str_replace('{questionId}', $params['questionId'] ?? '', $url);
        unset($params['questionId']);

        return $this->makeRequest(
            $url,
            $params,
            $this->config->getApiKey()
        );
    }
}
