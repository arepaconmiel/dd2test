<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\Overwrite\Controller\Cart;

class DeleteMultiple extends \Magento\Checkout\Controller\Cart
{
	/**
     * Delete shopping cart item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {	
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

       $ids = $this->getRequest()->getParam('ids');
        $idsParsed=json_decode($ids);
        if ($idsParsed) {
            try {
            	foreach($idsParsed as $item){  $this->cart->removeItem($item)->save(); }
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t remove the item.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }
        $defaultUrl = $this->_objectManager->create(\Magento\Framework\UrlInterface::class)->getUrl('*/*');
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($defaultUrl));
    }
}