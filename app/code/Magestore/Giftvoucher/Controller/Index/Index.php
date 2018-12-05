<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

/**
 * Index Action
 */
class Index extends \Magestore\Giftvoucher\Controller\Action
{
    /**
     * @return mixed
     */
    public function execute()
    {
        if ($this->getHelperData()->getGeneralConfig('active') == '1') {
            return $this->initFunction('Gift Card');
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('csm/noroute');
        }
    }
}
