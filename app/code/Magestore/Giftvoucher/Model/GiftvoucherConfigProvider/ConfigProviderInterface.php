<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\GiftvoucherConfigProvider;

/**
 * Interface ConfigProviderInterface
 * @package Magestore\Giftvoucher\Model\GiftvoucherConfigProvider
 */
interface ConfigProviderInterface
{
    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig();
}
