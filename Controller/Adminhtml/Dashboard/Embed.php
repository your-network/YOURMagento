<?php

declare(strict_types=1);

namespace Your\Integration\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Your\Integration\Model\ApiRequest;

class Embed extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Your_Integration::your_integration_dashboard';

    /**
     * @var ApiRequest
     */
    private ApiRequest $apiRequest;

    /**
     * @param Context $context
     * @param ApiRequest $apiRequest
     */
    public function __construct(
        Context $context,
        ApiRequest $apiRequest
    ) {
        parent::__construct($context);
        $this->apiRequest = $apiRequest;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $subpath = $this->apiRequest->getMagentoApiBasePath();
        $rawResult = $this->apiRequest->getClientCode($subpath);

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)
            ->setContents($rawResult);
    }
}
