<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Helper;

/**
 * Giftvoucher draw helper
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Drawgiftcard extends \Magestore\Giftvoucher\Helper\Data
{
    /**
     * Get the directory of gift code image
     *
     * @param string $code
     * @return string
     */
    public function getImagesInFolder($code)
    {
        $directory = $this->getBaseDirMedia()->getAbsolutePath('giftvoucher/draw/' . $code . '/');
        return glob($directory . $code . "*.png");
    }
}
