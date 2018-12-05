<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml;

/**
 * Catalog product controller
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class GiftCodePattern extends AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_Giftvoucher::generategiftcard';
    
    /**
     * @var \Magestore\Giftvoucher\Model\GiftCodePatternFactory
     */
    protected $modelFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftCodePatternRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Api\GiftCodePatternRepositoryInterface $repository
     * @param \Magestore\Giftvoucher\Model\GiftCodePatternFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Api\GiftCodePatternRepositoryInterface $repository,
        \Magestore\Giftvoucher\Model\GiftCodePatternFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry);
        $this->repository = $repository;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
    }
}
