<?php
namespace Qxd\Extendsalesapi\Setup; 


use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{   

    const QUOTE_ITEM_TABLE = 'quote_item';
    const QUOTE_ADDRESS_TABLE = 'quote_address';
    const ORDER_ITEM_TABLE = 'sales_order_item';

    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {

        $setup->startSetup();
        
        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::ORDER_ITEM_TABLE), 'allow_zero_price')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::ORDER_ITEM_TABLE),
                'allow_zero_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Allow Zero Price'
                ]
            );
        }

        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ITEM_TABLE), 'allow_zero_price')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ITEM_TABLE),
                'allow_zero_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Allow Zero Price'
                ]
            );
        }

        if (!$setup->getConnection()->tableColumnExists($setup->getTable(self::QUOTE_ADDRESS_TABLE), 'allow_zero_price')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(self::QUOTE_ADDRESS_TABLE),
                'allow_zero_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Allow Zero Price'
                ]
            );
        }

        $setup->endSetup();
    }
}