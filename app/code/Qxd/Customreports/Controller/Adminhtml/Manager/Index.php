<?php

namespace Qxd\Customreports\Controller\Adminhtml\Manager;

class Index extends \Magento\Backend\App\Action
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
        $resultPage->setActiveMenu('Magento_Reports::report_salesroot');
        $resultPage->getConfig()->getTitle()->prepend(__('YOY Sales'));

        return $resultPage;
    }


    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Qxd_Customreports::custom');
    }
}
