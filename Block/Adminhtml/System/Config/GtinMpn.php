<?php

declare(strict_types=1);

namespace Your\Integration\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Your\Integration\Model\System\Config;

class GtinMpn extends Field
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Context $context
     * @param Config $config
     */
    public function __construct(
        Context $context,
        Config $config,
    ) {
        parent::__construct($context);
        $this->config = $config;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $comment = $element->getComment();
        $comment .= $this->getMessage();
        $element->setComment($comment);

        return parent::render($element);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        if ($this->config->hasIdentifierAttributesConfigured()) {
            return '';
        }

        $message = implode(' ', [
            __('At least one product identifier - GTIN or MPN is required.'),
            __('Configure at least one for the integration to work properly.'),
        ]);

        return "
            <div class='yi-config-note messages'>
                <div class='message'>$message</div>
            </div>";
    }
}
