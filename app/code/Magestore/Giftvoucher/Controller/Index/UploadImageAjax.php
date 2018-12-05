<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller\Index;

use Magento\Customer\Model\Session;

/**
 * Giftvoucher Index UploadImageAjax Action
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class UploadImageAjax extends \Magestore\Giftvoucher\Controller\Action
{
   
    /**
     * Upload images action
     */
    public function execute()
    {
        $fileRequest = $this->getRequest()->getFiles();
        $result = array();
        if (isset($fileRequest['templateimage'])) {
            $error = $fileRequest["templateimage"]["error"];

            try {
                $uploader = $this->_objectManager->create(
                    'Magento\Framework\File\Uploader',
                    array('fileId' => 'templateimage')
                );
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $this->getHelper()->createImageFolderHaitv('', '', true);
                $fileName = $fileRequest['templateimage']['name'];
                $result = $uploader->save(
                    $this->getFileSystem()->getDirectoryRead('media')->getAbsolutePath('tmp/giftvoucher/images')
                );
                $result['tmp_name'] = $result['tmp_name'];
                $result['path'] = $result['path'];
                $result['url'] = $this->_storeManager->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                    . 'tmp/giftvoucher/images/' . $result['file'];

                $result['filename']= $fileName;
                $result['sucess'] = true;
            } catch (\Exception $e) {
                $result['sucess'] = false;
                $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
            }
        } else {
            $this->messageManager->addError(__('Image Saving Error!'));
            $result['sucess'] = false;
            $result = array('error' => __('Image Saving Error!'));
        }
        $this->getResponse()->setBody(
            $this->_objectManager->create('\Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
}
