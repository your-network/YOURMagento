<?php

declare(strict_types=1);

namespace Your\Integration\Model\System;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XML_PATH_ENABLED = 'your_integration/general/enabled';
    public const XML_PATH_API_KEY = 'your_integration/general/api_key';
    public const XML_PATH_SHOP_ID = 'your_integration/general/shop_id';
    public const XML_PATH_API_BASE_URL = 'your_integration/general/api_base_url';
    public const XML_PATH_MPN_ATTRIBUTE_CODE = 'your_integration/general/mpn_attribute_code';
    public const XML_PATH_GTIN_ATTRIBUTE_CODE = 'your_integration/general/gtin_attribute_code';
    public const XML_PATH_CONTENT_LANGUAGE = 'your_integration/general/content_language';
    public const XML_PATH_CLIENT_SCRIPT_PREFIX = 'your_integration/general/client_script';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function getIsConfigured(): bool
    {
        return $this->getApiKey()
            && $this->getIsEnabled()
            && $this->getClientScript()
            && $this->hasIdentifierAttributesConfigured();
    }

    /**
     * @return bool
     */
    public function hasIdentifierAttributesConfigured(): bool
    {
        return $this->getGtinAttributeCode()
            || $this->getMpnAttributeCode();
    }

    /**
     * @return bool
     */
    public function getIsEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_API_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SHOP_ID
        );
    }

    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return trim((string)$this->scopeConfig->getValue(
            self::XML_PATH_API_BASE_URL,
            ScopeInterface::SCOPE_STORE
        ), '/');
    }

    /**
     * @return string
     */
    public function getMpnAttributeCode(): string
    {
        return trim((string)$this->scopeConfig->getValue(
            self::XML_PATH_MPN_ATTRIBUTE_CODE,
            ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * @return string
     */
    public function getGtinAttributeCode(): string
    {
        return trim((string)$this->scopeConfig->getValue(
            self::XML_PATH_GTIN_ATTRIBUTE_CODE,
            ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getContentLanguage(?int $storeId = null): string
    {
        $language = (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONTENT_LANGUAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $language ?: 'en';
    }

    /**
     * @return string
     */
    public function getClientScript(): string
    {
        $configPath = implode('/', [
            self::XML_PATH_CLIENT_SCRIPT_PREFIX,
            $this->getContentLanguage()
        ]);

        return (string)$this->scopeConfig->getValue($configPath);
    }
}
