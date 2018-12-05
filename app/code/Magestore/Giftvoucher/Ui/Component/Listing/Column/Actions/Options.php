<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\Component\Listing\Column\Actions;

use Magento\Framework\Data\OptionSourceInterface;
use Magestore\Giftvoucher\Model\Actions;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var Actions
     */
    protected $actions;

    /**
     * Constructor
     *
     * @param Actions $actions
     * @internal param Status $status
     */
    public function __construct(Actions $actions)
    {
        $this->actions = $actions;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->actions->getOptions();
        }
        return $this->options;
    }
}
