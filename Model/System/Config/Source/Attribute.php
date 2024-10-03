<?php

declare(strict_types=1);

namespace Your\Integration\Model\System\Config\Source;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\OptionSourceInterface;

class Attribute implements OptionSourceInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private ProductAttributeRepositoryInterface $productAttributeRepository;

    /**
     * @var array|null
     */
    private ?array $options = null;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ProductAttributeRepositoryInterface $productAttributeRepository,
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $sortOrder = $this->sortOrderBuilder->setField('frontend_label')
                ->setDirection(SortOrder::SORT_ASC)
                ->create();

            $searchCriteria = $this->searchCriteriaBuilder->setSortOrders([$sortOrder])
                ->create();

            $attributes = $this->productAttributeRepository->getList($searchCriteria)
                ->getItems();

            $this->options = [[
                'value' => null,
                'label' => __('-- Please Select --'),
            ]];

            foreach ($attributes as $attribute) {
                $this->options[] = [
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getDefaultFrontendLabel(),
                ];
            }
        }

        return $this->options;
    }
}
