<?php

namespace Magestore\Giftvoucher\Plugin\Minicart;
/**
 * Class Image
 * @package Magestore\Giftvoucher\Plugin\Minicart
 */
class Image
{
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helperData;

    /**
     * Image constructor.
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     */
    public function __construct(
        \Magestore\Giftvoucher\Helper\Data $helperData
    )
    {
        $this->helperData = $helperData;
    }

    /**
     * @param $subject
     * @param $proceed
     * @param $item
     * @return mixed
     */
    public function aroundGetItemData($subject, $proceed, $item)
    {
        $result = $proceed($item);
        if ($this->helperData->getStoreConfig('giftvoucher/interface_checkout/display_image_item')
            && $item->getProduct()->getTypeId() == \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE){
            if ($item->getOptionByCode('giftcard_template_image')) {
                $filename = $item->getOptionByCode('giftcard_template_image')->getValue();
            } else {
                $filename = 'default.png';
            }
            $urlImage = '/giftvoucher/template/images/' . $filename;
            $imageUrl = $this->helperData->getStoreManager()
                    ->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$urlImage;
            $result['product_image']['src'] = $imageUrl;
        }
        return $result;
    }
}