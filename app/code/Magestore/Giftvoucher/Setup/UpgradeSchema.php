<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Module\Dir;

/**
 * Upgrade the Gift Card module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const QUOTE_TABLE = 'quote';
    const QUOTE_ITEM_TABLE = 'quote_item';
    const QUOTE_ADDRESS_TABLE = 'quote_address';
    const ORDER_TABLE = 'sales_order';
    const ORDER_ITEM_TABLE = 'sales_order_item';
    const GIFTCARD_TEMPLATE_TABLE = 'giftcard_template';

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityTypeModel;
    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_catalogAttribute;
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $_eavSetup;
    /**
     * @var \Magestore\Giftvoucher\Model\Templateoptions
     */
    protected $templateOptions;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;
    /**
     * @var ComponentRegistrarInterface
     */
    protected $componentRegistrar;
    /**
     * @var
     */
    protected $directory;
    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directoryWrite;
    /**
     * @var
     */
    protected $moduleReader;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteFactory
     */
    protected $writeFactory;
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $dataSetup;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface
     */
    protected $giftTemplateIOService;

    /**
     * UpgradeSchema constructor.
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param \Magento\Eav\Model\Entity\Type $entityType
     * @param \Magento\Eav\Model\Entity\Attribute $catalogAttribute
     * @param \Magestore\Giftvoucher\Model\Source\TemplateOptions $templateoptions
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Dir\Reader $moduleReader
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $dataSetup
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $giftTemplateIOService
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Eav\Model\Entity\Attribute $catalogAttribute,
        \Magestore\Giftvoucher\Model\Source\TemplateOptions $templateoptions,
        ComponentRegistrarInterface $componentRegistrar,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Setup\ModuleDataSetupInterface $dataSetup,
        \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $giftTemplateIOService
    )
    {
        $this->_eavSetup = $eavSetup;
        $this->_entityTypeModel = $entityType;
        $this->_catalogAttribute = $catalogAttribute;
        $this->templateOptions = $templateoptions;
        $this->componentRegistrar = $componentRegistrar;
        $this->directory = $directoryList;
        $this->moduleReader = $moduleReader;
        $this->dataSetup = $dataSetup;
        $this->directoryWrite = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->giftTemplateIOService = $giftTemplateIOService;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $entityTypeModel = $this->_entityTypeModel;
        $catalogAttributeModel = $this->_catalogAttribute;

        $installer = $this->_eavSetup;
        $setup->startSetup();


        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $setup->getConnection()->dropTable($setup->getTable('giftvoucher_sets'));
            $setup->getConnection()->addColumn(
                $setup->getTable('giftvoucher'),
                'used',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT
            );

            $table = $setup->getConnection()->newTable(
                $setup->getTable('giftvoucher_sets')
            )->addColumn(
                'set_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Set Id'
            )->addColumn(
                'set_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                45,
                ['default' => ''],
                'Set Name'
            )->addColumn(
                'sets_qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['default' => '0'],
                'Set Qty'
            )->addIndex(
                $setup->getIdxName('giftvoucher_sets', ['set_id']),
                ['set_id']
            );
            $setup->getConnection()->createTable($table);

            $setup->getConnection()->addColumn(
                $setup->getTable('giftvoucher'),
                'set_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER
            );
            $defaultData = $this->templateOptions->getDefaultData();
            $data = array(
                'group' => 'General',
                'type' => 'varchar',
                'input' => 'multiselect',
                'label' => 'Select Gift Card Templates ',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'source' => 'Magestore\Giftvoucher\Model\Source\TemplateOptions',
                'visible' => 1,
                'required' => 1,
                'user_defined' => 1,
                'used_for_price_rules' => 1,
                'position' => 2,
                'unique' => 0,
                'default' => $defaultData,
                'sort_order' => 100,
                'apply_to' => 'giftvoucher',
                'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'is_required' => 0,
                'is_configurable' => 1,
                'is_searchable' => 0,
                'is_visible_in_advanced_search' => 0,
                'is_comparable' => 0,
                'is_filterable' => 0,
                'is_filterable_in_search' => 1,
                'is_used_for_promo_rules' => 1,
                'is_html_allowed_on_front' => 0,
                'is_visible_on_front' => 0,
                'used_in_product_listing' => 1,
                'used_for_sort_by' => 0,
            );
            $installer->removeAttribute(
                $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
                'gift_template_ids'
            );
            $data['required'] = 1;
            $data['is_required'] = 1;
            $installer->addAttribute(
                $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
                'gift_template_ids',
                $data
            );
            $giftTemplateIds = $catalogAttributeModel->loadByCode('catalog_product', 'gift_template_ids');
            $giftTemplateIds->addData($data)->save();

            $data['input'] = 'select';
            $data['label'] = 'Select The Gift Code Sets';
            $data['source'] = 'Magestore\Giftvoucher\Model\Source\GiftCodeSetsOptions';
            $data['sort_order'] = 110;
            $data['default'] = '';
            $data['required'] = 0;

            $installer->addAttribute(
                $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
                'gift_code_sets',
                $data
            );
            $giftCodeSets = $catalogAttributeModel->loadByCode('catalog_product', 'gift_code_sets');
            $giftCodeSets->addData($data)->save();


            $data['label'] = 'Select Gift Card Type';
            $data['source'] = 'Magestore\Giftvoucher\Model\Source\GiftCardTypeOptions';
            $data['sort_order'] = 14;
            $data['default'] = '';
            $data['is_required'] = 1;
            $data['required'] = 1;

            $installer->addAttribute(
                $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
                'gift_card_type',
                $data
            );
            $giftCardType = $catalogAttributeModel->loadByCode('catalog_product', 'gift_card_type');
            $giftCardType->addData($data)->save();
            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            /* add created_at & updated_at to giftcard_template */
            $setup->getConnection()->addColumn(
                $setup->getTable('giftcard_template'),
                'created_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                    'comment' => 'Created At'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('giftcard_template'),
                'updated_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                    'comment' => 'Updated At'
                ]
            );

            $this->upgradeAttributes();

            $this->dataSetup->deleteTableRow(
                'eav_entity_attribute',
                'attribute_id',
                $installer->getAttributeId('catalog_product', 'gift_code_sets'),
                'attribute_set_id',
                $installer->getAttributeSetId('catalog_product', 'Default')
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_gift_codes',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_gift_codes_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes Discount'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_gift_codes_max_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes Max Discount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_base_shipping_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Base Shipping Hidden Tax Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_shipping_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Shipping Hidden Tax Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'base_giftvoucher_discount_for_shipping',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Base Gift Voucher Discount For Shipping'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_discount_for_shipping',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Discount For Shipping'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_base_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Base Hidden Tax Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'giftvoucher_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Hidden Tax Amount'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'base_gift_voucher_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Base Gift Voucher Discount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'gift_voucher_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Discount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'codes_base_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Codes Base Discount String'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'codes_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Codes Discount String'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'base_gift_voucher_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Base Gift Voucher Discount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'gift_voucher_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Discount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'giftvoucher_base_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Base Hidden Tax Amount'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_item'),
                'giftvoucher_hidden_tax_amount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'default' => 0,
                    'length' => '12,4',
                    'comment' => 'Gift Voucher Hidden Tax Amount'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'gift_voucher_gift_codes',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'gift_voucher_gift_codes_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Gift Codes Discount'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'codes_base_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Codes Base Discount String'
                )
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'codes_discount',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'Gift Voucher Codes Discount String'
                )
            );
            $setup->getConnection()->modifyColumn(
                $setup->getTable('giftvoucher_history'),
                'created_at',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                )
            );

            $setup->getConnection()->modifyColumn(
                $setup->getTable('giftvoucher_customer_voucher'),
                'added_date',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                )
            );

            $setup->getConnection()->modifyColumn(
                $setup->getTable('giftcard_template'),
                'design_pattern',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface::DEFAULT_TEMPLATE_ID
                )
            );

            $magentoRoot = $this->directory->getRoot();
            $giftVoucherViewDir = $this->moduleReader->getModuleDir(Dir::MODULE_VIEW_DIR, 'Magestore_Giftvoucher');
            $giftVoucherViewDirRelative = str_replace($magentoRoot, '', $giftVoucherViewDir);
            $fromPath = $giftVoucherViewDirRelative . '/frontend/web/images/template/images/default.png';
            $this->directoryWrite->copyFile(
                $fromPath,
                '/pub/media/giftvoucher/template/images/default.png'
            );
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $this->updateRebuiltDiscount($setup);
            $this->updateGiftcardTemplate($setup);
        }
    }

    /**
     *
     */
    public function upgradeAttributes()
    {
        $this->_eavSetup->updateAttribute('catalog_product', 'gift_code_sets', 'source_model', 'Magestore\Giftvoucher\Model\Source\GiftCodeSetsOptions');
        $this->_eavSetup->updateAttribute('catalog_product', 'gift_card_type', 'source_model', 'Magestore\Giftvoucher\Model\Source\GiftCardTypeOptions');
        $this->_eavSetup->updateAttribute('catalog_product', 'gift_type', 'source_model', 'Magestore\Giftvoucher\Model\Source\GiftType');
        $this->_eavSetup->updateAttribute('catalog_product', 'gift_price_type', 'source_model', 'Magestore\Giftvoucher\Model\Source\GiftPriceType');
        $this->_eavSetup->updateAttribute('catalog_product', 'gift_template_ids', 'source_model', 'Magestore\Giftvoucher\Model\Source\TemplateOptions');
    }

    /**
     * Update table
     *
     * @param SchemaSetupInterface $setup
     */
    public function updateRebuiltDiscount(SchemaSetupInterface $setup)
    {

        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'magestore_base_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_TABLE),
                'magestore_base_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Base Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_TABLE), 'magestore_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_TABLE),
                'magestore_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Discount'
                ]
            );
        }

        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'magestore_base_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'magestore_base_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Base Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'magestore_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'magestore_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'base_gift_voucher_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'base_gift_voucher_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Base Gift Card Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'gift_voucher_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'gift_voucher_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Gift Card Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'magestore_base_discount_for_shipping')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'magestore_base_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Base Discount For Shipping'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'magestore_discount_for_shipping')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'magestore_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore  Discount For Shipping'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'base_giftvoucher_discount_for_shipping')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'base_giftvoucher_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Base Giftvoucher Discount For Shipping'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'giftvoucher_discount_for_shipping')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'giftvoucher_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Giftvoucher Discount For Shipping'
                ]
            );
        }

        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ITEM_TABLE), 'magestore_base_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ITEM_TABLE),
                'magestore_base_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Base Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ITEM_TABLE), 'magestore_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ITEM_TABLE),
                'magestore_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Discount'
                ]
            );
        }

        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'magestore_base_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::ORDER_TABLE),
                'magestore_base_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Base Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'magestore_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::ORDER_TABLE),
                'magestore_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'magestore_base_discount_for_shipping')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::ORDER_TABLE),
                'magestore_base_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Base Discount For Shipping'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_TABLE), 'magestore_discount_for_shipping')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::ORDER_TABLE),
                'magestore_discount_for_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Discount For Shipping'
                ]
            );
        }

        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_ITEM_TABLE), 'magestore_base_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::ORDER_ITEM_TABLE),
                'magestore_base_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Base Discount'
                ]
            );
        }
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_ITEM_TABLE), 'magestore_discount')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::ORDER_ITEM_TABLE),
                'magestore_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Magestore Discount'
                ]
            );
        }
    }

    /**
     * Update gift card template affer modify design_pattern column
     *
     * @param SchemaSetupInterface $setup
     */
    public function updateGiftcardTemplate(SchemaSetupInterface $setup)
    {
        $templates = $this->giftTemplateIOService->getAvailableTemplates();
        if (empty($templates)) {
            return $this;
        }
        $count = count($templates);
        $index = 0;
        $updateValue = "CASE ";
        foreach ($templates as $template) {
            $updateValue .= "WHEN MOD(giftcard_template_id, $count) = $index THEN '$templates[$index]' ";
            $index++;
        }
        $updateValue .= "ELSE design_pattern END";
        $setup->getConnection()->update(
            $setup->getTable(self::GIFTCARD_TEMPLATE_TABLE),
            ['design_pattern' => new \Zend_Db_Expr($updateValue)],
            ['design_pattern NOT IN (?)' => $templates]
        );
    }
}
