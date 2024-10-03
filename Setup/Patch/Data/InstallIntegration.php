<?php

declare(strict_types=1);

namespace Your\Integration\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Integration\Model\ConfigBasedIntegrationManager;

class InstallIntegration implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var ConfigBasedIntegrationManager
     */
    private ConfigBasedIntegrationManager $integrationManager;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ConfigBasedIntegrationManager $integrationManager
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ConfigBasedIntegrationManager $integrationManager
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->integrationManager = $integrationManager;
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->integrationManager->processIntegrationConfig(['YOUR Product Content Integration']);
        $this->moduleDataSetup->endSetup();
    }
}
