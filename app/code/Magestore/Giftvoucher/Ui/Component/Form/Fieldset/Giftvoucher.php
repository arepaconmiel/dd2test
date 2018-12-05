<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\Component\Form\Fieldset;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\App\RequestInterface;

/**
 * Class Websites Fieldset
 */
class Giftvoucher extends Fieldset
{
    /**
     * Store manager
     *
     * @var ProductModel
     */
    protected $product;

    protected $request;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param ProductModel $product
     * @param RequestInterface $request
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        ProductModel $product,
        RequestInterface $request,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->product = $product;
        $this->request = $request;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $this->_data['config']['componentDisabled'] = true;

        if ($this->request->getParam('type') == 'giftvoucher') {
            $this->_data['config']['componentDisabled'] = false;
        }

        if ($this->request->getParam('id')) {
            $productModel = $this->product->load($this->request->getParam('id'));
            if ($productModel->getTypeId() == 'giftvoucher') {
                $this->_data['config']['componentDisabled'] = false;
            }
        }
    }
}
