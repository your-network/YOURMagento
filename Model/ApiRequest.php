<?php

declare(strict_types=1);

namespace Your\Integration\Model;

use Magento\Framework\HTTP\LaminasClient;
use Magento\Framework\HTTP\LaminasClientFactory;
use Your\Integration\Model\System\Config;

class ApiRequest
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

    /**
     * @var LaminasClientFactory
     */
    private LaminasClientFactory $laminasClientFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param LaminasClientFactory $laminasClientFactory
     * @param Config $config
     */
    public function __construct(
        LaminasClientFactory $laminasClientFactory,
        Config $config
    ) {
        $this->laminasClientFactory = $laminasClientFactory;
        $this->config = $config;
    }

    /**
     * @return LaminasClient
     */
    public function getHttpClient(): LaminasClient
    {
        /** @var LaminasClient $client */
        $client = $this->laminasClientFactory->create();

        $client->setOptions([
            'maxredirects' => 0,
            'timeout' => 60
        ]);

        $client->setHeaders([
            'Accept' => 'application/json',
            'Authorization' => $this->config->getApiKey()
        ]);

        return $client;
    }
}
