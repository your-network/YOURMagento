<?php

declare(strict_types=1);

namespace Your\Integration\Plugin\Framework\WebApi;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Response\Renderer\Json as Subject;

/**
 * Plugin is used to forward the result from YOUR API directly without encoding it
 */
class JsonRenderer
{
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @param Request $request
     * @param Json $json
     */
    public function __construct(
        Request $request,
        Json $json
    ) {
        $this->request = $request;
        $this->json = $json;
    }

    /**
     * @param Subject $subject
     * @param callable $proceed
     * @param mixed $data
     * @return mixed
     */
    public function aroundRender(Subject $subject, callable $proceed, mixed $data): mixed
    {
        if (strpos($this->request->getPathInfo(), '/V1/your-integration/') === false) {
            return $proceed($data);
        }

        if (!$this->isJson($data)) {
            return $proceed($data);
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    private function isJson(mixed $data)
    {
        if (!is_string($data)) {
            return false;
        }

        try {
            $this->json->unserialize($data);
        } catch (\Exception) {
            return false;
        }

        return true;
    }
}
