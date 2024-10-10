<?php

declare(strict_types=1);

namespace Your\Integration\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Model\YourApi;

class UpdateClientScript implements ObserverInterface
{
    /**
     * @var WriterInterface
     */
    private WriterInterface $configWriter;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param WriterInterface $configWriter
     * @param Config $config
     * @param YourApi $yourApi
     */
    public function __construct(
        WriterInterface $configWriter,
        Config $config,
        YourApi $yourApi
    ) {
        $this->configWriter = $configWriter;
        $this->config = $config;
        $this->yourApi = $yourApi;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $changedPaths = (array)$observer->getEvent()->getChangedPaths();
        $configHasChanged = (bool)array_intersect(
            [
                Config::XML_PATH_API_KEY,
                Config::XML_PATH_CONTENT_LANGUAGE,
            ],
            $changedPaths
        );

        $needsScript = $this->config->getApiKey() && !$this->config->getClientScript();
        if (!$configHasChanged && !$needsScript) {
            return;
        }

        $clientScript = $this->yourApi->apiGetEmbedSnippet([
            'locale' => $this->config->getContentLanguage()
        ])->getResponse();

        if ($clientScript) {
            $this->configWriter->save(Config::XML_PATH_CLIENT_SCRIPT, $clientScript);
        }
    }
}
