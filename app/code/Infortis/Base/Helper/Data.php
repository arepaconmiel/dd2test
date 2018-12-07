<?php

namespace Infortis\Base\Helper;

use Infortis\Infortis\Helper\Image;
use Infortis\Base\Helper\AltImage;
use Infortis\Base\Model\System\Config\Source\Category\Altimagecolumn;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Section name of module configuration
     */
    const CONFIG_SECTION_SETTINGS   = 'theme_settings';
    const CONFIG_SECTION_DESIGN     = 'theme_design';
    const CONFIG_SECTION_LAYOUT     = 'theme_layout';

    /**
     * Is alt image helper initialized
     *
     * @var bool
     */
    protected $altImageHelperInitialized = false;

    /**
     * Alternative image helper
     *
     * @var AltImage
     */
    protected $helperAltImage;
    
    /**
     * Initialization
     */
    public function __construct(
        Context $context,
        AltImage $helperAltImage
    ) {
        $this->helperAltImage = $helperAltImage;

        parent::__construct($context);
    }

    //
    //  Get section of options from the configuration
    //  -----------------------------------------------------------------------

    /**
     * Get selected section from the configuration
     *
     * @return array
     */
    public function getCfgSection($section, $storeCode = NULL)
    {
        return $this->scopeConfig->getValue($section, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * Get design section from the configuration
     *
     * @return array
     */
    public function getCfgSectionDesign($storeCode = NULL)
    {
        return $this->getCfgSection(self::CONFIG_SECTION_DESIGN, $storeCode);
    }

    //
    //  Get group of options from the configuration
    //  -----------------------------------------------------------------------

    /**
     * Get selected group from main configuration
     *
     * @return array
     */
    public function getCfgGroup($group, $storeCode = NULL)
    {
        return $this->scopeConfig->getValue(self::CONFIG_SECTION_SETTINGS . '/' . $group, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    //
    //  Get single option from the configuration
    //  -----------------------------------------------------------------------
    
    /**
     * Get single option from main configuration
     *
     * @return string
     */
    public function getCfg($optionString, $storeCode = NULL)
    {
        return $this->scopeConfig->getValue(self::CONFIG_SECTION_SETTINGS . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
    }
    
    /**
     * Get single option from design configuration
     *
     * @return string
     */
    public function getCfgDesign($optionString, $storeCode = NULL)
    {
        return $this->scopeConfig->getValue(self::CONFIG_SECTION_DESIGN . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
    }
    
    /**
     * Get single option from layout configuration
     *
     * @return string
     */
    public function getCfgLayout($optionString, $storeCode = NULL)
    {
        return $this->scopeConfig->getValue(self::CONFIG_SECTION_LAYOUT . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);

    }

    //
    //  Other helper methods
    //  -----------------------------------------------------------------------

    /**
     * Get maximum width of the page.
     * Returns:
     * - selected predefined width
     * - custom width, if custom width was selected
     * - 0, if full width was selected
     *
     * @return int
     */
    public function getMaxWidth($storeCode = null)
    {
        $w = $this->getCfgLayout('responsive/max_width', $storeCode);
        if ($w === 'custom')
        {
            return intval($this->getCfgLayout('responsive/max_width_custom', $storeCode));
        }
        elseif ($w === 'full')
        {
            return 0;
        }
        else
        {
            return intval($w);
        }
    }
    
    /**
     * Get custom page width from the config.
     * Value of custom width is returned only if predefined width was NOT selected.
     *
     * @return int|null
     */
    public function getCustomWidth($storeCode = null)
    {
        $w = $this->getCfgLayout('responsive/max_width', $storeCode);
        if ($w === 'custom')
        {
            return intval($this->getCfgLayout('responsive/max_width_custom', $storeCode));
        }
        else
        {
            return null;
        }
    }

    /**
     * Get product's alternative image HTML.
     * Important: first usage initializes alternative image helper.
     *
     * @param Product   $product        Product
     * @param string    $imageTypeId    Image version
     * @param int       $w              Image width
     * @param int       $h              Image height
     * @return string
     */
    public function getAltImgHtml($product, $imageTypeId = 'product_base_image', $w = null, $h = null)
    {
        if ($this->altImageHelperInitialized === false)
        {
            $this->helperAltImage->init(
                $this->getCfg('category/alt_image_column'),
                $this->getCfg('category/alt_image_column_value'),
                $this->getCfg('category/alt_image_role_attribute')
            );
            $this->altImageHelperInitialized = true;
        }

        return $this->helperAltImage->getAltImgHtml($product, $imageTypeId, $w, $h);
    }

    /**
     * Get HTML of all child static blocks with given ID and merge them in columns
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block Parent block object
     * @param bool $auto If true, automatically split blocks into even columns (5 columns not possible because grid has 12 columns)
     * @param string $staticBlockId Identifier of blocks followed by a number
     * @param int $max number
     * @return string
     */
    public function getFormattedBlocks($block, $auto = true, $staticBlockId = 'block_footer_column', $max = 6)
    {
        //Get HTML output of 6 static blocks with ID $staticBlockId<X>, where <X> is a number from 1 to 6
        $maxLoops = $max + 1;
        $colCount = 0; //Number of existing and active static blocks
        $colHtml = []; //Static blocks content
        $html = ''; //Final HTML output
        for ($i = 1; $i < $maxLoops; $i++)
        {
            if ($tmp = $block->getChildHtml($staticBlockId . $i))
            {
                $colHtml[] = $tmp;
                $colCount++;
            }
        }
        
        if ($colHtml)
        {
            $gridClass = '';
            $gridClassBase = 'grid12-';
            
            //Get grid unit class.
            if ($auto)
            {
                //Grid units per static block
                $n = (int) (12 / $colCount);
                $gridClass = $gridClassBase . $n;
            }
            else
            {
                $gridClass = $gridClassBase . '2';
            }
                
            for ($i = 0; $i < $colCount; $i++)
            {
                $classString = $gridClass;
                $html .= '<div class="'. $classString .'">';
                $html .= '<div class="std">'. $colHtml[$i] .'</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }
}
