<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magestore\Giftvoucher\Model\Giftvoucher\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

/**
 * Class Render
 */
class GridToCsv extends Action
{
    /**
     * @var ConvertToCsv
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;
    
    /**
     * @param Context $context
     * @param ConvertToCsv $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        ConvertToCsv $converter,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute()
    {
        return $this->fileFactory->create('giftcode.csv', $this->converter->getCsvFile(), 'var');
    }
}
