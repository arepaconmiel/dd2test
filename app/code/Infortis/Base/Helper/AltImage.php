<?php

namespace Infortis\Base\Helper;

use Infortis\Infortis\Helper\Image;
use Infortis\Base\Model\System\Config\Source\Category\Altimagecolumn;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

class AltImage extends AbstractHelper
{
    /**
     * Defines the property by which we select the alternative image for each product
     *
     * @var string
     */
    protected $property;

    /**
     * Value of the property. It is user's input.
     *
     * @var string|int
     */
    protected $value;

    /**
     * Code of the image role attribute (which is a product attribute). It is user's input.
     *
     * @var string
     */
    protected $roleAttributeCode;

    /**
     * Image helper
     *
     * @var Image
     */
    protected $helperImage;

    /**
     * To test if attributes exist
     *
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;
    
    public function __construct(
        Context $context,
        Image $helperImage,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->helperImage = $helperImage;
        $this->attributeRepository = $attributeRepository;

        parent::__construct($context);
    }

    /**
     * Initialize properties
     *
     * @param string
     * @param string
     * @param string
     */
    public function init($property, $value, $roleAttribute)
    {
        $this->property = $property;
        $this->value = $value;
        $this->roleAttributeCode = $roleAttribute;

        if ($this->property === Altimagecolumn::PROPERTY_ROLE) // 'role'
        {
            // If we choose image by role, check if the role attribute exists
            try
            {
                $testAttribute = $this->attributeRepository->get(ProductAttributeInterface::ENTITY_TYPE_CODE, $this->roleAttributeCode);
            }
            catch(\Exception $e)
            {
                // If the role attribute does not exist, set the role attribute code to null
                $this->roleAttributeCode = null;
            }
        }
        elseif ($this->property === Altimagecolumn::PROPERTY_POSITION) // 'position'
        {
            // If we choose image by position, convert value to int
            $this->value = intval($this->value);
        }
    }

    /**
     * Get product's alternative image HTML
     *
     * @param Product   $product        Product
     * @param string    $imageTypeId    Image version
     * @param int       $w              Image width
     * @param int       $h              Image height
     * @return string
     */
    public function getAltImgHtml($product, $imageTypeId = 'product_base_image', $w = null, $h = null)
    {
        $file = null; // Image name and path

        // Load gallery of images
        $product->load('media_gallery');
        if ($gallery = $product->getMediaGalleryImages())
        {
            // Get image based on selected property
            if ($this->property === Altimagecolumn::PROPERTY_ROLE) // 'role'
            {
                if (!empty($this->roleAttributeCode))
                {
                    $file = $product->getResource()
                        ->getAttribute($this->roleAttributeCode)
                        ->getFrontend()
                        ->getValue($product);
                }
            }
            elseif ($this->property === Altimagecolumn::PROPERTY_POSITION) // 'position'
            {
                // $this->value defines index of the image (inside the gallery) which should be used as the alternative image.
                // Indexing starts from 1, i.e. 1 means the first image in the gallery.
                $i = 1;
                $altImage = null;

                foreach ($gallery as $img) // $gallery->getItems()
                {
                    if ($i++ === $this->value)
                    {
                        $altImage = $img; // If it's the correct index - save the image and break
                        break;
                    }
                }

                // Get image name and path
                if ($altImage)
                {
                    $file = $altImage->getFile();
                }
            }
            else // 'label'
            {
                $altImage = $gallery->getItemByColumnValue($this->property, $this->value);

                // Get image name and path
                if ($altImage)
                {
                    $file = $altImage->getFile();
                }
            }
        }

        // Get image url and generate HTML
        if ($file)
        {
            return 
                '<img class="alt-img" src="' 
                . $this->helperImage->getImageUrlExtended($product, $imageTypeId, $w, $h, $file)
                . '" alt="' . $product->getName() . '" />';
        }

        return '';
    }
}
