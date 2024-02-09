<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dcw\ProductAlerts\Block\Email;

/**
 * ProductAlert email back in stock grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Stock extends \Magento\ProductAlert\Block\Email\Stock
{
    /**
     * @var string
     */
    protected $_template = 'Dcw_ProductAlerts::email/stock.phtml';    
}