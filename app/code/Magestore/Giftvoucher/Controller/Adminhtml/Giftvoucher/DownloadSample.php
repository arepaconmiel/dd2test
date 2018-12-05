<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;
use Magento\Framework\Component\ComponentRegistrar;

/**
 * Class DownloadSample
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
 */
class DownloadSample extends Giftvoucher
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    
    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        ComponentRegistrar $componentRegistrar
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultForwardFactory,
            $coreRegistry,
            $modelFactory,
            $collectionFactory
        );
        $this->fileFactory = $fileFactory;
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $filename = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            'Magestore_Giftvoucher'
        );
        $filename .= '/fixtures/import_giftcode_sample.csv';
        
        return $this->fileFactory->create(
            'import_giftcode_sample.csv',
            file_get_contents($filename),
            DirectoryList::VAR_DIR
        );
    }
}
