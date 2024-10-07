<?php

declare(strict_types=1);

namespace Your\Integration\Model;

use Your\Integration\Api\ApiProxyInterface;

class ApiProxy implements ApiProxyInterface
{
    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param YourApi $yourApi
     */
    public function __construct(YourApi $yourApi)
    {
        $this->yourApi = $yourApi;
    }

    /**
     * @inheritdoc
     */
    public function getProductTitle(): mixed
    {
        return $this->yourApi->apiGetProductTitle();
    }

    /**
     * @inheritdoc
     */
    public function getProductDescription(): mixed
    {
        return $this->yourApi->apiGetProductDescription();
    }

    /**
     * @inheritdoc
     */
    public function getProductProsCons(): mixed
    {
        return $this->yourApi->apiGetProductProsCons();
    }

    /**
     * @inheritdoc
     */
    public function getProductImages(): mixed
    {
        return $this->yourApi->apiGetProductImages();
    }

    /**
     * @inheritdoc
     */
    public function getProductMedia(): mixed
    {
        return $this->yourApi->apiGetProductMedia();
    }

    /**
     * @inheritdoc
     */
    public function getProductBullets(): mixed
    {
        return $this->yourApi->apiGetProductBullets();
    }

    /**
     * @inheritdoc
     */
    public function getProductReviews(): mixed
    {
        return $this->yourApi->apiGetProductReviews();
    }

    /**
     * @inheritdoc
     */
    public function getProductReasonsToBuy(): mixed
    {
        return $this->yourApi->apiGetProductReasonsToBuy();
    }

    /**
     * @inheritdoc
     */
    public function getProductSpecifications(): mixed
    {
        return $this->yourApi->apiGetProductSpecifications();
    }

    /**
     * @inheritdoc
     */
    public function getProductQnaQuestions(): mixed
    {
        return $this->yourApi->apiGetProductQnaQuestions();
    }
}
