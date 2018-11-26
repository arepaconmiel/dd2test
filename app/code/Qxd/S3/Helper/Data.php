<?php
namespace Qxd\S3\Helper;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_resource = $resource;
        $this->_scopeConfig = $scopeConfig;
        $this->_filesystem = $filesystem; 
    }


    public function getAWSBasePath(){ 
        $url = $this->_scopeConfig->getValue(
            's3_options/configure_s3/s3_region',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return "https://s3-".$url.".amazonaws.com/"; 
    }
    public function awsConnection()
    {   
        $region = $this->_scopeConfig->getValue(
            's3_options/configure_s3/s3_region',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $key = $this->_scopeConfig->getValue(
            's3_options/configure_s3/s3_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $secret = $this->_scopeConfig->getValue(
            's3_options/configure_s3/s3_secret',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $result = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => array(
                'key'    => $key,
                'secret' => $secret
            )
        ]);

        return $result;
        //return true;
    }
    public function sendFile($client,$filename, $cache_control = '')
    {   

        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_S3-Error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);*/

        $bucket = $this->_scopeConfig->getValue(
            's3_options/configure_s3/s3_bucket',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $rootPath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)->getAbsolutePath();
        $aux_file_name = str_replace($rootPath,'',$filename);

        $result = $client->putObject([
            'ACL' => 'public-read',
            'Bucket' => $bucket, // REQUIRED
            'SourceFile' => $filename,
            'StorageClass' => 'STANDARD', 
            'Key' =>$aux_file_name,
            'CacheControl' => $cache_control
        ]);

    }
    public function isS3enabled(){ 
        $enable = $this->_scopeConfig->getValue(
            's3_options/configure_s3/s3_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $enable; 
    }
    
}
