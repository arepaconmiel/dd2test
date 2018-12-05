<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftProduct;

/**
 * Class Index
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftProduct
 */
class Index extends \Magestore\Giftvoucher\Controller\Adminhtml\GiftProduct
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Catalog::catalog_products');
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Card Product Manager'));
        return $resultPage;
    }
}
