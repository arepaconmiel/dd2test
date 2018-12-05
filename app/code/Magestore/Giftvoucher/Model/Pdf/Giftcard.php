<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Pdf;

/**
 * Class Giftcard
 * @package Magestore\Giftvoucher\Model\Pdf
 */
class Giftcard extends \Magento\Framework\DataObject
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directorylist;

    /**
     * Giftcard constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directorylist
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directorylist,
        array $data = []
    ) {
    
        $this->_objectManager = $objectManager;
        $this->_directorylist = $directorylist;
        parent::__construct(
            $data
        );
    }

    /**
     * @param $giftvoucherIds
     * @return Zend_Pdf
     */
    public function getPdf($giftvoucherIds)
    {
        if ($giftvoucherIds) {
            $pdf = new \Zend_Pdf();
            $this->_setPdf($pdf);
            $style = new \Zend_Pdf_Style();
            $this->_setFontBold($style, 10);

            $giftvoucherIds = array_chunk($giftvoucherIds, 3);


            foreach ($giftvoucherIds as $giftvouchers) {
                $page = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
                $pdf->pages[] = $page;
                $this->y = 790;
                $i = 0;
                foreach ($giftvouchers as $giftvoucherId) {
                    $giftvoucher = $this->_objectManager->create('Magestore\Giftvoucher\Model\Giftvoucher')->load($giftvoucherId);
                    $gifttemplate = $this->_objectManager->create('Magestore\Giftvoucher\Model\GiftTemplate')->load($giftvoucher['giftcard_template_id']);

                    // resize the width image to 300px
                    if ($gifttemplate && $gifttemplate['design_pattern'] != 4) {
                        if ($giftvoucher->getId()) {
                            $newImgWidth = ($page->getWidth() - 300) / 2;
                            $newImgHeight = 183;

                            $images = $this->_objectManager->get('Magestore\Giftvoucher\Helper\Drawgiftcard')
                                ->getImagesInFolder($giftvoucher['gift_code']);
                            if (isset($images[0]) && is_file($images[0])) {
                                $image = \Zend_Pdf_Image::imageWithPath($images[0]);
                                $page->drawImage($image, $newImgWidth, $this->y - 183, $newImgWidth + 300, $this->y);
                            }
                        }
                        $temp = $this->y - 200;
                    } else {
                        if ($giftvoucher->getId()) {
                            $newImgWidth = ($page->getWidth() - 300) / 2;
                            $images = $this->_objectManager->get('Magestore\Giftvoucher\Helper\Drawgiftcard')
                                ->getImagesInFolder($giftvoucher['gift_code']);
                            if ($giftvoucher['message'] && $giftvoucher['message'] != '') {
                                $newImgHeight = 265;
                                if (isset($images[0]) && is_file($images[0])) {
                                    $image = \Zend_Pdf_Image::imageWithPath($images[0]);
                                    $page->drawImage($image, $newImgWidth, $this->y - 265, $newImgWidth + 300, $this->y);
                                }
                                $temp = $this->y - 280;
                            } else {
                                $newImgHeight = 219;
                                if (isset($images[0]) && is_file($images[0])) {
                                    $image = \Zend_Pdf_Image::imageWithPath($images[0]);
                                    $page->drawImage($image, $newImgWidth, $this->y - 219, $newImgWidth + 300, $this->y);
                                }
                                $temp = $this->y - 240;
                            }
                        }
                    }
                }
            }
        }
        return $pdf;
    }


    /**
     * Set PDF object
     *
     * @param Zend_Pdf|\Zend_Pdf $pdf
     * @return Magestore_Giftvoucher_Model_Pdf_Giftvoucher
     */
    protected function _setPdf(\Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath($this->_directorylist->getRoot() . '/lib/internal/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }
}
