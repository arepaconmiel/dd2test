<?php

namespace Qxd\Stateshipping\Controller\Adminhtml\Stateshipping;
use Magento\Framework\App\Filesystem\DirectoryList;

class DownloadFile extends \Magento\Backend\App\Action
{   

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_directoryList = $directoryList;
        $this->_fileFactory =  $fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping_labels.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);     


        try{

            $name = $this->getRequest()->getParam('filename');
            $file = $this->_directoryList->getRoot().'/var/QXD_import/'.$name;

            $logger->info(print_r($file, true));

            //$content = file_get_contents($file);
            //$this->_prepareDownloadResponse($name, $content);
            return $this->_fileFactory->create(
                $name,
                @file_get_contents($file)
            );

        }catch (Exception $e)
        {
            Mage::log($e->getMessage().' '.$e->getLine(),null,'QXD-Error.log');
        }
    }

    protected function _isAllowed()
    {
        return true;
    }
}
