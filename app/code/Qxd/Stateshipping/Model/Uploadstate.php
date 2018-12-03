<?php
namespace Qxd\Stateshipping\Model;

class Uploadstate extends \Magento\Config\Model\Config\Backend\File
{	
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        array $data = []
    ) {
        $this->_configWriter = $configWriter;
        parent::__construct($context, $registry, $config, $cacheTypeList, $uploaderFactory, $requestData, $filesystem, $resource, $resourceCollection, $data);
    }

	/**
     * @return string[]
     */
    public function _getAllowedExtensions() {
        return ['csv', 'xls'];
    }
 	
 	public function afterSave()
    {	
    	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD-Error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try{
            if (file_exists($this->_getUploadDir()."/".$this->getValue()))
            {
                $file = fopen($this->_getUploadDir()."/".$this->getValue(),"r");

                $daysData=array();
                while(! feof($file))
                {
                    $row=fgetcsv($file);
                    $logger->info(print_r($row,true));
                    //$row=array_filter($row);
                    if(!empty($row)) { 
                    	$this->_configWriter->save('stateshipping_options/configure_states/stateshipping_'.$row[0],$row[1]); 
                    }
                }
                fclose($file);
            }
        }catch (Exception $e){ 
        	$logger->info($e->getMessage().' '.$e->getLine());
        }

        return parent::afterSave();
    }
}