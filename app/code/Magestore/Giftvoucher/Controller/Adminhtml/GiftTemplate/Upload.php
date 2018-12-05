<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;
use Magestore\Giftvoucher\Service\GiftTemplate\MediaService;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Upload
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate
 */
class Upload extends GiftTemplate
{
    /**
     * @var string
     */
    protected $_uploadPath = 'tmp/comingsoon/maintenance/images';


    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $uploader = $this->_objectManager ->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
            $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
            $result = $uploader->save($mediaDirectory->getAbsolutePath(MediaService::UPLOAD_PATH));
            $imgpath = $this->_prepareFile($result['file']);
            $imgpathArray = explode("/", $imgpath);
            unset($imgpathArray[count($imgpathArray) - 1]);
            $imgpath = implode("/", $imgpathArray);
            $url = $this->_objectManager->get('Magento\Framework\UrlInterface')->getBaseUrl() . 'pub/media/' . MediaService::UPLOAD_PATH;
            $result['url'] = $url . '/' . $this->_prepareFile($result['file']);
            $result['file'] = $result['file'];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        $response = $this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory')->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    /**
     *
     * @param string $file
     * @return string
     */
    private function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }
}
