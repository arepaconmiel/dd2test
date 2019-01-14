<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var \Magento\Indexer\Model\Indexer
     */
    private $indexer;

    public function __construct(
        \Magento\Indexer\Model\Indexer $indexer
    ) {
        $this->indexer = $indexer;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $indexer = $this->indexer
            ->load(\Amasty\Xsearch\Model\Indexer\Category\Fulltext::INDEXER_ID);
        $indexer->reindexAll();
    }
}
