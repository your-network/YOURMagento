<?php

declare(strict_types=1);

namespace Your\Integration\Setup\Patch\Data;

use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Service\IntegrationManager;

class Install implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var IntegrationManager
     */
    private IntegrationManager $integrationManager;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param IntegrationManager $integrationManager
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        IntegrationManager $integrationManager
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->integrationManager = $integrationManager;
    }

    /**
     * @inheritdoc
     */
    public function apply(): self
    {
        return $this;
    }

    /**
     * @return void
     * @throws IntegrationException
     */
    public function revert(): void
    {
        $table = $this->moduleDataSetup->getTable('core_config_data');
        $connection = $this->moduleDataSetup->getConnection();

        $connection->delete($table, ['path IN (?)' => [
            Config::XML_PATH_ENABLED,
            Config::XML_PATH_API_KEY,
            Config::XML_PATH_MPN_ATTRIBUTE_CODE,
            Config::XML_PATH_GTIN_ATTRIBUTE_CODE,
            Config::XML_PATH_CONTENT_LANGUAGE,
        ]]);

        $this->integrationManager->deleteIntegration();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
