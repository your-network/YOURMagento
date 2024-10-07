<?php

declare(strict_types=1);

namespace Your\Integration\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Your\Integration\Model\YourApi;

class Embed extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Your_Integration::your_integration_dashboard';

    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param Context $context
     * @param YourApi $yourApi
     */
    public function __construct(
        Context $context,
        YourApi $yourApi
    ) {
        parent::__construct($context);
        $this->yourApi = $yourApi;
    }

    /**
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        $rawResult = $this->yourApi->apiGetHome();
        $magentoApiUrl = $this->yourApi->getMagentoApiUrl();
        $rawResult = $this->yourApi->replaceIntegrationPath($rawResult, $magentoApiUrl);

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents($rawResult);
    }
}
