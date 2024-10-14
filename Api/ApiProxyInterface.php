<?php

declare(strict_types=1);

namespace Your\Integration\Api;

interface ApiProxyInterface
{
    /**
     * @param string $locale
     * @return mixed
     */
    public function getEmbedSnippet(string $locale): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @return mixed
     */
    public function getProductTitle(string $matchId, string $lang): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @return mixed
     */
    public function getProductDescription(string $matchId, string $lang): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @return mixed
     */
    public function getProductImages(string $matchId, string $lang): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @return mixed
     */
    public function getProductMedia(string $matchId, string $lang): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @return mixed
     */
    public function getProductProsCons(string $matchId, string $lang): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @param string|null $secretKey
     * @return mixed
     */
    public function getProductBullets(string $matchId, string $lang, ?string $secretKey = null): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @return mixed
     */
    public function getProductReasonsToBuy(string $matchId, string $lang): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @return mixed
     */
    public function getProductSpecifications(string $matchId, string $lang): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @param int|null $page
     * @param int|null $resultsPerPage
     * @return mixed
     */
    public function getProductReviews(
        string $matchId,
        string $lang,
        ?int $page = null,
        ?int $resultsPerPage = null
    ): mixed;

    /**
     * @param string $matchId
     * @param string $lang
     * @param int|null $page
     * @param int|null $resultsPerPage
     * @return mixed
     */
    public function getProductQnAQuestions(
        string $matchId,
        string $lang,
        ?int $page = null,
        ?int $resultsPerPage = null
    ): mixed;

    /**
     * @param int $questionId
     * @param bool|null $answeredByUserOnly
     * @return mixed
     */
    public function getQnAQuestionAnswers(int $questionId, ?bool $answeredByUserOnly = null): mixed;

    /**
     * @return mixed
     */
    public function clientScriptWebhook(): bool;
}
