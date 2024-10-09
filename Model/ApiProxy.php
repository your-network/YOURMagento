<?php

declare(strict_types=1);

namespace Your\Integration\Model;

use Magento\Framework\Webapi\Rest\Request;
use Your\Integration\Api\ApiProxyInterface;

class ApiProxy implements ApiProxyInterface
{
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @param Request $request
     * @param YourApi $yourApi
     */
    public function __construct(
        Request $request,
        YourApi $yourApi
    ) {
        $this->request = $request;
        $this->yourApi = $yourApi;
    }

    /**
     * @inheritdoc
     */
    public function getEmbedSnippet(): mixed
    {
        return $this->yourApi->apiGetEmbedSnippet(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductTitle(): mixed
    {
        return $this->yourApi->apiGetProductTitle(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductDescription(): mixed
    {
        return $this->yourApi->apiGetProductDescription(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductProsCons(): mixed
    {
        return $this->yourApi->apiGetProductProsCons(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductImages(): mixed
    {
        return $this->yourApi->apiGetProductImages(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductMedia(): mixed
    {
        return $this->yourApi->apiGetProductMedia(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductBullets(): mixed
    {
        return $this->yourApi->apiGetProductBullets(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductReviews(): mixed
    {
        return $this->yourApi->apiGetProductReviews(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductReasonsToBuy(): mixed
    {
        return $this->yourApi->apiGetProductReasonsToBuy(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductSpecifications(): mixed
    {
        return $this->yourApi->apiGetProductSpecifications(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductQnAQuestions(): mixed
    {
        return $this->yourApi->apiGetProductQnAQuestions(
            $this->request->getParams()
        );
    }

    /**
     * @inheritdoc
     */
    public function getQnAQuestionAnswers(): mixed
    {
        return $this->yourApi->apiGetQnAQuestionAnswers(
            $this->request->getParams()
        );
    }
}
