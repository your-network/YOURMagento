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
    public function __construct(
        YourApi $yourApi
    ) {
        $this->yourApi = $yourApi;
    }

    /**
     * @inheritdoc
     */
    public function getEmbedSnippet(string $locale): mixed
    {
        return $this->yourApi->apiGetEmbedSnippet([
            'locale' => $locale
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductTitle(string $matchId, string $lang): mixed
    {
        return $this->yourApi->apiGetProductTitle([
            'matchId' => $matchId,
            'lang' => $lang,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductDescription(string $matchId, string $lang): mixed
    {
        return $this->yourApi->apiGetProductDescription([
            'matchId' => $matchId,
            'lang' => $lang,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductImages(string $matchId, string $lang): mixed
    {
        return $this->yourApi->apiGetProductImages([
            'matchId' => $matchId,
            'lang' => $lang,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductMedia(string $matchId, string $lang): mixed
    {
        return $this->yourApi->apiGetProductMedia([
            'matchId' => $matchId,
            'lang' => $lang,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductProsCons(string $matchId, string $lang): mixed
    {
        return $this->yourApi->apiGetProductProsCons([
            'matchId' => $matchId,
            'lang' => $lang,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductBullets(string $matchId, string $lang, ?string $secretKey = null): mixed
    {
        return $this->yourApi->apiGetProductBullets([
            'matchId' => $matchId,
            'lang' => $lang,
            'secretKey' => $secretKey,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductReasonsToBuy(string $matchId, string $lang): mixed
    {
        return $this->yourApi->apiGetProductReasonsToBuy([
            'matchId' => $matchId,
            'lang' => $lang,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductSpecifications(string $matchId, string $lang): mixed
    {
        return $this->yourApi->apiGetProductSpecifications([
            'matchId' => $matchId,
            'lang' => $lang,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductReviews(
        string $matchId,
        string $lang,
        ?int $page = null,
        ?int $resultsPerPage = null
    ): mixed {
        return $this->yourApi->apiGetProductReviews([
            'matchId' => $matchId,
            'lang' => $lang,
            'page' => $page,
            'resultsPerPage' => $resultsPerPage,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProductQnAQuestions(
        string $matchId,
        string $lang,
        ?int $page = null,
        ?int $resultsPerPage = null
    ): mixed {
        return $this->yourApi->apiGetProductQnAQuestions([
            'matchId' => $matchId,
            'lang' => $lang,
            'page' => $page,
            'resultsPerPage' => $resultsPerPage,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getQnAQuestionAnswers(int $questionId, ?bool $answeredByUserOnly = null): mixed
    {
        if ($answeredByUserOnly !== null) {
            $answeredByUserOnly = $answeredByUserOnly ? 'true' : 'false';
        }

        return $this->yourApi->apiGetQnAQuestionAnswers([
            'questionId' => $questionId,
            'answeredByUserOnly' => $answeredByUserOnly
        ]);
    }
}
