<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer\Adminhtml;

/**
 * Class ProductSaveAfterObserver
 * @package Magestore\Giftvoucher\Observer\Adminhtml
 */
class ProductSaveAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $productFactory;

    protected $request;

    /**
     * ProductSaveAfterObserver constructor.
     * @param \Magestore\Giftvoucher\Model\ProductFactory $productFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magestore\Giftvoucher\Model\ProductFactory $productFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->productFactory = $productFactory;
        $this->request = $request;
    }

    /**
     * Set Gift Card conditions when product is saved
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() != 'giftvoucher' || !$product->getId()) {
            return;
        }
        $model = $this->productFactory->create();
        if ($model->getIsSavedConditions()) {
            return;
        }
        $model->setIsSavedConditions(true);
        if (!$model->getId()) {
            $model->loadByProduct($product);
        }
        
        $data = $this->request->getPostValue();
        
        if (isset($data['rule'])) {
            $rules = $data['rule'];
            if (isset($rules['conditions'])) {
                $data['conditions'] = $rules['conditions'];
            }
            if (isset($rules['actions'])) {
                $data['actions'] = $rules['actions'];
            }
            unset($data['rule']);
        }
        $model->loadPost($data);
        $model->setProductId($product->getId());
        try {
            $model->save();
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            throw new \Magento\Framework\Exception\LocalizedException(__($exception->getMessage()));
        }
    }
}
