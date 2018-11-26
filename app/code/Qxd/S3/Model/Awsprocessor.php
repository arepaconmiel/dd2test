<?php
namespace Qxd\S3\Model;
use Magento\Framework\App\Filesystem\DirectoryList;

class Awsprocessor extends \Magento\Framework\Model\AbstractModel 
{
    private static $instance;
    private static $scopeConfig;

    public function __construct(
        \Qxd\S3\Helper\Data $helper,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_helper = $helper;
        $this->_filesystem = $filesystem;
    }

    public function updateS3($type)
    {
        $client = $this->_helper->awsConnection($type);

        $path="";
        $cache_control = "";
        $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();

        switch($type)
        {
            case "media":{ $path=$mediapath; $cache_control='max-age=29030400';}break;
            case "wysiwyg":{ $path=$mediapath . "wysiwyg"; $cache_control='max-age=29030400';}break;
        }

        if($path){ 
            $this->sendFiles($client,$path,$cache_control); 
        }
    }

    public function sendFiles($client,$path,$cache_control)
    {   
        $di = new \RecursiveDirectoryIterator($path);

        foreach (new \RecursiveIteratorIterator($di) as $filename => $file)
        {
            if($file->getFilename()!="." && $file->getFilename()!="..") { 
                $this->_helper->sendFile($client,$filename,$cache_control); 
            }
        }
    }
}