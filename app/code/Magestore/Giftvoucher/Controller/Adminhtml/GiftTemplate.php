<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller\Adminhtml;

/**
 * Class GiftTemplate
 * @package Magestore\Giftvoucher\Controller\Adminhtml
 */
abstract class GiftTemplate extends AbstractAction
{
    /**
     * @var Magestore\Giftvoucher\Model\GiftTemplateFactory
     */
    protected $giftTemplateFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface
     */
    protected $giftTemplateRepository;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory
     * @param \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory,
        \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository
    ) {
        $this->giftTemplateFactory = $giftTemplateFactory;
        $this->giftTemplateRepository = $giftTemplateRepository;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry);
    }
    
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Giftvoucher::gifttemplate');
    }
    
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Giftvoucher::gifttemplate');
        $resultPage->addBreadcrumb(__('Gift Card'), __('Gift Card'));
        $resultPage->addBreadcrumb(__('Template'), __('Template'));
        return $resultPage;
    }
}
