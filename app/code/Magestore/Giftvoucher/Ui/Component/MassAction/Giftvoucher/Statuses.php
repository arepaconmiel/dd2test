<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\Component\MassAction\Giftvoucher;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;
use Magestore\Giftvoucher\Model\Status;

/**
 * Gift code statuses
 */
class Statuses implements JsonSerializable
{

    /**
     * @var array
     */
    protected $options;
    
    /**
     * @var Status
     */
    protected $status;
    
    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;
    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $urlPath;
    
    /**
     * Param name for subactions
     *
     * @var string
     */
    protected $paramName;
    
    /**
     * Additional params for subactions
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * Statuses constructor.
     * @param Status $status
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Status $status,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->status = $status;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $options = $this->status->toOptionArray();
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => 'status_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];
        
                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }
        
                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }
        
            $this->options = array_values($this->options);
        }
        
        return $this->options;
    }
    
    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
