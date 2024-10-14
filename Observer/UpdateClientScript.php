<?php

declare(strict_types=1);

namespace Your\Integration\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Your\Integration\Service\ClientScriptUpdate;
use Your\Integration\Model\System\Config;

class UpdateClientScript implements ObserverInterface
{
    /**
     * @var ClientScriptUpdate
     */
    private ClientScriptUpdate $clientScriptUpdate;

    /**
     * @param ClientScriptUpdate $clientScriptUpdate
     */
    public function __construct(
        ClientScriptUpdate $clientScriptUpdate,
    ) {
        $this->clientScriptUpdate = $clientScriptUpdate;
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

        if ($configHasChanged) {
            $this->clientScriptUpdate->execute(false);
        }
    }
}
