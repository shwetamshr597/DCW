<?php
/**
 * Copyright © Bazaarvoice, Inc. All rights reserved.
 * See LICENSE.md for license details.
 */

declare(strict_types=1);


namespace Dcw\Bazaarvoice\Logger;

use Bazaarvoice\Connector\Api\ConfigProviderInterface;
use Exception;
use Magento\Framework\App\State;
use Monolog\DateTimeImmutable;

/**
 * Class Logger
 *
 * @package Bazaarvoice\Connector\Logger
 */
class Logger extends \Bazaarvoice\Connector\Logger\Logger
{
  
    /**
     * @param string|array $message
     * @param array        $context
     *
     * @return bool 
     */
    public function debug($message, array $context = []): void
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /* Create a new product object */
        $configProvider = $objectManager->create("Bazaarvoice\Connector\Api\ConfigProviderInterface");
        if ($configProvider->isDebugEnabled()) {
            if(is_string($message)){
                $this->addRecord(static::DEBUG, $message, $context);
            }            
        }
    }

    /**
     * @param int    $level
     * @param string $message   
     * @param array  $context
     * @return bool
     */
    public function addRecord(int $level, string $message, array $context = [], DateTimeImmutable $datetime = null): bool
    {
        if (is_array($message)) {
            $message = print_r($message, $return = true);
        }

        if (php_sapi_name() == "cli" || $this->admin) {
            print_r($message."\n");
        }

        return parent::addRecord($level, $message, $context);
    }
}