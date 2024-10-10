<?php

declare(strict_types=1);

namespace Your\Integration\Controller\Adminhtml\ApiProxy;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Your\Integration\Model\System\Config;
use Your\Integration\Model\YourApi;

class Request extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Your_Integration::your_integration_dashboard';

    /**
     * @var Json
     */
    private Json $json;

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
     * @param Json $json
     * @param Config $config
     * @param YourApi $yourApi
     */
    public function __construct(
        Context $context,
        Json $json,
        Config $config,
        YourApi $yourApi
    ) {
        parent::__construct($context);
        $this->json = $json;
        $this->config = $config;
        $this->yourApi = $yourApi;
    }

    /**
     * @return bool
     */
    public function _processUrlKeys()
    {
        $isLoggedIn = $this->_auth->isLoggedIn();

        if (!$isLoggedIn) {
            $this->_redirect($this->_backendUrl->getStartupPageUrl());
        }

        return $isLoggedIn;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $apiRequestUrl = $this->getApiRequestUrl();
        if (!$apiRequestUrl) {
            return $this->resultFactory->create(ResultFactory::TYPE_RAW)
                ->setContents(__('API Request Is Not Mapped'))
                ->setHttpResponseCode(404);
        }

        /** @var Http $request */
        $request = $this->_request;
        if ($request->getMethod() === $request::METHOD_POST) {
            $params = $this->json->unserialize(
                $request->getContent()
            );
        } else {
            $params = $request->getQuery()->toArray();
        }

        $response = $this->yourApi->makeRequest(
            $apiRequestUrl,
            $params,
            $this->config->getApiKey(),
            $request->getMethod()
        );

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)
            ->setHttpResponseCode($response->getHttpStatus())
            ->setContents($response->getResponse());
    }

    /**
     * @return string
     */
    private function getApiRequestUrl(): string
    {
        $requestPath = $this->_request->getPathInfo();
        $apiProxyUrl = $this->yourApi->getMagentoAdminApiUrl();
        $apiRequestUrl = str_replace($apiProxyUrl, '', $requestPath);

        return $this->yourApi->getMappedApiUrl($apiRequestUrl);
    }
}
