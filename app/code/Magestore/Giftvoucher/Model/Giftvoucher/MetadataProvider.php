<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Giftvoucher;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MetadataProvider extends \Magento\Ui\Model\Export\MetadataProvider
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;
    
    /**
     * @param Filter $filter
     * @param TimezoneInterface $localeDate
     * @param ResolverInterface $localeResolver
     * @param DataObjectFactory $dataObjectFactory
     * @param string $dateFormat
     * @param array $data
     */
    public function __construct(
        Filter $filter,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver,
        DataObjectFactory $dataObjectFactory,
        $dateFormat = 'M j, Y H:i:s A',
        array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($filter, $localeDate, $localeResolver, $dateFormat, $data);
    }

    /**
     * (non-PHPdoc)
     * @see \Magento\Ui\Model\Export\MetadataProvider::getHeaders()
     * @param UiComponentInterface $component
     * @return array|\string[]
     */
    public function getHeaders(UiComponentInterface $component)
    {
        $row = [];
        foreach ($this->getColumns($component) as $column) {
            $row[] = $column->getName();
        }
        return $row;
    }

    /**
     * (non-PHPdoc)
     * @see \Magento\Ui\Model\Export\MetadataProvider::getColumns()
     * @param UiComponentInterface $component
     * @return UiComponentInterface[]
     */
    protected function getColumns(UiComponentInterface $component)
    {
        if (!isset($this->columns[$component->getName()])) {
            $columns = $this->getColumnsComponent($component);
            foreach ($columns->getChildComponents() as $column) {
                if ($column->getData('config/label') && $column->getData('config/dataType') !== 'actions') {
                    $this->columns[$component->getName()][$column->getName()] = $column;
                }
            }
            // Add more columns to export
            foreach ([
                'currency',
                'customer_id',
                'customer_email',
                'recipient_email',
                'recipient_address',
                'message',
            ] as $column) {
                $this->columns[$component->getName()][$column] = $this->dataObjectFactory->create([
                    'name' => $column,
                ]);
            }
        }
        return $this->columns[$component->getName()];
    }
}
