<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Ui\Component\Listing\Column\GiftTemplate\Action;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class ProductActions
 */
class Preview extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;


    /**
     * Preview constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['preview'] = [
                    'href' => "javascript:void(window.open('".
                    $this->urlBuilder->getUrl('giftvoucheradmin/giftTemplate/preview', ['id' => $item['giftcard_template_id']])
                    . "', 'newWindow', 'width=680,height=860,left=600,resizable=yes,scrollbars=yes'));",
                    'label' => __('Preview'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
