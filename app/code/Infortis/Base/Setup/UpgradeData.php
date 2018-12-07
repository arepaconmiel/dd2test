<?php

namespace Infortis\Base\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ConfigInterface
     */
    protected $resourceConfig;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * Initialization
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ConfigInterface $resourceConfig,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->resourceConfig = $resourceConfig;
        $this->attributeRepository = $attributeRepository;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Attributes - for more details see #21
         */

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        // If version is lower than 2.8.0
        if (version_compare($context->getVersion(), '2.8.0') < 0)
        {
            $this->addProductLabelAttribute($eavSetup);
            $this->addImageRoleAttribute($eavSetup);
        }

        /**
         * System config operations
         */

        $setup->startSetup();

        // If version is lower than 2.5.0
        if (version_compare($context->getVersion(), '2.5.0') < 0)
        {
            $this->resourceConfig->saveConfig(
                'cms/wysiwyg/enabled', 
                \Magento\Cms\Model\Wysiwyg\Config::WYSIWYG_HIDDEN, // 'hidden' (Disabled by Default)
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, // 'default'
                \Magento\Store\Model\Store::DEFAULT_STORE_ID // 0
            );

            $this->resourceConfig->saveConfig(
                'checkout/sidebar/display', // Display Shopping Cart Sidebar
                true,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, // 'default'
                \Magento\Store\Model\Store::DEFAULT_STORE_ID // 0
            );
        }

        $setup->endSetup();
    }

    /**
     * Add product custom label attribute
     *
     * @param EavSetup
     */
    private function addProductLabelAttribute($eavSetup)
    {
        $entityTypeCode = \Magento\Catalog\Model\Product::ENTITY;
        $groupName = 'General';
        $attributeCode = 'custom_label';
        $attributeProp = [
            'type' => 'varchar',
            'label' => 'Custom Label',
            'input' => 'select',
            'required' => false,
            'sort_order' => 200,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'used_in_product_listing' => true,
            'group' => $groupName,
            // 'user_defined' => true,
            'option' => [
                'values' => [
                    'Promo',
                    'Only Today',
                    'Hot',
                    'Test Label',
                ],
            ],
        ];

        if ($this->attributeExists($entityTypeCode, $attributeCode) === false)
        {
            $eavSetup->addAttribute(
                $entityTypeCode,
                $attributeCode,
                $attributeProp
            );
        }
    }

    /**
     * Add image role attribute
     *
     * @param EavSetup
     */
    protected function addImageRoleAttribute($eavSetup)
    {
        $entityTypeCode = \Magento\Catalog\Model\Product::ENTITY;
        $groupName = 'Images';
        $attributeCode = 'alt_image';
        $attributeProp = [
            'type' => 'varchar',
            'label' => 'Alt Image',
            'input' => 'media_image',
            'frontend' => \Magento\Catalog\Model\Product\Attribute\Frontend\Image::class,
            'required' => false,
            'sort_order' => 200,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'used_in_product_listing' => true,
            // 'group' => null,
            'user_defined' => true,
        ];

        if ($this->attributeExists($entityTypeCode, $attributeCode) === false)
        {
            // Create attribute
            $eavSetup->addAttribute(
                $entityTypeCode,
                $attributeCode,
                $attributeProp
            );

            // Get all sets
            $entityTypeId = $eavSetup->getEntityTypeId($entityTypeCode);
            $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);

            // Add attribute to attribute sets
            foreach($attributeSetIds as $attributeSetId)
            {
                // Add existing attribute to group
                $groupId = (int)$eavSetup->getAttributeGroupByCode(
                    $entityTypeCode,
                    $attributeSetId,
                    'image-management',
                    'attribute_group_id'
                );
                $eavSetup->addAttributeToGroup(
                    $entityTypeCode,
                    $attributeSetId,
                    $groupId,
                    $attributeCode
                );
            }
        }
    }

    /**
     * Check if attribute already exists
     *
     * @param string
     * @param string
     * @return bool
     */
    protected function attributeExists($entityTypeCode, $attributeCode)
    {
        $exists = true;

        try
        {
            $attribute = $this->attributeRepository->get($entityTypeCode, $attributeCode);
        }
        catch(\Exception $e)
        {
            $exists = false;
        }

        return $exists;
    }
}
