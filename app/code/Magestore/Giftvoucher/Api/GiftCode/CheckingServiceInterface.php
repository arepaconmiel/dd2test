<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftCode;

/**
 * Interface CheckingServiceInterface
 * @package Magestore\Giftvoucher\Api\GiftCode
 */
interface CheckingServiceInterface
{
    /**
     * @param string $code
     * @return \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface
     */
    public function check($code);

    /**
     * @param string $code
     * @param bool $formated
     * @return array
     */
    public function getCodeData($code, $formated = false);
}
