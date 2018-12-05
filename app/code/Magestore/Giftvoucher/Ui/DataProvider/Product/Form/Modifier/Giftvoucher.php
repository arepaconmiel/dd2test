<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\App\RequestInterface;

/**
 * Class adds Downloadable collapsible panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Giftvoucher extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var array
     */
    protected $meta = [];

    protected $request;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param RequestInterface $request
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        RequestInterface $request
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
//        $meta['giftvoucher_fieldset'] = [
//            'arguments' => [
//                'data' => [
//                    'config' => [
//                        'label' => __('Gift Price Setting'),
//                        'sortOrder' => 1000,
//                        'collapsible' => true
//                    ]
//                ]
//            ]
//        ];

        return $meta;
    }
}
