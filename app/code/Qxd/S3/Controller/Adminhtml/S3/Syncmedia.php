<?php

namespace Qxd\S3\Controller\Adminhtml\S3;

class Syncmedia extends \Magento\Backend\App\Action
{   

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Qxd\Memcached\Helper\Data $helper,
        \Qxd\S3\Model\Awsprocessor $awsprocessor,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_awsprocessor = $awsprocessor;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_S3-Error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);     
        try{
            $test = $this->_awsprocessor->updateS3('media');
            $result = $this->_resultJsonFactory->create();
            $result->setData(['status' => '200', 'data' => "Files synchronized"]);
            return $result; 

        } catch (Exception $e)
        {   
            $logger->info(print_r($e->getMessage(), true));
            $result->setData(['status' => '500', 'data' => "Error, Please check that AWS Creds are correct and try again"]);
            return $result; 
        }
    }
}
