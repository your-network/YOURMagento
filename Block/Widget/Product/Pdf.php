<?php

declare(strict_types=1);

namespace Your\Integration\Block\Widget\Product;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Your\Integration\Model\System\Config;

class Pdf extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'Your_Integration::widget/product-pdf.phtml';

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        if (!$this->config->getIsConfigured()) {
            return '';
        }

        return parent::toHtml();
    }
}
