<?php

namespace Infortis\Base\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;

class InstallData implements InstallDataInterface
{
    protected $resourceConfig;

    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig
    ) {
        $this->resourceConfig = $resourceConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {    
    }
}
