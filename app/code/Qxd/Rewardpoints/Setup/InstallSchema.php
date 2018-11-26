<?php
namespace Qxd\Rewardpoints\Setup; 


use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
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
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
        
        $sql = "CREATE TABLE IF NOT EXISTS `rewards_customer_index_points` (
            `id`                                    INT(11) NOT NULL AUTO_INCREMENT,
            `customer_id`                           INT(11) unsigned NOT NULL,
            `customer_points_usable`                INT(11) NOT NULL,
            `customer_points_pending_event`         INT(11) NOT NULL,
            `customer_points_pending_time`          INT(11) NOT NULL,
            `customer_points_pending_approval`      INT(11) NOT NULL,
            `customer_points_active`                INT(11) NOT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (customer_id) REFERENCES customer_entity(entity_id)
        )
        ENGINE = InnoDB
        DEFAULT CHARSET = utf8;";
        $connection->query($sql);

        $sql = "ALTER TABLE sales_order ADD reward_points int DEFAULT 0;";
        $connection->query($sql);

        $sql = "ALTER TABLE customer_entity ADD reward_points int DEFAULT 0;";
        $connection->query($sql);

        $installer->endSetup();
    }
}