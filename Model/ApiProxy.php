<?php

declare(strict_types=1);

namespace Your\Integration\Model;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Webapi\Rest\Response;
use Your\Integration\Api\ApiProxyInterface;
use Your\Integration\Service\ClientScriptManager;
use Your\Integration\Service\IntegrationManager;

class ApiProxy implements ApiProxyInterface
{
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var Response
     */
    private Response $response;

    /**
     * @var YourApi
     */
    private YourApi $yourApi;

    /**
     * @var ClientScriptManager
     */
    private ClientScriptManager $clientScriptManager;

    /**
     * @var IntegrationManager
     */
    private IntegrationManager $integrationManager;

    /**
     * @param Request $request
     * @param Response $response
     * @param YourApi $yourApi
     * @param ClientScriptManager $clientScriptManager
     * @param IntegrationManager $integrationManager
     */
    public function __construct(
        Request $request,
        Response $response,
        YourApi $yourApi,
        ClientScriptManager $clientScriptManager,
        IntegrationManager $integrationManager
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->yourApi = $yourApi;
        $this->clientScriptManager = $clientScriptManager;
        $this->integrationManager = $integrationManager;
    }

    /**
     * @inheritdoc
     */
    public function getEmbedSnippet(string $locale): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetEmbedSnippet([
                'locale' => $locale
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductTitle(string $matchId, string $lang): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetProductTitle([
                'matchId' => $matchId,
                'lang' => $lang,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductDescription(string $matchId, string $lang): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetProductDescription([
                'matchId' => $matchId,
                'lang' => $lang,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductImages(string $matchId, string $lang): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetProductImages([
                'matchId' => $matchId,
                'lang' => $lang,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductMedia(string $matchId, string $lang): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetProductMedia([
                'matchId' => $matchId,
                'lang' => $lang,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductProsCons(string $matchId, string $lang): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetProductProsCons([
                'matchId' => $matchId,
                'lang' => $lang,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductBullets(string $matchId, string $lang, ?string $secretKey = null): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetProductBullets([
                'matchId' => $matchId,
                'lang' => $lang,
                'secretKey' => $secretKey,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductReasonsToBuy(string $matchId, string $lang): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetProductReasonsToBuy([
                'matchId' => $matchId,
                'lang' => $lang,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getProductSpecifications(string $matchId, string $lang): mixed
    {
        return $this->handleResponse(
            $this->yourApi->apiGetProductSpecifications([
                'matchId' => $matchId,
                'lang' => $lang,
            ])
        );
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
        return $this->handleResponse(
            $this->yourApi->apiGetProductReviews([
                'matchId' => $matchId,
                'lang' => $lang,
                'page' => $page,
                'resultsPerPage' => $resultsPerPage,
            ])
        );
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
        return $this->handleResponse(
            $this->yourApi->apiGetProductQnAQuestions([
                'matchId' => $matchId,
                'lang' => $lang,
                'page' => $page,
                'resultsPerPage' => $resultsPerPage,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function getQnAQuestionAnswers(int $questionId, ?bool $answeredByUserOnly = null): mixed
    {
        if ($answeredByUserOnly !== null) {
            $answeredByUserOnly = $answeredByUserOnly ? 'true' : 'false';
        }

        return $this->handleResponse(
            $this->yourApi->apiGetQnAQuestionAnswers([
                'questionId' => $questionId,
                'answeredByUserOnly' => $answeredByUserOnly
            ])
        );
    }

    /**
     * @return bool
     */
    public function clientScriptWebhook(): bool
    {
        $credentials = $this->integrationManager->getIntegrationCredentials();
        $accessTokenSecret = (string)$this->request->getHeader('Authorization');
        $accessTokenSecret = trim(str_replace('Bearer', '', $accessTokenSecret));
        $hasCredentials = $accessTokenSecret && $credentials;

        if ($hasCredentials && $accessTokenSecret === $credentials['access_token_secret']) {
            return $this->clientScriptManager->update();
        }

        $this->response->setHttpResponseCode(403);
        return false;
    }

    /**
     * @param ApiResponse $response
     * @return string
     */
    private function handleResponse(ApiResponse $response): string
    {
        $this->response->setHttpResponseCode($response->getHttpStatus());

        return $response->getResponse();
    }
}
