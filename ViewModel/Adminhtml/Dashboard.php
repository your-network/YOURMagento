<?php

declare(strict_types=1);

namespace Your\Integration\ViewModel\Adminhtml;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Dashboard implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $url;

    /**
     * @param UrlInterface $url
     */
    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getEmbedUrl(): string
    {
        return $this->url->getUrl('your_integration/dashboard/embed');
    }
}
