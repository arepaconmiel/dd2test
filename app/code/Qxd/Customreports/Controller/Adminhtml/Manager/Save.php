<?php

namespace Qxd\Customreports\Controller\Adminhtml\Manager;

class Save extends \Magento\Backend\App\Action
{   

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {   
        $resultPage = $this->resultPageFactory->create();
        //$resultPage->setActiveMenu('Qxd_Subscription::subscription');
        $resultPage->getConfig()->getTitle()->prepend((__('Custom Reports')));

        return $resultPage;
    }
}
