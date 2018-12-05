<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Service\GiftTemplate;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class MediaService
 * @package Magestore\Giftvoucher\Service\GiftTemplate
 */
class MediaService implements \Magestore\Giftvoucher\Api\GiftTemplate\MediaServiceInterface
{
    /**
     * @var string
     */
    const MEDIA_PATH = 'giftvoucher/template/images';
    
    const UPLOAD_PATH = 'tmp/comingsoon/maintenance/images';
    
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $adapterFactory;
    
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * MediaService constructor.
     * @param \Magento\Framework\Image\AdapterFactory $adapterFactory
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\UrlInterface $url
    ) {
    
        $this->adapterFactory = $adapterFactory;
        $this->fileSystem = $fileSystem;
        $this->url = $url;
    }

    /**
     *
     * @param \Magestore\Giftvoucher\Model\GiftTemplate $giftTemplate
     * @return $this
     */
    public function updateMedia(\Magestore\Giftvoucher\Model\GiftTemplate $giftTemplate)
    {
        $images = $giftTemplate->getData('media_gallery/images');
        $mediaFiles = [];
        if (count($images)) {
            foreach ($images as $image) {
                if (!isset($image['file']) || !$image['file']) {
                    continue;
                }
                $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
                if (isset($image['removed']) && $image['removed']) {
                    if ($mediaDirectory->isExist(self::MEDIA_PATH .'/'. $image['file'])) {
                        $mediaDirectory->delete(self::MEDIA_PATH .'/'. $image['file']);
                    }
                    //$removeFiles[] = $image['file'];
                    continue;
                }
                if ($mediaDirectory->isExist(self::MEDIA_PATH .'/'. $image['file'])) {
                    $mediaFiles[] = $image['file'];
                    continue;
                }
                $fileName = substr($image['file'], 5);
                $fromPath = self::UPLOAD_PATH . $image['file'];
                $toPath = self::MEDIA_PATH .'/'. $fileName;
                if ($mediaDirectory->isExist($fromPath)) {
                    $mediaDirectory->copyFile($fromPath, $toPath);
                    $mediaDirectory->delete($fromPath);
                    $mediaFiles[] = $fileName;
                }
            }
        }
        $giftTemplate->setImages(implode(',', $mediaFiles));
        return $this;
    }
    
    /**
     * Get images json from gift Template
     *
     * @param \Magestore\Giftvoucher\Model\GiftTemplate $model
     * @return string
     */
    public function getImagesJson($model)
    {
        $images = explode(',', $model->getImages());
        $imageData = [];
        if ($model->getImages() && count($images)) {
            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            foreach ($images as $image) {
                $imageUrl = $this->getImageUrl($image);
                try {
                    $fileHandler = $mediaDirectory->stat(self::MEDIA_PATH .'/'. $image);
                    $imageSize = $fileHandler['size'];
                } catch (FileSystemException $e) {
                    //$imageUrl = '';
                    $imageSize= 0;
                }
                $imageData[] = [
                    'url' => $imageUrl,
                    'size' => $imageSize,
                    'file' => $image
                ];
            }
            return \Zend_Json::encode($imageData);
        }
        return '[]';
    }
    
    /**
     *
     * @param string $image
     * @return string
     */
    public function getImageUrl($image)
    {
        return $this->url->getBaseUrl(). 'pub/media/' . self::MEDIA_PATH . '/' . $image;
    }
    
    /**
     * Get first image url from gift Template
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
     * @return string
     */
    public function getFirstImageUrl($giftTemplate)
    {
        $images = explode(',', $giftTemplate->getImages());
        return $this->getImageUrl(reset($images));
    }
}
