<?php

declare(strict_types=1);

namespace Your\Integration\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Model\YourApi;

class Index extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Your_Integration::your_integration_dashboard';

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param Context $context
     * @param Config $config
     * @param YourApi $yourApi
     */
    public function __construct(
        Context $context,
        Config $config,
        YourApi $yourApi
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->yourApi = $yourApi;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        if (!$this->config->getApiKey()) {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                ->setUrl($this->getUrl('adminhtml/system_config/edit/section/your_integration'));
        }

        $response = $this->yourApi->replaceIntegrationPath(
            $this->yourApi->apiGetHome()->getResponse(),
            $this->yourApi->getMagentoAdminApiUrl()
        );

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents($response);
    }
}
