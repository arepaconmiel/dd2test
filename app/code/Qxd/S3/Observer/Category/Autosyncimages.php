<?php

namespace Qxd\S3\Observer\Category;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Filesystem\DirectoryList;


class Autosyncimages implements ObserverInterface
{

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Qxd\S3\Helper\Data $helper,
        \Magento\Framework\Filesystem $filesystem,
        \Qxd\Memcached\Helper\Data $memcached
    ) {
        $this->_storeManager=$storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_filesystem = $filesystem;
        $this->_memcached = $memcached;
    }

    /*
    * Observer to upload new images to s3 for the category
    */

    public function execute(Observer $observer)
    {   

        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/category_s3.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Enter to category observer: status');*/

        $category = $observer->getCategory();


        $imagesToParse=array();
        $client = $this->_helper->awsConnection();

        $mediaPATH = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();

        $thumbnail = $category->getThumbnail();
        $main_image = $category->getImage();

        if($thumbnail){
            $image = (is_array($thumbnail)) ? $thumbnail['value'] : $thumbnail;
            $img_path = $mediaPATH."/catalog/category/".$image;
            if(file_exists($mediaPATH."/catalog/category/".$image)){
                $imagesToParse[]=$img_path;
            }
        }

        if($main_image){
            $image = (is_array($main_image)) ? $main_image['value'] : $main_image;
            $img_path = $mediaPATH."catalog/category/".$image;
             if(file_exists($mediaPATH."/catalog/category/".$image)){
                $imagesToParse[]=$img_path;
            }

        }
        if(!empty($imagesToParse)){ 
            $this->sendFiles($client,$imagesToParse); 
        }

        $_memcached = $this->_memcached->initMemcached();
        if($_memcached)
        {
            $_memcached->delete('_menu');
            $_memcached->delete('_menuDefault');
        }
    }

    public function sendFiles($client,$imagesToParse)
    {
        try{
            foreach ($imagesToParse as $file) { 
                $this->_helper->sendFile($client,$file,"max-age=29030400"); 
            }
        }catch (Exception $e){ 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_S3_Error.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}

