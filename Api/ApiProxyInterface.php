<?php

declare(strict_types=1);

namespace Your\Integration\Api;

interface ApiProxyInterface
{
    /**
     * @return mixed
     */
    public function getProductTitle(): mixed;

    /**
     * @return mixed
     */
    public function getProductDescription(): mixed;

    /**
     * @return mixed
     */
    public function getProductProsCons(): mixed;

    /**
     * @return mixed
     */
    public function getProductImages(): mixed;

    /**
     * @return mixed
     */
    public function getProductMedia(): mixed;

    /**
     * @return mixed
     */
    public function getProductBullets(): mixed;

    /**
     * @return mixed
     */
    public function getProductReviews(): mixed;

    /**
     * @return mixed
     */
    public function getProductReasonsToBuy(): mixed;

    /**
     * @return mixed
     */
    public function getProductSpecifications(): mixed;

    /**
     * @return mixed
     */
    public function getProductQnaQuestions(): mixed;
}
