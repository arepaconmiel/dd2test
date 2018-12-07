<?php

namespace Infortis\Base\Model\System\Config\Source\Design\Font\Family;

use Infortis\Base\Helper\Data as HelperData;
use Infortis\Base\Helper\Fonts as HelperFonts;
use Magento\Framework\Module\Dir;

class DefinedFonts extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Fonts helper
     *
     * @var HelperFonts
     */
    protected $helperFonts;

    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Initialization
     */
    public function __construct(
        HelperFonts $helperFonts,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helperFonts = $helperFonts;
        $this->request = $request;
    }

    public function toOptionArray()
    {
        if (!$this->options)
        {
            $this->options = [];
            $this->options[] = ['value' => '', 'label' => __('-')];
            $storeId = (int) $this->request->getParam('store');
            $definedFontFamilies = $this->helperFonts->getDefinedFontFamilies($storeId);

            // If no fonts defined yet
            if (empty($definedFontFamilies))
            {
                // Additional option with description for user. Value can't be empty string, otherwise it would be selected by default.
                // To retrieve default values of system config options - see #20.
                $this->options[] = ['value' => ' ', 'label' => __('- No fonts defined (go to "Defined Fonts", add fonts and save config)')];
            }

            foreach ($definedFontFamilies as $fontFamilyString)
            {
                // Remove additional info in parentheses
                $fontName = $this->helperFonts->removeAdditionalFontInfo($fontFamilyString);

                $this->options[] = ['value' => $fontName, 'label' => $fontFamilyString];
            }
        }

        return $this->options;
    }
}
