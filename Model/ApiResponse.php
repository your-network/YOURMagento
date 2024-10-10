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
     * @param string $response
     * @param int $httpStatus
     */
    public function __construct(
        string $response,
        int $httpStatus = 200,
    ) {
        $this->response = $response;
        $this->httpStatus = $httpStatus;
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
}
