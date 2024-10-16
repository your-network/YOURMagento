<?php

declare(strict_types=1);

namespace Your\Integration\Model;

class ApiResponse
{
    /**
     * @var string
     */
    private string $response;

    /**
     * @var int
     */
    private int $httpStatus;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @param string $response
     * @param int $httpStatus
     * @param array $headers
     */
    public function __construct(
        string $response,
        int $httpStatus = 200,
        array $headers = [],
    ) {
        $this->response = $response;
        $this->httpStatus = $httpStatus;
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
