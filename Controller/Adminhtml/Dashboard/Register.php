<?php

declare(strict_types=1);

namespace Your\Integration\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Service\Register as RegisterService;

class Register extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Your_Integration::your_integration_dashboard';

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var RegisterService
     */
    private RegisterService $registerService;

    /**
     * @param Context $context
     * @param Config $config
     * @param RegisterService $registerService
     */
    public function __construct(
        Context $context,
        Config $config,
        RegisterService $registerService
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->registerService = $registerService;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
            ->setUrl($this->getUrl('adminhtml/system_config/edit/section/your_integration'));

        if ($this->config->getApiKey()) {
            return $result;
        }

        try {
            $this->registerService->execute();
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $result;
    }
}
