<?php

declare(strict_types=1);

namespace Your\Integration\Service;

use Magento\Framework\Exception\IntegrationException;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Api\OauthServiceInterface;
use Magento\Integration\Model\Integration;

class IntegrationManager
{
    const INTEGRATION_NAME = 'YOUR Product Content';
    const INTEGRATION_EMAIL = 'magento-support@your.io';

    /**
     * @var IntegrationServiceInterface
     */
    private IntegrationServiceInterface $integrationService;

    /**
     * @var OauthServiceInterface
     */
    private OauthServiceInterface $oauthService;

    /**
     * @param IntegrationServiceInterface $integrationService
     * @param OauthServiceInterface $oauthService
     */
    public function __construct(
        IntegrationServiceInterface $integrationService,
        OauthServiceInterface $oauthService
    ) {
        $this->integrationService = $integrationService;
        $this->oauthService = $oauthService;
    }

    /**
     * @return array
     */
    public function getIntegrationCredentials(): array
    {
        try {
            $consumerId = $this->getIntegration()->getConsumerId();
            $consumer = $this->oauthService->loadConsumer($consumerId);
            $accessToken = $this->oauthService->getAccessToken($consumerId);

            if (!$accessToken && $this->oauthService->createAccessToken($consumerId, true)) {
                $accessToken = $this->oauthService->getAccessToken($consumerId);
            }

            $credentials = [
                'consumer_key' => $consumer->getKey(),
                'consumer_secret' => $consumer->getSecret(),
                'access_token' => $accessToken->getToken(),
                'access_token_secret' => $accessToken->getSecret(),
            ];
        } catch (\Exception) {
            return [
                'consumer_key' => '',
                'consumer_secret' => '',
                'access_token' => '',
                'access_token_secret' => '',
            ];
        }

        return $credentials;
    }

    /**
     * @return Integration
     * @throws IntegrationException
     */
    private function getIntegration(): Integration
    {
        $integration = $this->integrationService->findByName(
            self::INTEGRATION_NAME
        );

        if ($integration && $integration->getId()) {
            return $integration;
        }

        return $this->integrationService->create([
            'status' => Integration::STATUS_ACTIVE,
            'name' => self::INTEGRATION_NAME,
            'email' => self::INTEGRATION_EMAIL,
            'all_resources' => false,
            'resource' => [
                'Magento_Catalog::products',
            ],
        ]);
    }
}
