<?php

declare(strict_types=1);

namespace Your\Integration\Block\Widget\Product;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ProsCons extends Template implements BlockInterface
{
    protected $_template = 'Your_Integration::widget/product-pros-cons.phtml';
}