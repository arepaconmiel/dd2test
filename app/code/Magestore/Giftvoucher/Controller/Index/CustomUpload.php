<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller\Index;

use Magento\Customer\Model\Session;

/**
 * Giftvoucher Index CustomUpload Action
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class CustomUpload extends \Magestore\Giftvoucher\Controller\Action
{

    /**
     *
     */
    public function execute()
    {
        try {
            $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
            if ($customerSession->getGiftcardCustomUploadImage()) {
                $this->getHelperData()->deleteImageFile($customerSession->getGiftcardCustomUploadImage());
            }
            $uploader = $this->_objectManager->create('Magento\Framework\File\Uploader', array('fileId' => 'image'));
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $this->getHelperData()->createImageFolderHaitv('', '', true);
            $result = $uploader->save(
                $this->getFileSystem()->getDirectoryRead('media')->getAbsolutePath('tmp/giftvoucher/images')
            );
            $result['url'] = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'tmp/giftvoucher/images/' . $result['file'];
            $customerSession->setGiftcardCustomUploadImage($result['url']);
            $customerSession->setGiftcardCustomUploadImageName($result['file']);
            $this->getHelperData()->resizeImage($result['url']);
        } catch (\Exception $e) {
            $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
        }
        $this->getResponse()->setBody(
            $this->_objectManager->create('\Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}
