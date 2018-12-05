<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Service\GiftTemplate;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\DriverPool;
use Magestore\Giftvoucher\Api\Data\GiftTemplateInterface;

/**
 * Class IOService
 * @package Magestore\Giftvoucher\Service\GiftTemplate
 */
class IOService implements \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface
{
    /**
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    protected $fileReaderFactory;
    
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;
    
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleDirReader;
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $dirReaderFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface
     */
    protected $giftTemplateRepository;

    /**
     *
     * @param \Magento\Framework\Filesystem\File\ReadFactory $fileReaderFactory
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\Module\Dir\Reader $moduleDirReader
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $dirReaderFactory
     * @param \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository
     */
    public function __construct(
        \Magento\Framework\Filesystem\File\ReadFactory $fileReaderFactory,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
        \Magento\Framework\Filesystem\Directory\ReadFactory $dirReaderFactory,
        \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository
    ) {
    
        $this->fileReaderFactory = $fileReaderFactory;
        $this->fileSystem = $fileSystem;
        $this->moduleDirReader = $moduleDirReader;
        $this->dirReaderFactory = $dirReaderFactory;
        $this->giftTemplateRepository = $giftTemplateRepository;
    }
    
    /**
     * Get content of template file
     *
     * @param string $designPattern
     * @return string
     */
    public function getTemplateContent($designPattern)
    {
        $templateFile = $this->getTemplateFileFromDesign($designPattern);
        $templatePath = $this->moduleDirReader->getModuleDir('view', 'Magestore_Giftvoucher');
        $templatePath .= '/base/web/template/gift-template/' .$templateFile .'.html';
        return $this->fileReaderFactory->create($templatePath, DriverPool::FILE)->readAll();
    }
    
    /**
     * Get list of available gift card templates
     *
     * @return array
     */
    public function getAvailableTemplates()
    {
        $templatePath = $this->moduleDirReader->getModuleDir('view', 'Magestore_Giftvoucher');
        $templatePath .= '/base/web/template/gift-template';
        try {
            $templates = $this->dirReaderFactory->create($templatePath)->readRecursively();
            foreach ($templates as &$template) {
                $template = str_replace('.html', '', $template);
            }
        } catch (\Exception $e) {
            $templates = [];
        }
        if (!count($templates)) {
            $templates = [GiftTemplateInterface::DEFAULT_TEMPLATE_ID];
        }
        return $templates;
    }
    
    /**
     * Get template file system
     *
     * @param int $templateId
     * @return string
     */
    public function getTemplateFile($templateId)
    {
        $giftTemplate = $this->giftTemplateRepository->getById($templateId);
        $templateFile = $this->getTemplateFileFromDesign($giftTemplate->getDesignPattern());
        return 'Magestore_Giftvoucher/gift-template/' . $templateFile;
    }
    
    /**
     * Get template file from design pattern
     *
     * @param string $designPattern
     * @return string
     */
    public function getTemplateFileFromDesign($designPattern)
    {
        $templateFile = GiftTemplateInterface::DEFAULT_TEMPLATE_ID;
        $availabelTemplates = $this->getAvailableTemplates();
        if (in_array($designPattern, $availabelTemplates)) {
            $templateFile = $designPattern;
        }
        return $templateFile;
    }
}
