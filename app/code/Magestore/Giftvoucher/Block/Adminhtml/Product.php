<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Block\Adminhtml;

/**
 * Class Product
 * @package Magestore\Giftvoucher\Block\Adminhtml
 */
class Product extends \Magento\Backend\Block\Widget\Container
{

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Product constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $typeId = \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE;
        $addButtonProps = [
            'id' => 'add_new_product',
            'label' => __('Add Gift Card Product'),
            'class' => 'primary add',
            'onclick' => "setLocation('" . $this->_getProductCreateUrl($typeId) . "')"
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        parent::_prepareLayout();
    }


    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {
        return $this->getUrl(
            'catalog/product/new',
            ['set' => $this->_productFactory->create()->getDefaultAttributeSetId(), 'type' => $type]
        );
    }
}
