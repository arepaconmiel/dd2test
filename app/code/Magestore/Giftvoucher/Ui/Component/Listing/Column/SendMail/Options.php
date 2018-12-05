<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\Component\Listing\Column\SendMail;

use Magento\Framework\Data\OptionSourceInterface;
use Magestore\Giftvoucher\Model\Status;

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
     * @var Status
     */
    protected $status;

    /**
     * Constructor
     *
     * @param Status $status
     */
    public function __construct(Status $status)
    {
        $this->status = $status;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->status->getEmailOptions();
        }
        return $this->options;
    }
}
