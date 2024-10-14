<?php

declare(strict_types=1);

namespace Your\Integration\ViewModel\Adminhtml;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Your\Integration\Model\YourApi;

class Dashboard implements ArgumentInterface
{
    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param YourApi $yourApi
     */
    public function __construct(
        YourApi $yourApi
    ) {
        $this->yourApi = $yourApi;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->yourApi->replaceIntegrationPath(
            $this->yourApi->apiGetHome()->getResponse(),
            $this->yourApi->getMagentoAdminApiUrl()
        );
    }
}
