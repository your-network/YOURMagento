<?php

declare(strict_types=1);

namespace Your\Integration\Block\Widget\Product;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Qa extends Template implements BlockInterface
{
    protected $_template = 'Your_Integration::widget/product-qa.phtml';
}