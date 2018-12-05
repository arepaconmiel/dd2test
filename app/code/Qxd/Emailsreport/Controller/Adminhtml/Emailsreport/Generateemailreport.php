<?php

namespace Qxd\Emailsreport\Controller\Adminhtml\Emailsreport;

class Generateemailreport extends \Magento\Backend\App\Action
{   

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Qxd\Emailsreport\Model\Reports $report,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_report = $report;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Emailsreport-Error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer); 

        $result = $this->_resultJsonFactory->create();    
        try{
            $test = $this->_report->generateReports();
            $result->setData(['status' => '200', 'data' => "Report emails sent"]);

        } catch (Exception $e)
        {   
            $logger->info(print_r($e->getMessage(), true));
            $result->setData(['status' => '500', 'data' => "Error, Please try again"]);   
        }

        return $result; 
    }
}
