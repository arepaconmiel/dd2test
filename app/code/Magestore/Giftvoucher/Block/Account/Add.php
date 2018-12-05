<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Account;

/**
 * Add Gift Code block
 */
class Add extends \Magestore\Giftvoucher\Block\Account
{
    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/addlist');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
