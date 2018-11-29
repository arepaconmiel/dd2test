<?php

namespace Qxd\S3\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Filesystem\DirectoryList;


class Autosyncimages implements ObserverInterface
{

    public function __construct(
        \MagicToolbox\MagicZoomPlus\Helper\Data $magicToolboxHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Qxd\S3\Helper\Data $helper,
        \Magento\Framework\Filesystem $filesystem,
        \Qxd\Memcached\Helper\Data $memcached,
        \Magento\Catalog\Helper\Image $imageHelper
    ) {
        $this->magicToolboxHelper = $magicToolboxHelper;
        $this->toolObj = $magicToolboxHelper->getToolObj();
        $this->_storeManager=$storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_filesystem = $filesystem;
        $this->_memcached = $memcached;
        $this->_imageHelper = $imageHelper;
    }

    /*
    * Observer to upload new images to s3 for the category
    */

    public function execute(Observer $observer)
    {   
        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/tests3product.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);*/ 
        $product = $observer->getProduct();
        $productId = $product->getId();
        
        $imagesToParse=array();
        $client = $this->_helper->awsConnection();
        $mediaPATH = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();

        $baseMediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $baseStaticUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);

        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        $product_media = $product->getMediaGalleryImages();
        if(is_array($product_media)){
            foreach ($product_media  as $image)
            {   

                $mediaType = $image->getMediaType();
                if ($mediaType != 'image' && $mediaType != 'external-video') {
                     continue;
                }

                $img = $this->_imageHelper->init($product, 'product_page_image_large', ['width' => null, 'height' => null])
                                ->setImageFile($image->getFile())
                                ->getUrl();
                $iPath = $image->getPath();

                if (!is_file($iPath)) {
                    if (strpos($img, $baseMediaUrl) === 0) {
                        $iPath = str_replace($baseMediaUrl, '', $img);
                        $iPath = $this->magicToolboxHelper->getMediaDirectory()->getAbsolutePath($iPath);
                    } else {
                        $iPath = str_replace($baseStaticUrl, '', $img);
                        $iPath = $this->magicToolboxHelper->getStaticDirectory()->getAbsolutePath($iPath);
                    }
                }
                if(file_exists($image->getPath())){
                    $imagesToParse[]= $image->getPath();
                }

                try {
                    $originalSizeArray = getimagesize($iPath);
                } catch (\Exception $exception) {
                    $originalSizeArray = [0, 0];
                }

                if ($mediaType == 'image') {
                    if ($this->toolObj->params->checkValue('square-images', 'Yes')) {
                        $bigImageSize = ($originalSizeArray[0] > $originalSizeArray[1]) ? $originalSizeArray[0] : $originalSizeArray[1];
                        $img = $this->_imageHelper->init($product, 'product_page_image_large')
                                        ->setImageFile($image->getFile())
                                        ->resize($bigImageSize)
                                        ->getUrl();
                    }

                    $first_img = str_replace($baseUrl. 'pub/media/', $mediaPATH, $img);
                    if(file_exists($first_img)){
                        $imagesToParse[]= $first_img;
                    }
                    list($w, $h) = $this->magicToolboxHelper->magicToolboxGetSizes('thumb', $originalSizeArray);
                    $medium = $this->_imageHelper->init($product, 'product_page_image_medium', ['width' => $w, 'height' => $h])
                                    ->setImageFile($image->getFile())
                                    ->getUrl();

                    $second_img = str_replace($baseUrl. 'pub/media/', $mediaPATH, $medium);
                    if(file_exists($first_img)){
                        $imagesToParse[]= $second_img;
                    }
                }

                list($w, $h) = $this->magicToolboxHelper->magicToolboxGetSizes('selector', $originalSizeArray);
                $thumb = $this->_imageHelper->init($product, 'product_page_image_small', ['width' => $w, 'height' => $h])
                                ->setImageFile($image->getFile())
                                ->getUrl();

                $third_img = str_replace($baseUrl. 'pub/media/', $mediaPATH, $thumb);
                if(file_exists($first_img)){
                    $imagesToParse[]= $third_img;
                }
                
            }
        }

        try{

            $small_image = $product->getData('small_image');
            if( $small_image != 'no_selection' && $small_image != '' && $small_image != null){

                $small_image_path = $mediaPATH . 'catalog/product' . $small_image;
                

                if(file_exists($small_image_path)){
                    $resizedSmallImage = $this->_imageHelper->init($product, 'small_image')
                                        ->setImageFile($small_image)
                                        ->resize(250);
                    //$logger->info(print_r($resizedSmallImage->getUrl(),true));
                    $imagesToParse[]= str_replace($baseUrl. 'pub/media/', $mediaPATH, $resizedSmallImage->getUrl());

                    $resizedSmallImage = $this->_imageHelper->init($product, 'small_image')
                                        ->setImageFile($small_image)
                                        ->resize(62, 65);
                    //$logger->info(print_r($resizedSmallImage->getUrl(),true));
                    $imagesToParse[]= str_replace($baseUrl. 'pub/media/', $mediaPATH, $resizedSmallImage->getUrl());
                }
            }   

            $thumbnail_image = $product->getThumbnail();
            if( $thumbnail_image != 'no_selection' && $thumbnail_image != '' && $thumbnail_image != null){

                $thumbnail_image_path = $mediaPATH . 'catalog/product' . $thumbnail_image;
                
                if(file_exists($thumbnail_image_path)){

                    $resizedThumbnailImage = $this->_imageHelper->init($product, 'thumbnail')
                                        ->setImageFile($thumbnail_image)
                                        ->resize(75);
                    //$logger->info(print_r($resizedThumbnailImage->getUrl(),true));
                    $imagesToParse[]= str_replace($baseUrl. 'pub/media/', $mediaPATH, $resizedThumbnailImage->getUrl());
                }
            }  
            
        }catch (Exception $e){ 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_S3_Error.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }


        //$logger->info(print_r($imagesToParse, true));
        if(!empty($imagesToParse)){ 
            $this->sendFiles($client, $imagesToParse); 
        }

        $_memcached = $this->_memcached->initMemcached();
        if($_memcached){ $_memcached->delete($product->getId().'_media'); }
    }

    public function sendFiles($client,$imagesToParse)
    {
        try{
            foreach ($imagesToParse as $file) { $this->_helper->sendFile($client,$file,"max-age=29030400"); }
        }catch (Exception $e){ 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_S3_Error.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}

