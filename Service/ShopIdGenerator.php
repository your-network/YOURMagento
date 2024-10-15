<?php

declare(strict_types=1);

namespace Your\Integration\Service;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Your\Integration\Model\System\Config;

class ShopIdGenerator
{
    /**
     * @var WriterInterface
     */
    private WriterInterface $configWriter;

    /**
     * @var Random
     */
    private Random $random;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param WriterInterface $configWriter
     * @param Random $random
     * @param Config $config
     */
    public function __construct(
        WriterInterface $configWriter,
        Random $random,
        Config $config,
    ) {
        $this->configWriter = $configWriter;
        $this->random = $random;
        $this->config = $config;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getShopId(): string
    {
        $shopId = $this->config->getShopId();

        if (!$shopId) {
            $shopId = $this->random->getUniqueHash();
            $this->configWriter->save(Config::XML_PATH_SHOP_ID, $shopId);
        }

        return $shopId;
    }
}
