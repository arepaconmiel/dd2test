<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Service\Logger;

/**
 * Class Logger
 * @package Magestore\Giftvoucher\Service\Logger
 */
class Logger implements LoggerInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    const MESSAGE_PREFIX = 'Magestore_Giftvoucher';
    
    const LOG_LEVEL = 'debug';

    /**
     * Logger constructor.
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * @param string $message
     * @param string $section
     * @return \Magestore\Giftvoucher\Service\Logger\LoggerInterface
     */
    public function log($message, $section = null)
    {
        $section = $section ? '.'.$section : '';
        $message = self::MESSAGE_PREFIX . $section .': '. $message;
        $this->logger->log(self::LOG_LEVEL, $message);
        return $this;
    }
}
