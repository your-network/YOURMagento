<?php

declare(strict_types=1);

namespace Your\Integration\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Embed extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Your_Integration::your_integration_dashboard';

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents('RAW RESPONSE TO BE REPLACED WITH YOUR JS & CSS');

        return $resultRaw;
    }
}
