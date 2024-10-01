<?php

declare(strict_types=1);

namespace Your\Integration\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Index extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Your_Integration::your_integration_dashboard';

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Your_Integration::your_integration');
        $resultPage->getConfig()->getTitle()->prepend(__('Dashboard'));

        return $resultPage;
    }
}
