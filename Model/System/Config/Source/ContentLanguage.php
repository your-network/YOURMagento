<?php

declare(strict_types=1);

namespace Your\Integration\Model\System\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ContentLanguage implements OptionSourceInterface
{
    /**
     * @var array|null
     */
    private ?array $options = null;

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [
                [
                    'value' => 'en',
                    'label' => __('English'),
                ],
                [
                    'value' => 'nl',
                    'label' => __('Dutch'),
                ]
            ];
        }

        return $this->options;
    }
}
