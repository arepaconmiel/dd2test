<?php
/**
 * Page footer block
 */

namespace Qxd\Diverstheme\Block\Html;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Framework\View\Element\Template\Context;

class Footer extends \Infortis\Base\Block\Html\Footer
{
    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $helperData, $data);
    }

    
    public function getCmsBlock ($identifier) {
        $result_block = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($identifier);
        return $result_block->toHtml();
    }

    public function getBaseUrl(){
        return $this->_storeManager->getStore()->getBaseUrl();
    }

}
