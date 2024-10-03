<?php

declare(strict_types=1);

namespace Your\Integration\Block\Adminhtml\System\Config;

use Magento\Backend\Model\UrlInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Your\Integration\Model\System\Config;

class ApiKey extends Field
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $url;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Context $context
     * @param UrlInterface $url
     * @param Config $config
     */
    public function __construct(
        Context $context,
        UrlInterface $url,
        Config $config,
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->url = $url;
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
        if ($this->config->getApiKey()) {
            return '';
        }

        $buttonLabel = __('Register At YOUR');
        $message = implode(' ', [
            __('YOUR API key is not provided.'),
            __('Enter the API key or click the "%1" button below.', $buttonLabel),
            '<br>',
            __('Registration will create new Magento integration with API read/write access to your catalog data.'),
            '<br>',
            __('Registration will send your user and store information to YOUR required for new account creation.')
        ]);

        $buttonUrl = $this->url->getUrl('your_integration/dashboard/register');
        $buttonHtml = "
            <div class='yi-register-button'>
                <a href='$buttonUrl' class='action-primary abs-action-l'>$buttonLabel</a>
            </div>";

        return "
            <div class='yi-config-note messages'>
                <div class='message'>$message</div>
                $buttonHtml
            </div>";
    }
}
