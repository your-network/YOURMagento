<?php

declare(strict_types=1);

namespace Your\Integration\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Your\Integration\Model\System\Config;

class ApiRequest
{
    public const INTEGRATION_SUBPATH_STRING             = '__YOUR_INTEGRATION_SUBPATH__';
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
     * @return Curl
     */
    public function getHttpClient(): Curl
    {
        $client = $this->curlFactory->create();
        $client->setHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->config->getApiKey()
        ]);

        return $client;
    }

    /**
     * @return string
     */
    public function getYourApiUrlHome(): string
    {
        return $this->config->getApiBaseUrl() . self::ENDPOINT_PATH_HOME;
    }

    /**
     * @return string
     */
    public function getYourApiUrlShopRegister(): string
    {
        return $this->config->getApiBaseUrl() . self::ENDPOINT_PATH_SHOP_REGISTER;
    }

    /**
     * @return string
     */
    public function getMagentoApiBasePath(): string
    {
        try {
            $storeCode = $this->storeManager->getStore()->getCode();
        } catch (\Exception) {
            $storeCode = 'default';
        }

        return "/rest/$storeCode/V1/your-integration/";
    }

    /**
     * @param array $payload
     * @return string
     * @throws LocalizedException
     * @throws \InvalidArgumentException
     */
    public function postShopRegister(array $payload): string
    {
        $client = $this->getHttpClient();
        $client->post(
            $this->getYourApiUrlShopRegister(),
            $this->json->serialize($payload)
        );

        $result = $this->json->unserialize($client->getBody());
        $errors = implode(', ', $result['errors'] ?? []);

        if ($client->getStatus() !== 200) {
            if ($errors) {
                throw new LocalizedException(__($errors));
            } else {
                throw new LocalizedException(__('Non-200 Response From API'));
            }
        }

        if (isset($result['success']) && $result['success'] !== true) {
            throw new LocalizedException(__('Unsuccessful API Attempt'));
        }

        if (!isset($result['apiKey']) && !$result['apiKey']) {
            throw new LocalizedException(__('API Not Present In Response'));
        }

        return $result['apiKey'];
    }

    /**
     * @param string $replaceSubPathWith
     * @return string
     */
    public function getClientCode(string $replaceSubPathWith): string
    {
        $client = $this->getHttpClient();
        $client->get($this->getYourApiUrlHome());

        if ($client->getStatus() !== 200) {
            return __('Non-200 Response From API')->getText();
        }

        $body = $client->getBody();
        if ($replaceSubPathWith) {
            $replace = self::INTEGRATION_SUBPATH_STRING;
            $body = str_replace($replace, $replaceSubPathWith, $body);
        }

        return $body;
    }
}
