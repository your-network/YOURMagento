<?php

declare(strict_types=1);

namespace Your\Integration\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Your\Integration\Model\System\Config;
use Your\Integration\Model\YourApi;

class ClientScripts implements ArgumentInterface
{
    /**
     * @var RequestInterface $request
     */
    private RequestInterface $request;

    /**
     * @var ProductRepositoryInterface $productRepository
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     * @param Json $json
     * @param Config $config
     * @param YourApi $yourApi
     */
    public function __construct(
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        Json $json,
        Config $config,
        YourApi $yourApi
    ) {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->json = $json;
        $this->config = $config;
        $this->yourApi = $yourApi;
    }

    /**
     * @return null|ProductInterface
     */
    public function getProduct(): ?ProductInterface
    {
        try {
            return $this->productRepository->getById(
                $this->request->getParam('id')
            );
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function getCanShow(): bool
    {
        return $this->config->getIsConfigured()
            && $this->getMatchIds()
            && $this->getProduct();
    }

    /**
     * @return array
     */
    public function getMatchIds(): array
    {
        $matchIds = [];
        $product = $this->getProduct();

        if ($attributeCode = $this->config->getMpnAttributeCode()) {
            $matchIds[] = $product->getData($attributeCode);
        }

        if ($attributeCode = $this->config->getGtinAttributeCode()) {
            $matchIds[] = $product->getData($attributeCode);
        }

        return $matchIds;
    }

    /**
     * @return string
     */
    public function getClientScript(): string
    {
        return $this->config->getClientScript();
    }

    /**
     * @return string
     */
    public function getConfig(): string
    {
        $config = [
            'matchId' => implode(',', $this->getMatchIds()),
            'locale' => $this->config->getContentLanguage(),
            'subpath' => $this->yourApi->getMagentoApiUrl(),
        ];

        return $this->json->serialize($config);
    }
}
