<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern;

/**
 * Class NewAction
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern
 */
class NewAction extends GiftCodePattern
{
    /**
     * Create new gift code pattern
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
