<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Service\Logger;

/**
 * Interface LoggerInterface
 * @package Magestore\Giftvoucher\Service\Logger
 */
interface LoggerInterface
{
    /**
     *
     * @param string $message
     * @param string $section
     * @return \Magestore\Giftvoucher\Service\Logger\LoggerInterface
     */
    public function log($message, $section = null);
}
