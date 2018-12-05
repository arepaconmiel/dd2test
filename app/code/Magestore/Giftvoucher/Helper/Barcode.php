<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Helper;

/**
 * Class Barcode
 * @package Magestore\Giftvoucher\Helper
 */
class Barcode
{
    /* default barcode config value */
    const SYMBOLOGY = 'code128';
    const FONT_SIZE = 16;
    const HEIGHT = 0;
    const WIDTH = 0;
    const IMAGE_TYPE = 'png';
    const DRAW_TEXT = false;
    
    /**
     * Generate source of barcode image
     *
     * @param string $barcodeString
     * @param array $config
     *
     * @return string
     */
    public function getBarcodeSource($barcodeString, $config = [])
    {
        $symbology = isset($config['symbology']) ? $config['symbology'] : self::SYMBOLOGY;
        $fontSize = isset($config['font_size']) ? $config['font_size'] : self::FONT_SIZE;
        $height = isset($config['height']) ? $config['height'] : self::HEIGHT;
        $width = isset($config['width']) ? $config['width'] : self::WIDTH;
        $imageType = isset($config['image_type']) ? $config['image_type'] : self::IMAGE_TYPE;
        $fontSize = isset($config['font_size']) ? $config['font_size'] : self::FONT_SIZE;
        $drawText = isset($config['drawText']) ? $config['drawText'] : self::DRAW_TEXT;
        
        $barcodeOptions = [
            'text' => $barcodeString,
            'fontSize' => $fontSize,
            'drawText' => $drawText
        ];
        $rendererOptions = [
            'width' => $width,
            'height' => $height,
            'imageType' => $imageType
        ];

        $source = \Zend_Barcode::factory(
            $symbology, 'image', $barcodeOptions, $rendererOptions
        );
        
        ob_start();
        imagepng($source->draw());
        $barcode = ob_get_contents();
        ob_end_clean();
        
        return base64_encode($barcode);
    }
    
    /**
     * Get barcode source in png image format
     *
     * @param string $barcodeString
     * @param array $config
     * @return string
     */
    public function getBarcodeImageSource($barcodeString, $config = [])
    {
        return 'data:image/png;base64,'. $this->getBarcodeSource($barcodeString, $config);
    }
}
